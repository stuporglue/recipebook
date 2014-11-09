<?php

class Ingredients {

    static function ingredientString($ingredients){
        $ret = "<ul>";
        foreach($ingredients as $ingredient){
            $ingredient['ingredient'] = str_replace(' ,',',',$ingredient['ingredient']);
            $ret .= "<li>" . Ingredients::quantityToString($ingredient['quantity']) . " <span alt='{$ingredient['unit']}'>{$ingredient['abbreviation']}</span> {$ingredient['ingredient']}</li>";
        }
        $ret .= "</ul>";
        return $ret;
    }

    static function quantityToString($unit){
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

    static function combine($in){
        $tmp = Array();
        foreach($in as $ingredient){
            $baseU = Ingredients::makeBaseUnit($ingredient['quantity'],$ingredient['unit']);
            if(!isset($tmp[$ingredient['ingredient']])){
                $tmp[$ingredient['ingredient']] = Array(
                        'ingredient' => $ingredient['ingredient'],
                        'quantity' => $baseU['quant'],
                        'unit' => $baseU['unit'],
                        'abbreviation' => $baseU['unit']
                    );
            }else{
                $tmp[$ingredient['ingredient']]['quantity'] += $baseU['quant'];
            }
        }

        // Convert units back to human units
        // sort by name 
        ksort($tmp);
        return array_values($tmp);
    }

    static function makeBaseUnit($quant,$unit){
        return Array(
            'quant' => $quant,
            'unit' => $unit
        );
    }

    static function makeHumanUnit($quant,$unit){
        return Array(
            'quant' => $quant,
            'unit' => $unit
        );
    }
}
