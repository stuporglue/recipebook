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
printHeader("Meal planner",'meal',$depth);

$menu = '';
$cats = Array();

foreach($ids as $recipeId){
    $r = new recipe($recipeId);
    $cols = count($r->subrecipes) + 1;
    $cols = 12 / $cols;

    $cats[] = "<a href='#{$r->category}'>{$r->catlabel}: {$r->name}</a>";

    $menu .=  '<div class="container">';
    $menu .=  '<h2 id='.$r->category.'>' . $r->catlabel . ': ' . $r->name . '</h2>';
    $menu .=  '<div class="row">';
    $menu .=  "<div class='col-md-$cols'>"; 
    $menu .=  "<h3>Ingredients</h3>";
    $menu .=  $r->ingredientString();
    $menu .=  "</div>";
    foreach($r->subrecipes as $subname => $sub){
        $menu .=  "<div class='col-md-$cols'>"; 
        $menu .=  $sub->ingredientString($subname);
        $menu .=  "</div>";
    }
    $menu .=  '</div></div>';

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
You can <a href='<?php print implode(',',$ids)?>'>bookmark this meal</a> or <a href='./'>generate a new meal</a>. 
</p>
</div>
<?php

print $menu;
printFooter();
