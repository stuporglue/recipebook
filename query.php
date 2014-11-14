<?php
require_once('lib/db.inc');
require_once('lib/query.php');
$results = query($_GET['q']);
header("Content-type: application/json");
print json_encode($results);

