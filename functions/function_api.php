<?php
if(!defined('INDEX')) {
    header('location: /');
}

//Show API
//Print out msg's and errors for API
// $http = http response code
function show_api($http=200,$content='application/json',$charset='UTF-8') {
    $Output = ob_get_contents();
    ob_clean();
    header('x-phpGolf-API-version: 1',true,$http);
    if($content) {
        header('Content-Type: '.$content.'; charset='.$charset);
    }
    echo $Output;
}

//Error api
function error_api($code,$msg) {
    msg($msg,0);
    echo makeOutput(array(),'json');
    show_api($code,'application/json');
}
//Make output
//Makes output in spesified format
function makeOutput(array $output, $type) {
    makeMsgArr($output);
    switch($type) {
        
        case 'xml':
            break;
        case 'plain':
            return makeOutputPlain($output);
            break;
        case 'json':
        default:
            return json_encode($output);
            break;
    }
}

//Internal function
function makeOutputPlain(array $output,$level=0) {
    for($i=0; $i<$level;$i++) {
        $tab .= "    ";
    }
    foreach($output as $key => $value) {
        if(is_array($value)) {
            $return .= $tab.strtoupper($key).":\n";
            $return .= makeOutputPlain($value,$level+1);
            $return .= $tab.':'.strtoupper($key)." END\n";
        } else {
            $return .= $tab.$key.': '.$value."\n";
        }
    }
    return $return;
}

//Make msg array
//Combine respond array with messages and alter array given
function makeMsgArr(array &$response) {
     $MSG = msg();
    if(!empty($MSG)) {
        $types[0] = 'ERROR';
        $types[1] = 'OK';
        $types[2] = 'DEBUG';
        $msgs = array();
        foreach($MSG as $msg) {
            $msgs['MSG'][$types[$msg['type']]][]=$msg['msg'];
        }
        $response = array_merge($msgs,$response);
    }
    return true;
}


