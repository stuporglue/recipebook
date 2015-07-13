<?php
header("Content-type: application/json");

$allowedTables = Array(
    'units' => 'units',
    'ingredient' => 'ingredients',
    'childrecipe' => 'recipes'
    );

$table = $allowedTables[$_REQUEST['t']];
if(!$table){
    die("Not a valid table");
}

if(empty($_REQUEST['q'])){
    die("No query");
}

require_once('../lib/db.inc');

$res = pg_query_params('SELECT id,name FROM ' . $table . ' WHERE name ILIKE $1',Array('%' . $_REQUEST['q']. '%'));

$rows = Array();
while($row = pg_fetch_assoc($res)){
    $rows[] = $row;
}

print json_encode($rows);
