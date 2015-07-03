<?php
require_once('edit_template.php');
processPost('categories');

if(isset($_GET['id'])){
    $updateme = getIngredient($_GET['id']);
}else{
    $updateme = Array(
        'id' => '',
        'name' => '',
        'label' => '',
        );
}
?>
<form method='post' action='<?= $_SERVER['REQUEST_URI']?>'>
<input type='hidden' name='id' value='<?=$updateme['id']?>'>
<div class='formline'><label for='name'>Category Name</label><input required type='text' value='<?=$updateme['name']?>' name='name'></div>
<div class='formline'><label for='label'>Label</label><input type='text' value='<?=$updateme['label']?>' name='label'></div>
</form>

