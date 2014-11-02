<?php
require_once('db.inc');
require_once('parsedown/Parsedown.php');

class recipe {
    function __construct($id){
        $res = pg_query_params('SELECT r.*,c.name AS category FROM recipes r,categories c WHERE r.id=$1 AND r.category=c.id',Array($id));
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
        $ret .= "<ul>";
        foreach($this->ingredients as $ingredient){
            $ingredient['ingredient'] = str_replace(' ,',',',$ingredient['ingredient']);
            $ret .= "<li>" . $this->quantityToString($ingredient['quantity']) . " <span alt='{$ingredient['unit']}'>{$ingredient['abbreviation']}</span> {$ingredient['ingredient']}</li>";
        }
        $ret .= "</ul></div>";
        return $ret;
    }

    function quantityToString($unit){
        // http://symbolcodes.tlt.psu.edu/bylanguage/mathchart.html#fractions
        $whole = (int)$unit;
        $whole = ($whole === 0 ? '' : $whole);
        $part = fmod($unit,1);
        switch($part){
        case 0:
            $part = '';
            break;
        case 0.25:
            $part = '&frac14;';
            break;
        case 0.33:
            $part = '&#x2153;';
            break;
        case 0.5:
            $part = '&frac12;';
            break;
        case 0.66: 
            $part = '&#8532;';
            break;
        case 0.75:
            $part = '&frac34;';
            break;
        }

        $str = $whole . $part;
        return $str;
    }

    function __toString(){
        return print_r($this);
    }

    function directions(){
        $dir = preg_replace("|\s([0-9]+)/([0-9]+)\s|"," <span class='fraction'><sup>$1</sup>&frasl;<sub>$2</sub></span> ",$this->instructions);
        return $this->parsedown->text($dir);
    }
}
