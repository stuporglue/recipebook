<?php
require_once('lib/recipe.php');
require_once('lib/template.php');
$ids = Array();
if(isset($_GET['id']) && strlen($_GET['id']) > 0){
    $depth = 2;
    $ids = explode(',',$_GET['id']);
}

if(count($ids) == 0){
    $depth = 1;
    $ids = getMealIds();
}
printHeader("Shopping List",'ingredients',$depth);


$names = Array();
$links = Array();
$ingredients = Array();
foreach($ids as $recipeId){
    $r = new recipe($recipeId);
    $names[] = $r->name;
    $links[] = '../' . $r->getLink();
    $ingredients = array_merge($ingredients,$r->getIngredients());
}

?>
<div class='jumbotron'>
<h1>Shopping List!</h1>
<p>
You can <a href='<?php print implode(',',$ids)?>'>bookmark this list</a>.
</p>
<p>
Here's a shopping list for all the ingredients needed for <?php 

if(count($names) == 1){
    print "<a href='../{$links[0]}' alt='{$names[0]}'>{$names[0]}</a>";
}else{
    $last = array_pop($names);
    $lastlink = array_pop($links);
    foreach($links as $k => $name){
        print "<a href='../{$links[$k]}' alt='{$names[$k]}'>{$names[$k]}</a>, ";
    }
    print " and <a href='../$lastlink' alt='$last'>$last</a>";
}
?>.
</p>
</div>
<?php

$ingredients = Ingredients::combine($ingredients);
print Ingredients::ingredientString($ingredients);

print "</div></div>";
printFooter();
