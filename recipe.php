<?php
require_once('lib/recipe.php');
require_once('lib/template.php');
$r = new recipe(urldecode($_GET['id']));
printHeader($r->name,$r->category);
?>

<div itemscope itemtype='http://data-vocabulary.org/Recipe'>

<div class="jumbotron <?=$r->category?>">
    <h1><?php 
print "<span itemprop='name'>{$r->name}</span>";
if($r->quick == 't'){
    print $quickicon;
}
if($r->favorite == 't'){
    print $favoriteicon;
}
?></h1>

<?php editLink('recipes',$r->id); ?>

<div class='hidden'>
</div>
<?php

if(!is_null($r->about)){
    print "<p itemprop='summary'>" . $r->about() . "</p>";
}
    
$usedIn = $r->usedIn();
if($usedIn !== FALSE){
    print "This recipe is used as a step in the following recipes:";
    print "<ul class='recipelist'>";
    foreach($usedIn as $parentRecs){
        print $parentRecs;
    }
    print "</ul>";
}
?>
</div>
<div class='container'>
    <!-- Example row of columns -->
    <div class="row">
    <?php
        print $r->ingredientString();
    ?>
    </div>
</div>

<div class='container' itemprop='instructions'>
    <h2>Directions</h2>
    <div class='instructions main'>
        <?=$r->directions()?>
    </div>
<?php
foreach($r->subrecipes as $subname => $sub){
    print "<div class='instructions sub'><h3>$subname</h3>{$sub->directions()}</div>";
}
print "</div></div>";


printFooter();
