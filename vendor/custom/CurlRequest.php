<?php
class CurlRequest
{
    public $url;
    
    public function __construct($url) 
    {
        $this->url = $url;
    }

    public function getOauth2Token($clientTokenPost) 
    {
        $curl = curl_init($this->url."oauth");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $clientTokenPost);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $jsonResponse = curl_exec($curl);
        curl_close($curl);

        $authObj = json_decode($jsonResponse);

        return $authObj;
    }
    
    public function httpGet($queryString,$accessToken)
    {
        $authorization = "Authorization: Bearer ".$accessToken;
        
        $ch = curl_init();  

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch,CURLOPT_URL,$this->url.$queryString);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //curl_setopt($ch,CURLOPT_HEADER, false); 

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }
    
    public function httpPost($params)
    {
        $postData = '';
        //create name value pairs seperated by &
        foreach($params as $k => $v) 
        { 
           $postData .= $k . '='.$v.'&'; 
        }
        $postData = rtrim($postData, '&');

        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    

        $output=curl_exec($ch);

        curl_close($ch);
        return $output;

    }       
}