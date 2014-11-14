<?php
require_once('lib/template.php');
$catId = urldecode($_GET['id']);
$category = getCategory($catId);
printHeader($category['label'],$catId);
?>
<div class="jumbotron">
<?php
print "<h1>" . htmlentities($category['label']) . "</h1>";
print "</div><div class='container'>";
        $res = getAllFromCategory($catId);
        print "<ul class='recipelist'>";
        while($row = pg_fetch_assoc($res)){
            print "<li><a href='../recipe/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a>".($row['quick'] == 't' ? $quickicon : '') . ($row['favorite'] == 't' ? $favoriteicon : '')."</li>";
        }
        print "</ul>";
?>
</div>
<?php
printFooter();
