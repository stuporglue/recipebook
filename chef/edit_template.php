<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if($_SESSION['loggedin'] !== TRUE){
    header("Location:index.php");
    exit();
}

require_once('../lib/db.inc');
require_once('../lib/template.php');

$fieldInfo = Array(
    'units' => Array(
        Array( 'id' => 'name', 'label' => 'Name'),
        Array( 'id' => 'plural','label' => 'Plural'),
        Array( 'id' => 'base_unit', 'label' => 'Base Unit'),
        Array( 'id' => 'base_count', 'label' => 'Base Unit Count', 'type' => 'numeric')
    ),
    'ingredients' => Array(
        Array( 'id' => 'name', 'label' => 'Name'),
        Array( 'id' => 'plural','label' => 'Plural'),
    ),
    'categories' => Array(
        Array( 'id' => 'name', 'label' => 'Name'),
        Array( 'id' => 'label','label' => 'Label'),
    ),
    'recipes' => Array(
        Array( 'id' => 'name', 'label' => 'Name', 'css-class' => 'widercol', 'header-css-class' => 'widercol', 'formatter' => 'recipe-link'),
        Array( 'id' => 'category', 'label' => 'Category'),
        Array( 'id' => 'tags', 'label' => 'Tags', 'formatter' => 'tags'),
        Array( 'id' => 'display_name', 'label' => 'Display Name'),

    ),
);


function editPage($table,$fields){
    printHeader("Manage " . ucfirst($table));
    printEditorInterface($table,$fields);
    printFooter();
}

function printEditorInterface($type,$fields){
    print "
    <div class='jumbotron'>
    <h1>Manage " . ucfirst($type) . "</h1>
    </div>
    <div>
        <table id='grid-data' data-type='$type' class='table table-condensed table-hover table-striped'>
            <thead>
                <tr>
                    <th data-css='slimmer' data-column-id='commands' data-formatter='commands' data-sortable='false'>Edit</th>
                    <th data-column-id='id' data-css-class='idcol' data-header-css-class='idcol' data-order='asc' data-identifier='true' data-type='numeric'>ID</th>
                    ";
    foreach($fields as $field){
        print "<th data-column-id='{$field['id']}'";
        foreach($field as $k => $v){
            if($k !== 'id' && $k !== 'label'){
                print " data-$k='$v'";
            }
        }
        print ">{$field['label']}</th>";
    }
                    print "</tr>
            </thead>
        </table>
        </div>";
}


function processPost($table){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $kvPairs = updateOrInserts($_POST);

        if(isset($_POST['action']) && $_POST['action'] == 'delete'){
            $action = "DELETE FROM $table WHERE id=" . pg_escape_literal($_POST['id']);
        }else{
            $action = updateOrInserts($_POST);
        }
        $res = pg_query($action);

        header("Content-type: application/json");
        if($res){
            $row = pg_fetch_assoc($res);
            print json_encode($row);
        }else{
            http_response_code(500);
            print json_encode(Array("success" => FALSE,"msg" => pg_last_error()));
        }

        exit();
    }
}

function getStandard($table,$fields){
    $field = Array();
    foreach($fields as $field){
        $selectfields[] = "t.{$field['id']}";
    }

    $q = "SELECT t.id," . implode(',',$selectfields) . " FROM $table t";

    $where = "";
    if(isset($_REQUEST['searchPhrase']) && $_REQUEST['searchPhrase'] != ''){
        $phrase = " ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%');
        $where = implode("OR $phrase",$selectfields) . $phrase;
    }

    return wrapUp($q,$where);
}

function getPaginatedUnits(){
    $q = "SELECT 
        u.id,
        u.name,
        u.plural,
        b.name AS base_unit,
        u.base_count
        FROM 
        units u LEFT JOIN units b ON u.base_unit = b.id ";

    $where = "";
    if(isset($_REQUEST['searchPhrase']) && $_REQUEST['searchPhrase'] != ''){
        $where = " WHERE 
            u.name ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
            u.plural ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " ";
    }

    return wrapUp($q,$where);
}

function getPaginatedRecipes(){
    $q = "SELECT
        r.id,
        r.name,
        r.about,
        c.label AS category,
        r.quick,
        r.display_name,
        r.hide,
        r.date_added,
        r.favorite
        FROM
        recipes r LEFT JOIN categories c ON r.category = c.id ";

    $where = "";
    if(isset($_REQUEST['searchPhrase']) && $_REQUEST['searchPhrase'] != ''){
    $where = " WHERE 
        r.name ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
        r.about ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
        r.instructions ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
        c.name ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
        c.label ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
        r.display_name ILIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') ;
    }

    if($_REQUEST['searchPhrase'] == 'favorite'){
        $where .= " OR r.favorite ";
    }
    if($_REQUEST['searchPhrase'] == 'quick'){
        $where = " OR r.quick ";
    }
    if($_REQUEST['searchPhrase'] == 'hidden'){
        $where = " OR r.hidden ";
    }

    if(strpos($_REQUEST['searchPhrase'],'is:') === 0){
        preg_match('|is:\s*(.*)\s*$|',$_REQUEST['searchPhrase'],$matches);
        $where = " WHERE r." . pg_escape_identifier($matches[1]);
    }

    return wrapUp($q,$where);
}

function wrapUp($q,$where){
    $q .= $where;

    if(count($_REQUEST['sort']) > 0){
        $q .= " ORDER BY ";
        $orders = Array();
        foreach($_REQUEST['sort'] AS $k => $dir){
            $orders[] = pg_escape_identifier($k) . ($dir == 'asc' ? 'ASC' : 'DESC');
        }
        $q .= implode(',',$orders);
    }

    $countQ = "SELECT COUNT(q.*) AS count FROM ($q) q";
    $q .= " LIMIT " . (int)$_REQUEST['rowCount'] . " OFFSET " . (int)($_REQUEST['rowCount'] * ($_REQUEST['current'] - 1));

    $res = pg_query($q);
    $rows = Array();
    while($row = pg_fetch_assoc($res)){
        $rows[] = $row;
    }

    $countres = pg_query_params($countQ,Array());
    $count = pg_fetch_assoc($countres);

    return Array(
        'current' => (int)$_REQUEST['current'],
        'rowCount' => (int)min($count['count'],$_REQUEST['rowCount']),
        'rows' => $rows,
        'total' => (int)$count['count']
    );
}

function makeIngredientRow($ingredient = FALSE){
    $autoNew = ($ingredient === FALSE ? 'autonewrow' : '');

    if(!$ingredient){
        $ingredient = Array(
            'id' => '',
            'quantity' => '',
            'unit_id' => '',
            'unit' => '',
            'name' => '',
            'premodifier' => '',
            'ingredient_id' => '',
            'postmodifier' => '',
        );
    }

    $ingredient = array_map('htmlentities',$ingredient);

    return "<tr data-type='ingredient' class='$autoNew'>
        <td>
            <input name='i_id[]' type='hidden' value=\"{$ingredient['id']}\">
            <input type='checkbox' name='i_delete[]'>
        </td>
        <td><input name='i_quantity[]' value=\"{$ingredient['quantity']}\"></td>
        <td>
            <input type='hidden' name='i_unit_id[]' value=\"{$ingredient['unit_id']}\">
            <input data-source='ta_units' value=\"{$ingredient['unit']}\">
        </td>
        <td><input name='i_premodifier[]' value=\"{$ingredient['premodifier']}\"></td>
        <td>
            <input type='hidden' name='i_ingredient_id[]' value=\"{$ingredient['ingredient_id']}\">
            <input data-source='ta_ingredient' value=\"{$ingredient['name']}\">
        </td>
        <td><input name='i_postmodifier[]' value=\"{$ingredient['postmodifier']}\"></td>
    </tr>";
}

function makeSubRecipes($subrecipe = FALSE){
    $autoNew = ($subrecipe === FALSE ? 'autonewrow' : '');
    if(!$subrecipe){
        $subrecipe = Array(
            'id' => '',
            'parent' => '',
            'child' => '',
            'parent_name' => '',
            'child_name' => '',
            'childname' => ''
            );
    } 

    $subrecipe = array_map('htmlentities',$subrecipe);

    return "<tr data-type='subrecipe' class='$autoNew'>
        <td>
            <input name='s_id[]' type='hidden' value=\"{$subrecipe['id']}\">
            <input type='checkbox' name='s_delete[]'>
        </td>
        <td>
            <input type='hidden' name='s_child[]' value=\"{$subrecipe['child']}\">
            <input data-source='ta_childrecipe' value=\"{$subrecipe['child_name']}\">
        </td>
        <td>
            <input name='s_childname[]' value=\"{$subrecipe['childname']}\">
        </td>
        </tr>";
}

function processRecipePost(){
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        return;
    }

    $ingredients = Array();
    $subrecipes = Array();

    // Convert i_ and s_ post arrays into ingredient and subrecipe arrays
    foreach($_POST as $k => $val){
        if(strpos($k,'i_') === 0){
            $realk = str_replace('i_','',$k);
            foreach($val as $kk => $vv){
                $ingredients[$kk][$realk] = $vv;
            }
            unset($_POST[$k]);
        }

         if(strpos($k,'s_') === 0){
            $realk = str_replace('s_','',$k);
            foreach($val as $kk => $vv){
                $subrecipes[$kk][$realk] = $vv;
            }
            unset($_POST[$k]);
        }
    }

    // If we're deleting, remove any ingredients and sub-recipes that aren't already in the db
    if(isset($_POST['action']) && $_POST['action'] == 'delete'){
        foreach($ingredients as $k => $v){
            if(!$v['id']){
                unset($ingredients[$k]); 
            }
        }
        foreach($subrecipes as $k => $v){
            if(!$v['id']){
                unset($subrecipes[$k]);
            }
        }
    }


    // For delete -- we delete our sub-stuff, then our recipe
    if(isset($_POST['action']) && $_POST['action'] == 'delete'){
        foreach($subrecipes as $k => $subr){
            $action = "DELETE FROM recipe_recipe WHERE id=" . pg_escape_literal($subr['id']);
            $res = pg_query($action);
        }

        foreach($ingredients as $k => $ing){
            $action = "DELETE FROM recipe_ingredient WHERE id=" . pg_escape_literal($ing['id']);
            $res = pg_query($action);
        }

        $action = "DELETE FROM recipes WHERE id=" . pg_escape_literal($_POST['id']);

        $res = pg_query($action);

        header("Content-type: application/json");
        if($res){
            $row = pg_fetch_assoc($res);
            print json_encode($row);
        }else{
            http_response_code(500);
            print json_encode(Array("success" => FALSE,"msg" => pg_last_error()));
        }

        exit();
    }

    $_POST['quick'] = ($_POST['quick'] == 'on' ? 1 : 0);
    $_POST['favorite'] = ($_POST['favorite'] == 'on' ? 1 : 0);
    $_POST['hide'] = ($_POST['hide'] == 'on' ? 1: 0);

    $ingredients = array_filter($ingredients,function($ingredient){ return count(array_filter($ingredient)); });
    $subrecipes = array_filter($subrecipes,function($subrecipe){ return count(array_filter($subrecipe)); });

    // For update/insert we update/insert the recipe and verify that we have the recipe ID, then we update/insert the other stuff
    $action = updateOrInserts($_POST,'recipes');
    $res = pg_query($action);
    $recipe_row = pg_fetch_assoc($res);

    foreach($ingredients as $k => $ingredient){
        if(count(array_filter($ingredient)) === 0){
            continue;
        }
        $ingredient['recipe_id'] = $recipe_row['id'];
        $action = updateOrInserts($ingredient,'recipe_ingredient',$ingredient['id']);
        pg_query($action);
    }

    foreach($subrecipes as $k => $subrecipe){
        if(count(array_filter($subrecipe)) === 0){
            continue;
        }
        $subrecipe['parent'] = $recipe_row['id'];
        $action = updateOrInserts($subrecipe,'recipe_recipe',$subrecipe['id']);
        pg_query($action);
    }

    header("Content-type: application/json");
    if($recipe_row){
        print json_encode($recipe_row);
    }else{
        http_response_code(500);
        print json_encode(Array("success" => FALSE,"msg" => pg_last_error()));
    }

    exit();
}

function updateOrInserts($data,$table,$tableKey = FALSE){
    $fields = Array();
    $values = Array();

    if($tableKey === FALSE){
        $tableKey = $_POST['id'];
    }

    foreach($data as $k => $v){
        if($k != 'id' && !(empty($v) && $v !== 0)){
            $fields[] = pg_escape_identifier($k);
            $values[] = pg_escape_literal($v);
        }
    }

    if(isset($data['id']) && $data['id'] != ''){
        $kvCombo = array_combine($fields,$values);
        $setPairs = Array();
        foreach($kvCombo as $k => $v){
            $setPairs[] = "$k=$v";
        }

        $action = "UPDATE $table SET ";
        $action .= implode(', ',$setPairs);
        $action .= " WHERE id=" . pg_escape_literal($tableKey) . " RETURNING id";
    }else{
        $kvPairs = Array('fields' => "(" . implode(',',$fields) . ")",'values' => "(" . implode(',',$values) . ")");
        $action = "INSERT INTO $table {$kvPairs['fields']} VALUES {$kvPairs['values']} RETURNING id";
    }

    return $action;
}
