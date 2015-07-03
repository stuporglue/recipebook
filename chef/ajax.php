<?php

require_once('edit_template.php');

switch ($_REQUEST['table']){
case 'units':
    $res = getPaginatedUnits();
    break;
case 'ingredients':
    $res = getStandard('ingredients',$fieldInfo['ingredients']);
    break;
case 'categories':
    $res = getStandard('categories',$fieldInfo['categories']);
    break;
case 'recipes':
    $res = getPaginatedRecipes();
    break;
default:
    http_response_code(404);
    die("this table not yet supported");
}
header("Content-type: application/json");
print json_encode($res);
