<?php
require_once('lib/template.php');
printHeader(implode(' ',array_map('ucfirst',explode(' ',$_GET['id']))),'',2);
?>
<div class="jumbotron">
<?php
print "<h1 class='ingredient'>" . htmlentities($_GET['id']) . "!</h1>";
?>
    These are all the recipes that use <?=htmlentities($_GET['id'])?>. If you just love it, or want to use it up, this is the place to be.
</div>
<div class="container">
<?php 
$prevCat = '';
$res = getRecipesByIngredient($_GET['id']);
while($row = pg_fetch_assoc($res)){
    if($row['catlabel'] !== $prevCat){
        if($prevCat !== ''){
            print "</ul>";
        }

        $prevCat = $row['catlabel'];
        print "<h2>" . htmlentities($prevCat) . "</h2>";
        print "<ul class='recipelist'>";
    }

    print "<li class='".($row['quick'] == 't' ? 'quick' : '')."'><a href='../recipe/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a></li>";
}
print "</ul>";
?>
</div>

<?php

printFooter();
