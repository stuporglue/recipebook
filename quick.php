<?php
require_once('lib/template.php');
printHeader('Quick Recipes!','quick');
?>
<div class="jumbotron">
<h1>Quick Recipes! <?=$quickicon?></h1>
<p>Need something yummy in a jiffy? These quick recipes should do the trick. You can always look for the kitchen-timer icon next to the recipe name on any recipe category page, or come to this page for a list of just the fast recipes. </p>
</div>
<div class='container'>
<?php

$prevCat = '';
$res = getQuick();
while($row = pg_fetch_assoc($res)){
    if($row['catlabel'] !== $prevCat){
        if($prevCat !== ''){
            print "</ul>";
        }

        $prevCat = $row['catlabel'];
        print "<h2>" . htmlentities($prevCat) . "</h2>";
        print "<ul class='recipelist'>";
    }

    print "<li><a href='../recipe/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a>".($row['quick'] == 't' ? $quickicon : '') . ($row['favorite'] == 't' ? $favoriteicon : '')."</li>";
}

print "</ul></div>";

printFooter();
