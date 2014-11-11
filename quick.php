<?php
require_once('lib/template.php');
printHeader('Quick Recipes!','quick',2);
?>
<div class="container">
<h1 class='quick'>Quick Recipes!</h1>
<p>Need something yummy in a jiffy? These quick recipes should do the trick. You can always look for the kitchen-timer icon next to the recipe name on any recipe category page, or come to this page for a list of just the fast recipes. </p>
<?php
print "<ul>";

$prevCat = '';
$res = getQuick();
while($row = pg_fetch_assoc($res)){
    if($row['catlabel'] !== $prevCat){
        if($prevCat !== ''){
            print "</ul>";
        }

        $prevCat = $row['catlabel'];
        print "<h2>" . htmlentities($prevCat) . "</h2>";
        print "<ul>";
    }

    print "<li class='".($row['quick'] == 't' ? 'quick' : '')."'><a href='../recipe/{$row['id']}/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a></li>";
}
print "</ul>";

print "</ul></div>";

printFooter();
