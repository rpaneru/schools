<?php
class Security 
{    
    public static $secKey = '59c84ab';
    public static $compulsion = 'Yes';    
    
    public function generateAndMatchHash($data)    
    {	        
        $array = (array)$data;        
        unset($array['hash']);
        $str = '';
        foreach($array as $key=>$val)	
        {
            $str.= '|'.$key.'|'.$val;
        }
        $str = trim($str,'|');        
        $str.='|key|'.self::$secKey;
        return md5($str);        
    }     
}