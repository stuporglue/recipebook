<?php

require_once('../lib/db.inc');


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
        $action = "DELETE FROM units WHERE id=" . pg_escape_literal($_POST['id']);
    }else if(isset($_POST['id']) && $_POST['id'] != ''){
        $action = 'UPDATE units (' . implode(',',$fields) . ') = ('. implode(',',$values) .') WHERE id=' . pg_escape_literal($_POST['id']) . ' RETURNING id';
    }else{
        $action = 'INSERT INTO units (' . implode(',',$fields) . ') VALUES (' . implode(',',$values) . ') RETURNING id';
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

if(isset($_GET['id'])){
    $updateme = getUnit($_GET['id']);
}else{
    $updateme = Array(
        'id' => '',
        'name' => '',
        'plural' => '',
        'base_unit' => '',
        'base_count' => ''
        );
}
?>
<form method='post' action='<?= $_SERVER['REQUEST_URI']?>'>
<input type='hidden' name='id' value='<?=$updateme['id']?>'>
<div class='formline'><label for='name'>Unit Name</label><input required type='text' value='<?=$updateme['name']?>' name='name'></div>
<div class='formline'><label for='plural'>Plural Name</label><input type='text' value='<?=$updateme['plural']?>' name='plural'></div>
<div class='formline'><label for='base_count'>Base Count</label><input type='number' value='<?=$updateme['base_count']?>' name='base_count'></div>
<div class='formline'><label for='base_unit'>Base Unit</label><select name='base_unit'>
    <option value=''>--</option>
    <?php
    foreach(getUnits() as $unit){
        print "<option " . ($unit['id'] == $updateme['base_unit'] ? 'selected' : '') . " value='{$unit['id']}'>{$unit['name']}</option>";
    }
    ?>
</select></div>
</form>

