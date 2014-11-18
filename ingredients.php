<?php
require_once('lib/template.php');
printHeader("Ingredients",'ingredients');
?>
<div class='jumbotron'>
<h1>Ingredients!</h1>
<p>
Trying to use up the last of something? Need to satisfy your craving for something? 
Use this list to find all recipes that use a specific ingredient.
</p>
</div>
<div class='container'>
<?php
    $res = getAllIngredients();
    $list = '';
    $prevChar = '';
    $links = Array();
    while($row = pg_fetch_assoc($res)){
        $firstChar = ucfirst(substr($row['ingredient'],0,1));
        if($firstChar != $prevChar){
            if($prevChar != ''){
                $list .= "</ul></div>\n";
            }
            $prevChar = $firstChar;
            $list .= "\n<div id='{$prevChar}' class='alphagroup'><h2>$prevChar</h2>";
            $list .= "<ul id='$firstChar' class='ingredientlist'>";
            $links[] = "<a href='#{$prevChar}'>$prevChar</a>";
        }
        $list .= "<li><a href='../ingredient/". urlencode($row['ingredient']) . "' alt='" . htmlentities($row['ingredient']) . "' class='ingredient screenonly'>" . htmlentities($row['ingredient']) . "</a><span class='ingredient print'>" . htmlentities($row['ingredient']) . "</span><span class='count screenonly' title='Used in {$row['count']} recipes'>" . $row['count'] . "</span></li>";
    }
    $list .= "</ul></div>";

    print "<div class='links'>" . implode(' | ',$links) . "</div>";

    print "<div class='longlist'>";
    print $list;
    print "</div>";
?>
</ul>
</div>
<?php
print "</div></div>";
printFooter();
