<?php
//include 'placeholder.php';
//exit;
//INDEX constant
define('INDEX',TRUE);
//Require configs and other files
require_once 'config.php';
require_once FUNCTIONS.'function_start.php';

//Redirect to DOMAIN
if($_SERVER['HTTP_HOST'] != DOMAIN) {
    header('Location: http://'.DOMAIN.$_SERVER['REQUEST_URI']);
    exit;
}
//Include the rest
start();
//Login
//Autologin
if(!$_SESSION['id']) {
    autologin();
}
if($_GET['clearmem'] == 1 && access('clearmem')) {
    $mem = new cache;
    $mem->flush();
}

//Update time
if(access('debug')) {
   // msg($_SESSION['updateTime'] .' - '. date('Y-m-d H:i:s',$_SESSION['updateTime']) . ' - '.(int)($_SESSION['updateTime'] < time()),2);
}
//unset($_SESSION['updateTime']);
if($_SESSION['updateTime'] < time() && $_SESSION['id']) {
    $_SESSION['updateTime'] = time()+600;
    $pre = DB::$PDO->prepare('UPDATE users SET last_time=NOW() WHERE id=:id');
    $pre->execute(array(':id' => $_SESSION['id']));
}

//Timezone
if($_SESSION['timezone']) {
    date_default_timezone_set($_SESSION['timezone']);
}
switch ($_GET['p']) {
    case 'main':
        include_site('main');
    case 'news':
        include_site('news');
        break;
    case 'doc':
        include_site('doc');
        break;
    case 'faq':
        include_site('faq');
        break;
    case 'challenges':
    case 'challenge':
        include_site('challenges');
        break;
    case 'view':
        include_site('view');
        break;
    case 'forum':
        include_site('forum');
        break;
    case 'xforum':
        include_site('xforum');
        break;
    case 'stats':
        include_site('stats');
        break;
    case 'irc':
        include_site('irc');
        break;
    case 'toplist':
        include_site('toplist');
        break;
    case 'tips':
        include_site('tips');
        break;
    case 'recovery':
        include_site('recovery');
        break;
    case 'settings':
        include_site('settings');
        break;
    case 'logout':
        if(logout()) {
            msg('You are now logged out');
        }
        include_site('main');
        break;
    case 'login':
        include_site('login');
        break;
    case 'tools':
        include_site('tools');
        break;
    case 'users':
        header('Location: /user/'.$_GET['value'][0]);
        break;
    case 'user':
        include_site('profile');
        break;
    case 'api':
        include_site('api');
        break;
    case 'image';
        include_site('image');
        break;
    case 'stuff';
        include_site('stuff');
        break;
    case 'test':
        include_site('test');
        break;
    case 'rss':
        include_site('rss');
        break;
    case 'rsscomments':
        include_site('rss_comments');
        break;
    default:
        include_site('main');
        break;
}

if(defined('ERROR')) {
    ob_end_clean();
} else {
    ob_end_flush();
}
?>
