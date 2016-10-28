<?php
if(!defined('INDEX')) {
    header('Location: /');
    die('No access here');
}
define('IS_API', true);
include_class('rss');
$PDO=&DB::$PDO;
$args = array();
$WHERE = '';
if($_GET['passed']) {
    $WHERE .=' AND passed';
}elseif($_GET['failed']) {
    $WHERE .=' AND NOT passed';
}

switch($_GET['value'][0]) {
    case 'challenge':
        $cid = getChallengeIdFromSafename($_GET['value'][1]);
        $cname = getChallengeName($cid);
        $RSS = new RSS('Recent attempts on '.$cname);
        $WHERE .= ' AND c.id=:cid';
        $args[':cid']=$cid;
        break;
    default:
        $RSS = new RSS('Recent attempts on all challenges');
        break;
}
$pre = $PDO->prepare('SELECT a.id, a.time, a.size, a.passed, u.username, c.name, c.id 
                    FROM attempts a, users u, challenges c 
                    WHERE c.id = a.challenge_id AND u.id = a.user_id AND a.executed AND c.active AND c.open'.$WHERE.' ORDER BY a.id DESC LIMIT 30');
$pre->execute($args);
while(list($id,$date,$size,$passed,$username,$cname,$cid)=$pre->fetch()) {
    $title = $cname.' - '.$username.' - '.$size.' - '.(($passed)?'passed':'failed');
    $url = 'http://'.DOMAIN.'/challenge/'.getSafenameFromId($cid);
    
    $RSS->addEntry($title,'',$url,$date,$id);
    
}
$RSS->printRSS();

show_api(200,'application/rss+xml');
