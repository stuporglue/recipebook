<!DOCTYPE HTML>
<html>
<head>
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
while($row = pg_fetch_assoc($res)){
    print "<a href='recipe/{$row['id']}/" . urlencode($row['name']) . "'>{$row['name']}</a><br>";
}
?>
</body>
</html>
