<?php
require_once('lib/recipe.php');
require_once('lib/template.php');
$r = new recipe($_GET['id']);
printHeader($r->name,$r->category,2);

$cols = count($r->subrecipes) + 1;
$cols = 12 / $cols;
?>

<!-- div class='jumbotron'>
<h1><?=$r->name?></h1>
</div-->
    <div class="container <?=$r->category?>">
        <h1><?=$r->name?></h1>
      <!-- Example row of columns -->
      <div class="row">
        <?php
            print "<div class='col-md-$cols'>"; 
            print "<h2>Ingredients</h2>";
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

<h2>Directions</h2>
<div class='instructions main'>
    <?=$r->directions()?>
</div>
<?php
foreach($r->subrecipes as $subname => $sub){
    print "<div class='instructions sub'><h3>$subname</h3>{$sub->directions()}</div>";
}

printFooter();
