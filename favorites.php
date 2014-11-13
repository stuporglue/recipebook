<?php
require_once('lib/template.php');
printHeader('Quick Recipes!','quick');
?>
<div class="container">
<h1 class='quick'>Caroline's Favorite Recipes!<?=$favoriteicon?></h1>
<p>
All of the recipes here are <em>good</em>, but these are the best of the best, Caroline's very favorites.
</p>
<?php

$prevCat = '';
$res = getFavorites();
while($row = pg_fetch_assoc($res)){
    if($row['catlabel'] !== $prevCat){
        if($prevCat !== ''){
            print "</ul>";
        }

        $prevCat = $row['catlabel'];
        print "<h2>" . htmlentities($prevCat) . "</h2>";
        print "<ul class='recipelist'>";
    }

    print "<li class='".($row['quick'] == 't' ? 'quick' : '')."'><a href='../recipe/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a>".($row['quick'] == 't' ? $quickicon : '') . ($row['favorite'] == 't' ? $favoriteicon : '')."</li>";
}

print "</ul></div>";

printFooter();
