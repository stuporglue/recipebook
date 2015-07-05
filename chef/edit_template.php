<?php
session_start();
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
        Array( 'id' => 'name', 'label' => 'Name', 'css-class' => 'widercol', 'header-css-class' => 'widercol'),
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

        $fields = Array();
        $values = Array();
        foreach($_POST as $k => $v){
            if($k != 'id'){
                $fields[] = pg_escape_identifier($k);
                $values[] = pg_escape_literal($v);
            }
        }

        if(isset($_POST['action']) && $_POST['action'] == 'delete'){
            $action = "DELETE FROM $table WHERE id=" . pg_escape_literal($_POST['id']);
        }else if(isset($_POST['id']) && $_POST['id'] != ''){
            $action = "UPDATE $table (" . implode(',',$fields) . ') = ('. implode(',',$values) .') WHERE id=' . pg_escape_literal($_POST['id']) . ' RETURNING id';
        }else{
            $action = "INSERT INTO $table (" . implode(',',$fields) . ') VALUES (' . implode(',',$values) . ') RETURNING id';
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


    error_log($q . $where);


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

    $q .= " LIMIT " . (int)$_REQUEST['rowCount'] . " OFFSET " . (int)($_REQUEST['rowCount'] * ($_REQUEST['current'] - 1));

    $res = pg_query($q);
    $rows = Array();
    while($row = pg_fetch_assoc($res)){
        $rows[] = $row;
    }

    $countres = pg_query_params("SELECT COUNT(*) AS count FROM " . pg_escape_identifier($_REQUEST['table']) . $where,Array());
    $count = pg_fetch_assoc($countres);

    return Array(
        'current' => (int)$_REQUEST['current'],
        'rowCount' => (int)min($count['count'],$_REQUEST['rowCount']),
        'rows' => $rows,
        'total' => (int)$count['count']
    );
}

function makeIngredientRow($ingredient = FALSE){

    if(!$ingredient){
        $ingredient = Array(
            'id' => '',
            'quantity' => '',
            'unit_id' => '',
            'premodifier' => '',
            'ingredient_id' => '',
            'postmodifier' => '',
        );
    }

    $ingredient = array_map('htmlentities',$ingredient);

    return "<tr>
        <td>
            <input name='i_id[]' type='hidden' value=\"{$ingredient['id']}\">
            <input type='checkbox' name='i_delete[]'>
        </td>
        <td><input name='i_quantity[]' value=\"{$ingredient['quantity']}\"></td>
        <td>
            <input type='hidden' name='i_unit_id[]' value=\"{$ingredient['unit_id']}\">
            <input value=\"{$ingredient['unit']}\">
        </td>
        <td><input name='i_premodifier[]' value=\"{$ingredient['premodifier']}\"></td>
        <td>
            <input type='hidden' name='i_ingredient_id[]' value=\"{$ingredient['ingredient_id']}\">
            <input value=\"{$ingredient['name']}\">
        </td>
        <td><input name='i_postmodifier[]' value=\"{$ingredient['postmodifier']}\"></td>
    </tr>";
}

function makeSubRecipes($subrecipe = FALSE){
    if(!$subrecipe){
        $subrecipe = Array(
            'id' => '',
            'parent' => '',
            'child' => '',
            'parent_name' => '',
            'child_name' => ''
            );
    } 

    $subrecipe = array_map('htmlentities',$subrecipe);

    return "<tr>
        <td>
            <input name='s_id[]' type='hidden' value=\"{$subrecipe['id']}\">
            <input type='checkbox' name='s_delete[]'>
        </td>
        <td>
            <input type='hidden' name='s_child[]' value=\"{$subrecipe['child']}\">
            <input value=\"{$subrecipe['child_name']}\">
        </td>
        <td>
            <input name='childname' value=\"{$subrecipe['childname']}\">
        </td>
        </tr>";
}
