<?php

session_start();
if($_SESSION['loggedin'] !== TRUE){
    die("Not logged in");
}

// current=1&rowCount=10&sort[sender]=asc&searchPhrase=&id=b0df282a-0d67-40e5-8558-c9e93b7befed

require_once('../lib/db.inc');

function getPaginatedUnits(){
    $params = Array(
        $_REQUEST['rowCount'],
        $_REQUEST['rowCount'] * ($_REQUEST['current'] - 1)
        );

    $q = "SELECT 
        u.id,
        u.name,
        u.plural,
        b.name AS base_unit,
        u.base_count
        FROM 
        units u LEFT JOIN units b ON u.base_unit = b.id ";

    if(isset($_REQUEST['searchPhrase']) && $_REQUEST['searchPhrase'] != ''){
        $q .= " WHERE 
            u.name LIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " OR
            u.plural LIKE " . pg_escape_literal('%' . $_REQUEST['searchPhrase'] . '%') . " ";
    }
            
    if(count($_REQUEST['sort']) > 0){
        $q .= " ORDER BY ";
        $orders = Array();
        foreach($_REQUEST['sort'] AS $k => $dir){
            $orders[] = pg_escape_identifier($k) . ($dir == 'asc' ? 'ASC' : 'DESC');
        }
        $q .= implode(',',$orders);
    }


    $q .= " LIMIT " . (int)$_REQUEST['rowCount'] . " OFFSET " . (int)($_REQUEST['rowCount'] * ($_REQUEST['current'] - 1));

    $res = pg_query($q);

    $rows = Array();
    while($row = pg_fetch_assoc($res)){
        $rows[] = $row;
    }

    $countres = pg_query_params("SELECT COUNT(*) AS count FROM " . pg_escape_identifier($_REQUEST['table']),Array());
    $count = pg_fetch_assoc($countres);

    return Array(
        'current' => (int)$_REQUEST['current'],
        'rowCount' => (int)min($count['count'],$_REQUEST['rowCount']),
        'rows' => $rows,
        'total' => (int)$count['count']
    );
}

if($_REQUEST['table'] == 'units'){
    $res = getPaginatedUnits();
}else{
    die("this table not yet supported");
}


header("Content-type: application/json");
print json_encode($res);
