<?php
if(!defined('INDEX')) {
    header('location: /');
}

class Bitly {
    private $username = 'phpgolf';
    private $apiKey = '';
    private $URL = 'http://api.bit.ly/v3/[[FUNCTION]]?login=[[USERNAME]]&apiKey=[[APIKEY]]';

    function __construct() {
        $this->URL = str_replace(array('[[USERNAME]]','[[APIKEY]]'),array($this->username,$this->apiKey),$this->URL);
    }
    
    //Shorten
    // $url = url to be shorten
    function shorten($url) {
        $URL = str_replace('[[FUNCTION]]','shorten',$this->URL);
        $url = rawurlencode($url);
        $URL .= "&longUrl=$url&format=txt";
        return file_get_contents($URL);
    }
}
