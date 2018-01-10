<?php
namespace oldtailor\oauth;

class Utils {
    
    public static function combineURL($baseURL,$keysArr){
        
        $combined = $baseURL."?";
        
        $valueArr = array();
        
        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }
        
        $keyStr = implode("&",$valueArr);
        
        $combined .= ($keyStr);
        
        return $combined;
    }
    
}