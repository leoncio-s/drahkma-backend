<?php

namespace utils;

use Exception;

class HtmlTemplateParse {

    public static function parse(string $template, array $data):string | null {
        try{
            $patterns = [];
            $replacements = [];
    
            foreach($data as $key => $val){
                $k = "/{{$key}}/";
                array_push($patterns, $k);
                $replacements[$k] = $val;
            }
    
            $text = preg_replace($patterns, $replacements, $template);
    
            return $text;
        }catch(Exception $e){
            return null;
        }
    }
}