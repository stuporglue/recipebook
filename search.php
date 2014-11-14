<?php
require_once('lib/template.php');
require_once('lib/query.php');
printHeader('Search Results','search');
?>
<div class="jumbotron">
<h1>Search Results for <em><?=$_POST['searchval']?></em></h1>
</div>
<div class='container'>
<?php

$results = query($_POST['searchval']);

foreach($results as $res){
    print "<div class='searchsuggestion " . htmlentities($res['kind']) . "'>";
    print "<h2><span class='kind'></span><a href='../" . $res['url'] . "'>" . $res['label'] . "</a></h2>";
    print "<p class='wherefound'>" . $res['search'] . "</p></div>";
}

printFooter();
