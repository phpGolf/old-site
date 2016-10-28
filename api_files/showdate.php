<?php
if(!defined('INDEX')) {
    header('Location: /');
    die('No access here');
}
if(empty($_GET['time'])) {
    msg('No times spesified',0);
    echo makeOutput(array(), 'json');
    show_api(200,'application/json');
    exit;
}
$result = array();
$i=0;
foreach($_GET['time'] as $time) {
    $key = str_replace(array(' ',':'),'_',$time);
    $result['times'][$i]['key'] = $time;
    $result['times'][$i++]['text'] = time2str(strtotime($time));
}

echo makeOutput($result, 'json');
show_api(200,'application/json');
