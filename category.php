<?php
require_once('lib/template.php');
$category = getCategory($_GET['id']);
printHeader($category['label'],$_GET['id'],2);
?>
<div class="container">
<?php
print "<h1>" . htmlentities($category['label']) . "</h1>";
        $res = getAllFromCategory($_GET['id']);
        print "<ul class='recipelist'>";
        while($row = pg_fetch_assoc($res)){
            print "<li class='".($row['quick'] == 't' ? 'quick' : '')."'><a href='../recipe/{$row['id']}/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a>".($row['quick'] == 't' ? $quickicon : '') . ($row['favorite'] == 't' ? $favoriteicon : '')."</li>";
        }
        print "</ul>";
?>
</div>
<?php
printFooter();
