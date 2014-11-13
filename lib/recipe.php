<?php
require_once('db.inc');
require_once('parsedown/Parsedown.php');
require_once('lib/ingredients.php');

class recipe {
    function __construct($name_or_id){
        $res = pg_query_params('SELECT r.*,c.name AS category,c.label AS catlabel FROM recipes r,categories c WHERE (r.name=$1 OR r.id=$2) AND r.category=c.id',Array($name_or_id,intval($name_or_id,10)));
        if(!$res){
            var_dump($res);
            throw new Exception("No such recipe");
        }
        $row = pg_fetch_assoc($res);
        foreach($row as $k => $v){
            $this->$k = $v;
        }
        $this->getIngredients();
        $this->getSubrecipes();
        $this->parsedown = new Parsedown();
    }

    function getIngredients(){
        if(isset($this->ingredients)){
            return $this->ingredients;
        }
        $res = pg_query_params("SELECT * FROM pretty_ingredients WHERE recipe_id=$1",Array($this->id));
        $this->ingredients = Array();
        while($row = pg_fetch_assoc($res)){
            $this->ingredients[] = $row;
        }
        return $this->ingredients;
    }

    function getSubrecipes(){
        if(isset($this->subrecipes)){
            return $this->subrecipes;
        }
        $res = pg_query_params("SELECT * FROM recpie_recipe WHERE parent=$1",Array($this->id));
        $this->subrecipes = Array();
        while($row = pg_fetch_assoc($res)){
            $this->subrecipes[$row['childname']] = new recipe($row['child']);
        }
        $more = Array();
        foreach($this->subrecipes as $sub){
            $more = array_merge($more,$sub->subrecipes);
        }
        $this->subrecipes = array_merge($this->subrecipes,$more);
        return $this->subrecipes;
    }

    function ingredientString($subname=NULL){
        $moreclasses = (is_null($subname) ? '' : ' sub');
        $ret = "<div class='ingredients$moreclasses'>";
        if(!is_null($subname)){
            $ret .= "<h3>$subname</h3>";
        }
        $ret .= Ingredients::ingredientString($this->ingredients);
        $ret .= "</div>";
        return $ret;
    }

    function __toString(){
        return print_r($this);
    }

    function getLink(){
        return "recipe/{$this->id}/" . urlencode($this->name);     
    }

    function directions(){
        $dir = preg_replace("|\s([0-9]+)/([0-9]+)\s|"," <span class='fraction'><sup>$1</sup>&frasl;<sub>$2</sub></span> ",$this->instructions);
        return $this->parsedown->text($dir);
    }
}
