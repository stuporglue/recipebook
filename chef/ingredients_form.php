<?php
require_once('edit_template.php');
processPost('ingredients');

if(isset($_GET['id'])){
    $updateme = getIngredient($_GET['id']);
}else{
    $updateme = Array(
        'id' => '',
        'name' => '',
        'plural' => '',
        );
}
?>
<form method='post' action='<?= $_SERVER['REQUEST_URI']?>'>
<input type='hidden' name='id' value='<?=$updateme['id']?>'>
<div class='formline'><label for='name'>Ingredient Name</label><input required type='text' value='<?=$updateme['name']?>' name='name'></div>
<div class='formline'><label for='plural'>Plural Name</label><input type='text' value='<?=$updateme['plural']?>' name='plural'></div>
</form>

