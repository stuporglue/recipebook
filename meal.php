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
?>
<div class='jumbotron'>
<h1>Here's Your Random Meal!</h1>
<p>
This tool picks one recipe from each category so you can make a 100% Caroline meal.
There's no guarantee that this is a good combo, but it might be!
</p>
<p>
You can <a href='<?php print implode(',',$ids)?>'>bookmark this meal</a> or <a href='./'>generate a new meal</a>. 
</p>
</div>
<?php

foreach($ids as $recipeId){
    $r = new recipe($recipeId);
    $cols = count($r->subrecipes) + 1;
    $cols = 12 / $cols;
?>
 <div class="container">
        <h1><?=$r->name?></h1>
      <!-- Example row of columns -->
      <div class="row">
<?php
    print "<div class='col-md-$cols'>"; 
    print "<h3>Ingredients</h3>";
    print $r->ingredientString();
    print "</div>";
    foreach($r->subrecipes as $subname => $sub){
        print "<div class='col-md-$cols'>"; 
        print $sub->ingredientString($subname);
        print "</div>";
    }
?>
      </div>
</div>

<div class='container'>
    <h3>Directions</h3>
    <div class='instructions main'>
        <?=$r->directions()?>
    </div>
<?php
    foreach($r->subrecipes as $subname => $sub){
        print "<div class='instructions sub'><h3>$subname</h3>{$sub->directions()}</div>";
    }
print "</div>";

}

printFooter();
