<?php

class Ingredients {

    static function ingredientString($ingredients,$urlDepth = 1){
        $ret = "<ul>";
        foreach($ingredients as $ingredient){
            $li = "<li>" . Ingredients::quantityToString($ingredient['quantity']);
            $li .= " <span alt='{$ingredient['unit']}'>{$ingredient['abbreviation']}</span> ";
            $li .= $ingredient['premodifier'];
            $li .= " <a href='".str_repeat('../',$urlDepth)."ingredient/".urlencode($ingredient['name'])."' class='ingredient screenonly'>" . htmlentities($ingredient['name']) . "</a> ";
            $li .= " <span class='ingredient print'>" . htmlentities($ingredient['name']) . "</span>" ;
            $li .=  $ingredient['postmodifier'];
            $li .= "</li>";
            $li = str_replace(' ,',',',$li);
            $ret .= $li;
        }
        $ret .= "</ul>";
        return $ret;
    }

    static function quantityToString($unit){
        // http://symbolcodes.tlt.psu.edu/bylanguage/mathchart.html#fractions
        $whole = (int)$unit;
        $whole = ($whole === 0 ? '' : $whole . ' ');
        $part = round(fmod($unit,1) * 4) / 4;

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
        case 1: 
            $whole++;
            $part = '';
        }

        $str = $whole . $part;
        return $str;
    }

    static function combine($in){
        $tmp = Array();
        foreach($in as $ingredient){
            $baseU = Ingredients::makeBaseUnit($ingredient);
            $id_name = "{$ingredient['ingredient']}_{$ingredient['base_unit']}";
            if(!isset($tmp[$id_name])){
                $tmp[$id_name] = Array(
                        'ingredient' => $ingredient['ingredient'],
                        'quantity' => $baseU['quant'],
                        'unit' => $baseU['unit'],
                        'abbreviation' => $baseU['unit']
                    );
            }else{
                $tmp[$id_name]['quantity'] += $baseU['quant'];
            }
        }

        foreach($tmp as $name => $ingredient){
            print_r($ingredient);
            $humanU = Ingredients::makeHumanUnit($ingredient['quantity'],$ingredient['unit']);
            $tmp[$name]['unit'] = $humanU['unit'];
            $tmp[$name]['quantity'] = $humanU['quant'];
            $tmp[$name]['abbreviation'] = $humanU['quant'];
        }

        // sort by name 
        ksort($tmp);
        return array_values($tmp);
    }

    static function makeBaseUnit($ingredient){
        $unit = getUnit($ingredient['base_unit']);
        $ret = Array(
            'quant' => $ingredient['quantity'] * (isset($ingredient['base_count']) ? $ingredient['base_count'] : 1),
            'unit' => $unit['name']
        );

        return $ret;
    }

    static function makeHumanUnit($quant,$unit){

        if($unit == 'milligram'){
            // make this number into tidy pounds, ounces
        }else if($unit == 'milliliter'){
            // make this into quarts, pints, cups, tablespoons, teaspoons
        }else{
            print "UNIT WAS $unit\n";
        }


        return Array(
            'quant' => $quant,
            'unit' => $unit
        );
    }
}
