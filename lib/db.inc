<?php

$config = parse_ini_file('config.php');

$db = pg_connect("user={$config['user']} password={$config['password']} dbname={$config['dbname']}");

function getAll(){
    $res = pg_query_params('SELECT r.*,c.name AS category FROM recipes r,categories c WHERE ("hide"=false OR "hide" IS NULL) AND r.category=c.id ORDER BY c.name,r.name',Array());
    return $res;
}

function getAllFromCategory($cat){
    $res = pg_query_params('SELECT r.*,c.name AS category FROM recipes r,categories c WHERE ("hide"=false OR "hide" IS NULL) AND r.category=c.id AND c.name=$1 ORDER BY c.name,r.name',Array($cat));
    return $res;
}

function getCategories(){
    $res = pg_query_params('SELECT DISTINCT 
        categories.*,
        COALESCE(categories.label,categories.name) AS label
        FROM
        categories, recipes
        WHERE categories.id=recipes.category
         AND (hide=false OR hide IS NULL)
         ORDER BY COALESCE(categories.label,categories.name)',Array());
    $rows = Array();
    while($row = pg_fetch_assoc($res)){
        $rows[] = $row; 
    }
    
    return $rows;
}

function getCategory($cat) {
    $res = pg_query_params('SELECT *,COALESCE(label,name) AS label FROM categories WHERE name=$1',Array($cat));
    return pg_fetch_assoc($res);
}

function getMealIds(){
    $res = pg_query_params('SELECT DISTINCT ON (category) id
        FROM recipes
        WHERE hide=false
        ORDER BY category,RANDOM()',Array());

    $rows = Array();
    while($row = pg_fetch_assoc($res)){
        $rows[] = $row['id'];
    }

    return $rows;
}
