<?php

$config = parse_ini_file('config.php');

$db = pg_connect("user={$config['user']} password={$config['password']} dbname={$config['dbname']}");

function getAll(){
    $res = pg_query_params('SELECT r.*,c.name AS category FROM recipes r,categories c WHERE ("hide"=false OR "hide" IS NULL) AND r.category=c.id ORDER BY c.name,r.name',Array());
    return $res;
}
