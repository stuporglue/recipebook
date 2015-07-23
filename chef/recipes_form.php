<?php
require_once('edit_template.php');
require_once('../lib/recipe.php');
processRecipePost();

if(isset($_GET['id'])){
    $recipe = new recipe($_GET['id']);
    $updateme = $recipe->updateme;
    $updateme = array_map('htmlentities',$updateme);
}else{
    $updateme = Array(
        'id' => '',
        'name' => '',
        'display_name' => '',
        'about' => '',
        'instructions' => '',
        'category' => '',
        'quick' => '',
        'favorite' => ''
    );
}

?>
<form class='admin' method='post' action='<?= $_SERVER['REQUEST_URI']?>'>
<input type='hidden' name='id' value="<?=$updateme['id']?>">
<div class='formline'><label for='name'>Recipe Name</label><input required type='text' value="<?=$updateme['name']?>" name='name'></div>
<div class='formline'><label for='display_name'>Display Name</label><input type='text' value="<?=$updateme['display_name']?>" name='display_name'></div>
<div class='formline'><label for='about'>About</label><textarea name='about'><?=$updateme['about']?></textarea></div>
<div class='formline'><label for='instructions'>Instructions</label><textarea name='instructions'><?=$updateme['instructions']?></textarea></div>
<div class='formline'><label for='category'>Category</label><select name='category'>
<option value="">--</option>
<?php foreach(getCategories() as $category){
    $selected = '';
    if($category['id'] == $updateme['cid']){
        $selected = 'selected';
    }
    print "<option $selected value=\"{$category['id']}\">{$category['label']}</option>";
}
?>
</select>
<div class='formline '><label for='quick'>Quick</label><input type='checkbox' name='quick' <?= ($updateme['quick'] === 't' ? 'checked' : '') ?>></div>
<div class='formline '><label for='favorite'>Favorite</label><input type='checkbox' name='favorite' <?= ($updateme['favorite'] === 't' ? 'checked' : '') ?>></div>
<div class='formline '><label for='hide'>Hide</label><input type='checkbox' name='hide' <?= ($updateme['hide'] === 't' ? 'checked' : '') ?>></div>
<div class='formgroup'><label>Ingredients</label>
<table><tr>
    <th>Delete</th>
    <th>Quantity</th>
    <th>Units</th>
    <th>Pre-modifier</th>
    <th>Ingredient</th>
    <th>PostModifier</th>
</tr>
<?php
if($recipe){
    foreach($recipe->getIngredients() as $ingredient){
        print makeIngredientRow($ingredient);
    }
}
print makeIngredientRow();
?>
</table>
<div class='formgroup'><label>Sub-Recipes</label>
<table><tr>
    <th>Delete</th>
    <th>Child Recipe</th>
    <th>Child Name</th> 
</tr>
<?php
if($recipe){
    foreach($recipe->getSubrecipes(TRUE) as $subrecipe){
        print makeSubRecipes($subrecipe);
    }
}
print makeSubRecipes();
?>
</table>
</form>
<script>
templates = <?php 
print json_encode(Array(
'ingredient' => makeIngredientRow(),
'subrecipe' => makeSubRecipes()
));
?>;</script>
