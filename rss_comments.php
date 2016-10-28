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
        $RSS = new RSS('Recent comments on '.$cname);
        $WHERE .= ' AND c.id=:cid';
        $args[':cid']=$cid;
        break;
    default:
        $RSS = new RSS('Recent comments on all challenges');
        break;
}
$pre = $PDO->prepare('SELECT r.id, c.id, c.name, u.username, r.timestamp, r.text FROM forum_replies r, forum_topics t, challenges c, users u
WHERE r.category_id=5
AND r.topic_id=t.id
AND t.topic=c.name
AND u.id=r.user_id
'.$WHERE.'
ORDER BY r.id DESC LIMIT 10');
$pre->execute($args);
while(list($id,$cid,$cname,$username,$date,$text)=$pre->fetch()) {
    $title = $username.' posted a comment on '.$cname;
    $url = 'http://'.DOMAIN.'/challenge/'.getSafenameFromId($cid);
    $text = htmlspecialchars($text);
    if(strlen($text) > 150) {
        $text = substr($text,0,150).'...';
    }
    $RSS->addEntry($title,$text,$url,$date,$id);
    
}
$RSS->printRSS();

show_api(200,'application/rss+xml');
