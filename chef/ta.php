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

$rows = typeAhead($table,$_REQUEST['q']);

print json_encode($rows);
