<?php
require_once('edit_template.php');
processPost('units');

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
<form class='admin' method='post' action='<?= $_SERVER['REQUEST_URI']?>'>
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

