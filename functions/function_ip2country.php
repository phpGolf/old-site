<?php

function getCountryFromIP($ip) {
    $url = 'http://www.ipgp.net/';
    $referer = 'google.com';
    $data = array();
    $arr = array('ip' => $ip, 'mode' => 'view');    
    while (list($n,$v) = each($arr)) {
        $data[] = "$n=$v";
    }    
    $data = implode('&', $data);

    $url = parse_url($url);
    if ($url['scheme'] != 'http') { 
        return 'World';
    }
    $host = $url['host'];
    $path = $url['path'];
 
    $fp = fsockopen($host, 80);
 
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);
 
    $result = ''; 
    while(!feof($fp)) {
        $result .= fgets($fp, 128);
    }
    fclose($fp);
    $result = explode("\r\n\r\n", $result, 2);
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
    
    preg_match('/<td>\(\w{2}\) (\w+?)<\/td>/', $content, $m);
    return ($m[1]) ? $m[1] : 'World';
}

?>
