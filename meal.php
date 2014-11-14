<?php
require_once('lib/recipe.php');
require_once('lib/template.php');
$ids = Array();
if(isset($_GET['id']) && strlen($_GET['id']) > 0){
    $ids = explode(',',$_GET['id']);
}

if(count($ids) == 0){
    $ids = getMealIds();
}
printHeader("Meal planner",'meal');

$menu = '';
$cats = Array();

foreach($ids as $recipeId){
    $r = new recipe($recipeId);

    $cats[] = "<a href='#{$r->category}'>{$r->catlabel}: {$r->name}</a>";

    $menu .=  '<h2 id='.$r->category.'>' . $r->catlabel . ': ' . $r->name . '</h2>';
    $menu .=  '<div class="container">';
    $menu .= "<div class='row'>";
    $menu .= $r->ingredientString(NULL,3);
    $menu .= "</div>";
    $menu .= "</div>";

    $menu .=  '<div class="container">
        <h3>Directions</h3>
        <div class="instructions main">';
    $menu .=  $r->directions();
    $menu .=  '</div>';
    foreach($r->subrecipes as $subname => $sub){
        $menu .=  "<div class='instructions sub'><h3>$subname</h3>{$sub->directions()}</div>";
    }
    $menu .=  "</div>";
}

    $relpath = (isset($_GET['d']) ? str_repeat('../',$_GET['d']) : '');

?>
<div class='jumbotron'>
<h1>Here's Your Random Meal!</h1>
<p>
This tool picks one recipe from each category so you can make a 100% Caroline meal.
There's no guarantee that this is a good combo, but it might be!
</p>
<h2>Your Menu</h2>
<ul class='recipelist'>
<?php
foreach($cats as $cat){
    print "<li>$cat</li>";
}
?>
</ul>
<p>
You can <a href='<?php print "{$relpath}meal/" . implode(',',$ids)?>'>bookmark this meal</a> or <a href='./'>generate a new meal</a>. 
</p>
</div>
<?php

print $menu;
printFooter();
