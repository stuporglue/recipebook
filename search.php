<?php

require_once('lib/db.inc');

$res = search($_GET['q']);

$results = Array();
while($row = pg_fetch_assoc($res)){
    $pos = stripos($row['search'],$_GET['q']);
    $start = $pos - 30;
    if($start !== FALSE && $start < 0){
        $start = 0;
    }
    $row['search'] = substr($row['search'],$start,60 + strlen($_GET['q']));
    preg_match("/(.*)({$_GET['q']})(.*)/i",$row['search'],$matches);
    $row['search'] = htmlentities($matches[1]) . "<em>" . htmlentities($matches[2]) . "</em>" . htmlentities($matches[3]);
    $row['url'] = $row['urlpre'] . urlencode($row['urlpost']);
    unset($row['urlpre']);
    unset($row['urlpost']);

    $results[] = $row;
}
print_r($results);

