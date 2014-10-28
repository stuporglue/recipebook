<?php
require_once('db.inc');

class recipe {
    function __construct($id){
        $res = pg_query_params('SELECT * FROM recipes WHERE id=$1',Array($id));
        if(!$res){
            var_dump($res);
            throw new Exception("No such recipe");
        }
        $row = pg_fetch_assoc($res);
        foreach($row as $k => $v){
            $this->$k = $v;
        }
        $this->ingredients();
        $this->subrecipes();
    }

    function ingredients(){
        $res = pg_query_params("SELECT * FROM pretty_ingredients WHERE recipe_id=$1",Array($this->id));
        $this->ingredients = Array();
        while($row = pg_fetch_assoc($res)){
            $this->ingredients[] = $row;
        }
    }

    function subrecipes(){
        $res = pg_query_params("SELECT * FROM recpie_recipe WHERE parent=$1",Array($this->id));
        $this->subrecipes = Array();
        while($row = pg_fetch_assoc($res)){
            $this->subrecipes[$row['childname']] = new recipe($row['child']);
        }
    }

    function __toString(){
        return print_r($this);
    }
}
