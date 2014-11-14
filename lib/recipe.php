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

    function ingredientString($subname=NULL,$hlevel=2){
        $retAr = Array();
        if(count($this->ingredients) === 0){
            $retAr[] = ''; // empty ingredients list
        }else if(is_null($subname)){
            $ret = "\n<div class='ingredients'>";
            $ret .= "<h$hlevel>Ingredients</h$hlevel>";
            $ret .= Ingredients::ingredientString($this->ingredients);
            $ret .= "</div>\n";
            $retAr[] = $ret;
        }else{
            $ret = "\n<div class='ingredients sub'>";
            $ret .= "<h".($hlevel + 1).">$subname</h".($hlevel + 1).">";
            $ret .= Ingredients::ingredientString($this->ingredients);
            $ret .= "</div>\n";
            $retAr[] = $ret;
        }

        foreach($this->subrecipes as $subn => $sub){
            $retAr = array_merge($retAr,$sub->ingredientString($subn,$hlevel));
        }

        if(is_null($subname)){
            $retAr = array_filter($retAr);
            $retStr = '';
            $cols = 12 / count($retAr);
            foreach($retAr as $ing){
                $retStr .= "<div class='col-md-$cols'>$ing</div>";
            }
            return $retStr;
        }else{
            return $retAr;
        }
    }

    function __toString(){
        return print_r($this);
    }

    function getLink(){
        return "recipe/" . urlencode($this->name);     
    }

    function directions(){
        $dir = preg_replace("|\s([0-9]+)/([0-9]+)\s|"," <span class='fraction'><sup>$1</sup>&frasl;<sub>$2</sub></span> ",$this->instructions);
        return $this->parsedown->text($dir);
    }

    function usedIn(){
        global $favoriteicon,$quickicon;

        $res = pg_query_params('SELECT r.name,r.quick,r.favorite FROM recpie_recipe rr, recipes r WHERE rr.parent=r.id AND rr.child=$1',Array($this->id));
        $parents = Array();
        while($row = pg_fetch_assoc($res)){
            $parents[] = "<li><a href='../recipe/" . urlencode($row['name']) . "' alt='{$row['name']}'>{$row['name']}</a>".($row['quick'] == 't' ? $quickicon : '') . ($row['favorite'] == 't' ? $favoriteicon : '')."</li>";
        }

        if(count($parents) === 0){
            return FALSE;
        }
        return $parents;
    }
}
