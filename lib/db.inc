<?php

$config = parse_ini_file('config.php');

$db = pg_connect("user={$config['user']} password={$config['password']} dbname={$config['dbname']}");

function getAll(){
    $res = pg_query_params('SELECT * FROM recipes WHERE "hide"=false OR "hide" IS NULL');
    return $res;
}
