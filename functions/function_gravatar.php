<?php
if(!defined('INDEX')) {
    header('location: /');
}
//Return values
define('GRAVATAR_INVALID_EMAIL',-1);
define('GRAVATAR_IMAGE_NOTFOUND',-2);


function createGravatarUrl($email,$check=false) {
    $baseUrl = 'http://www.gravatar.com/avatar/';
    
    //Trim and etc
    $email = trim($email);
    $email = strtolower($email);
    
    //Check for valid email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return GRAVATAR_INVALID_EMAIL;
    }
    
    //Make hash
    $hash = md5($email);
    $url = $baseUrl.$hash;
    
    //Check if valid image
    if($check) {
        //Make curl
        $ch = curl_init($url.'?d=404');
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_exec($ch);
        $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if($code == 404) {
            return GRAVATAR_IMAGE_NOTFOUND;
        }
    }
    return $url;
}
