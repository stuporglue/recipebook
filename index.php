<!DOCTYPE HTML>
<html>
<head>
    <title>Caroline's Recipes</title>
    <link type='text/css' href='style.css' rel='stylesheet'/>
</head
<body>
<h1>Caroline's Recipes</h1>
<p>
Keep calm and ???
</p>
<p>
For now, here's a list of recipes which have been entered:<br>
<?php
require_once('lib/db.inc');

$res = getAll();
$cat = NULL;
while($row = pg_fetch_assoc($res)){
    if($row['category'] !== $cat){
        $cat = $row['category'];
        print "<h2>" . ucfirst($cat) . "</h2>";
    }
    print "<a href='recipe/{$row['id']}/" . urlencode($row['name']) . "'>{$row['name']}</a><br>";
}
?>
</body>
</html>
