<?php

function query($term){
    $res = search($term);
    $results = Array();
    while($row = pg_fetch_assoc($res)){
        $row['search'] = preg_replace('|<remove>.*?</remove>|','',$row['search']);
        $pos = stripos($row['search'],$term);
        $start = $pos - 30;
        if($start !== FALSE && $start < 0){
            $start = 0;
        }

        $origLen = strlen($row['search']);

        $row['search'] = substr($row['search'],$start,60 + strlen($term));
        preg_match("/(.*)({$term})(.*)/i",$row['search'],$matches);
        if(count($matches) > 0){
            $row['search'] = htmlentities($matches[1]) . "<em>" . htmlentities($matches[2]) . "</em>" . htmlentities($matches[3]);
        }
        $row['plainlabel'] = $row['label'];
        preg_match("/(.*)({$term})(.*)/i",$row['label'],$matches);
        if(count($matches) > 0){
            $row['label'] = htmlentities($matches[1]) . "<em>" . htmlentities($matches[2]) . "</em>" . htmlentities($matches[3]);
        }
        $row['search'] = trim($row['search']);

        if(strlen($row['search']) < $origLen){
            $row['search'] .= '&hellip;';
        }

        if($start !== 0){
            $row['search'] = '&hellip;' . $row['search'];
        }


        $row['url'] = $row['urlpre'] . urlencode($row['urlpost']);
        unset($row['urlpre']);
        unset($row['urlpost']);

        $results[] = $row;
    }

    return $results;
}
