<?php
require_once('lib/recipe.php');

$r = new recipe($_GET['id']);
?>
<!DOCTYPE HTML>
<head>
    <title><?=$r->name?></title>
    <link type='text/css' href='../../style.css' rel='stylesheet'/>
</head>
<body class='<?=$r->category?>'>
<h1><?=$r->name?></h1>
<h2>Ingredients</h2>
<?php
print $r->ingredientString();
foreach($r->subrecipes as $subname => $sub){
    print $sub->ingredientString($subname);
}
?>
<h2>Directions</h2>
<div class='instructions main'>
    <?=$r->directions()?>
</div>
<?php
foreach($r->subrecipes as $subname => $sub){
    print "<div class='instructions sub'><h3>$subname</h3>{$sub->directions()}</div>";
}
?>
</body>
</html>
