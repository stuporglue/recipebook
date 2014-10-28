<?php
require_once('lib/recipe.php');

$r = new recipe($_GET['id']);
?>
<!DOCTYPE HTML>
<head>
    <title><?=$r->name?></title>
</head>
<body>
<h1><?=$r->name?></h1>
<h2>Ingredients</h2>
<ul>
<?php
foreach($r->ingredients as $ingredient){
    print "<li>{$ingredient['quantity']} <span alt='{$ingredient['unit']}'>{$ingredient['abbreviation']}</span> {$ingredient['ingredient']}</li>";
}
?>
</ul>
<h2>Instructions</h2>
<p>
<?=$r->instructions?>
</p>
</body>
</html>
