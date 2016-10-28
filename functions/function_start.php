<?php
if(!defined('INDEX')) {
    header('location: /');
}

//Start function
function start() {
    if(!defined('FUNC_START')) {
        define('FUNC_START',true);
        require_once FUNCTIONS.'function_login.php';
        require_once FUNCTIONS.'function_gravatar.php';
        require_once FUNCTIONS.'function_image.php';
        require_once FUNCTIONS.'function_challenges.php';
        require_once FUNCTIONS.'function_user.php';
        require_once FUNCTIONS.'function_api.php';
        require_once FUNCTIONS.'function_stats.php';
        require_once CLASSES.'class_logpdo.php';
        require_once CLASSES.'class_cache.php';
        require_once CLASSES.'class_image.php';
        //Start PDO
        new DB;
        
        //Sessions
        session_start();
        //Buffering output
        ob_start();
    }
}

//Check page
function check_page($memcache_key) {
    //Disable cache for now
    return false;
    $mem = new cache;
    $mem->key = $_SESSION['userLevel'].'_'.$memcache_key;
    if(is_array($mem->get()) && count($_POST) == 0) {
        return true;
    } else {
        return false;
    }
}


//Show page
// $title = Title on page
// $memcache_key = Memcache key
// $memcache_time = Memcache time
function show_page($title,$memcache_key=false,$memcache_time=86400) {
    /*$modified = gmdate('D, d M Y H:i:s', time()).' GMT';
    if($memcache_key && is_numeric($memcache_time) && false) {
        $mem = new cache;
        $mem->key = $_SESSION['userLevel'].'_'.$memcache_key;
        if(!$data = $mem->get()) {
            $Output = ob_get_contents();
            $save['title'] = $title;
            $save['content'] = $Output;
            $save['modified'] = $modified;
            $save['memcache_time'] = $memcache_time;
            $mem->set(0,$save,MEMCACHE_COMPRESSED,$memcache_time);
        } else {
            $Output = $data['content'];
            $title = $data['title'];
            $modified = $data['modified'];
        }
        $keys = $mem->get('Keys');
        if(!in_array($memcache_key,(array) $keys)) {
            $keys[] = $memcache_key;
            $mem->set('Keys',$keys);
        }
    } else {
        $Output = ob_get_contents();
    }*/
    $Output = ob_get_contents(); //Disable cache
    ob_clean();
    //HTTP Caching
    /*header('Cache-Control: private, must-revalidate');
    header('Pragma: no-cache');
    header('Expires:');
    if($_SERVER['HTTP_IF_MODIFIED_SINCE'] >= $modified) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    } else {
        header('Last-Modified: '.$modified);
    }
    */
    
    //Show <head>
    addCss(DEF_SKIN);
    addScript('spoiler');
    addScript('showdate_new');
    foreach(addCss() as $css) {
        $CSS .= '    <link rel="stylesheet" type="text/css" href="'.DESIGN.$css.'.css" />
';
    }
    foreach(addScript() as $script) {
        $SCRIPT .= '    <script type="text/javascript" src="'.SCRIPT.$script.'.js"></script>
';
    }
    $msgs = msg();
    foreach($msgs as $msg) {
        switch ($msg['type']) {
            case 0:
                $class = "error";
                break;
            case 1:
                $class = "ok";
                break;
            case 2:
                $class = "debug";
                break;
        }
        $MSG .= '        <div class="'.$class.'">
            '.$msg['msg'].'
        </div>
';
    }
    if($_SESSION['id']) {
        $USER = $_SESSION['username'];
        require STAT.'header_in.php';
    } else {
        require STAT.'header_out.php';
    }
    if(show_right(true)) {
        require STAT.'right.php';
    }
    if(show_right(true)) {
        echo '<div id="content_left">';
        echo $Output;
        echo '</div>';
    } else {
        echo '<div id="content_left_full">';
        echo $Output;
        echo '</div>';
    }
    if(show_subcontent(true)) {
        require STAT.'sub_content.php';
    }
    show_footer();
    //Quit script
    exit;
}

//Shows the footer
function show_footer() {
    $ONLINE = online();
    if($_SESSION['id']) {
        require STAT.'footer_in.php';
    } else {
        require STAT.'footer_out.php';
    }
}


//Shows the right box with top 15 list and etc
// $return = false: none, true: return true if showing right
function show_right($return=false) {
    static $Show = false;
    if(!$return) {
        $Show = true;
    } else {
        return $Show;
    }
}

//Shows the recent activity box under the content.
// $return = false: none, true: return true if showing right
function show_subcontent($return=false) {
    static $Show = false;
    if(!$return) {
        $Show = true;
    } else {
        return $Show;
    }
}

//Include an site
// $site = name of the file without fileending
function include_site($site) {
    if(is_file($site.'.php')) {
        require $site.'.php';
        return true;
    } else {
        return false;
        error(404);
    }
}

//Include class
// $class = class name (or the filename without class_)
function include_class($class) {
    if(is_file(CLASSES.'class_'.$class.'.php')) {
        @include_once CLASSES.'class_'.$class.'.php';
        return true;
    } else {
        return false;
    }
}

//Include function collection
// $func = function collection name (or the filename without class_)
function include_func($func) {
    if(is_file(FUNCTIONS.'function_'.$func.'.php')) {
        @include_once FUNCTIONS.'function_'.$func.'.php';
        return true;
    } else {
        return false;
    }
}

//Add CSS
// $css = name of the css file without fileending
function addCss($css=false) {
    static $SHEETS = array();
   
    if(!$css) {
        return $SHEETS;
    }
    if(is_file(BASE_PATH.DESIGN.$css.'.css')) {
        $SHEETS[] = $css;
        return true;
    }
    return false;
}

//Add js script
// $script = name of the script file without fileending
function addScript($script=false) {
    static $SCRIPTS = array();
   
    if(!$script) {
        return $SCRIPTS;
    }
    if(is_file(BASE_PATH.SCRIPT.$script.'.js')) {
        $SCRIPTS[] = $script;
        return true;
    }
    return false;
}

//Shows the error page
// $code = an predefined error message (404, 403, ...)
// $title = If $code is false, the title of error message
// $msg = If $code is false, the error message
function error($code,$title=false,$msg=false) {
    ob_clean();
    if($code && is_numeric($code)) { //Start numeric behavior
        switch ($code) {
            case 404:
                $msg = "Did not find the page.\nIf you followed an link, please contact the administrators";
                $title = "404 Not Found";
                break;
            case 403:
                $msg = "You do not have access to view this page";
                $title = "403 Forbidden";
                break;
        }
    }
    $title = ($title) ? $title : 'ERROR';
    $msg = ($msg) ? $msg : "An error occured!\nContact the administrators";
    define('ERR_MSG',$msg);
    define('ERR_TITLE',$title);
    $code = ($code) ? $code : 406;
    
    
    if(!defined('ERROR')) {
        define('ERROR',true);    
    }
    //Make site
    if(!defined('IS_API')) {
        if($code) {
            header('What happend???',true,$http);
        }
        include_site('error');
        show_page($title);
    } else {
        error_api($code,$msg);
    }
    ob_flush();
}

//Create an SQL errormessage
function sqlError(array $errorInfo) {
    $debug = debug_backtrace();
    $file = str_replace(BASE_PATH,'',$debug[1]['file']);
    $line = $debug[1]['line'];
    list($code,$driver,$msg)=$errorInfo;
    msg("<b>SQL error:</b><br>
<b>Error code:</b> $code<br>
<b>Driver error code:</b> $driver<br>
<b>SQL msg:</b> $msg<br>
<b>File:</b> $file<br>
<b>Line:</b> $line<br>",2);
}

//Create an return message, this have to be called before show_header()
// $msg = the text to be printed
// $type = 0 for negative, 1 for positive, 2 for debug
function msg($msg=false,$type=1) {
    static $MSGS = array();
    if(!$msg) {
        return $MSGS;
    }else {
        $MSGS[] = array('type'=>$type,'msg'=>$msg);
        return true;
    }
    return false;
}

//Get online users
function online() {
    static $online;
    if(!empty($online)) {
        return $online;
    }
    //Insert get online users code here
    $query = DB::$PDO->query('SELECT count(id) AS online FROM users WHERE last_time>FROM_UNIXTIME(UNIX_TIMESTAMP()-600)');
    $res = $query->fetch();
    $online = (int)$res['online'];
    return (int)$online;
}

//Start DB connection
class DB {
    public static $PDO;
    function __construct() {
        if(!DB::$PDO) {
            try {
                DB::$PDO = new LogPDO(DSN,DB_USER,DB_PASS);
            } catch(PDOException $e) {
                ob_end_clean();
                echo 'Error connecting to database!<br>Contact the administrators';
                exit;
            }
            DB::$PDO->exec('SET time_zone = "Europe/Oslo"');
        }
    }
}

//Random string
// $length = string length
function randStr($length) {
    $array = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
    for($i = 0; $i < $length; $i++) {
        $str .= $array[array_rand($array)];
    }
    return $str;
}

function getGMTdate($date) {
    $oldTZ = date_default_timezone_get();
    date_default_timezone_set('Europe/Oslo');
    $GMTDate = gmdate('Y-m-d\TH:i:s',strtotime($date));
    date_default_timezone_set($oldTZ);
    return $GMTDate;
}

function time2str($timestamp) {
    $time = time() - $timestamp;
    $array =  array('secs' => $time,
                    'mins' => $time / 60,
                    'hours' => $time / (60 * 60),
                    'days' => $time / (60 * 60 * 24),
                    'months' => $time / (60 * 60 * 24 * 30),
                    'years' => $time / (60 * 60 * 24 * 365)
                   );
    if ($array['secs'] < 0) {
        $str = '0 sec';
    }
    elseif ($array['secs'] < 60) {
        $str = round($array['secs']) . ' sec';
    }
    elseif ($array['mins'] < 60) {
         $str = round($array['mins']) . ' min';
    }
    elseif ($array['hours'] < 24) {
        $str = round($array['hours']) . (round($array['hours']) > 1? ' hours' : ' hour');
    }
    elseif ($array['days'] < 30) {
        $str = round($array['days']) . (round($array['days']) > 1? ' days' : ' day');
    }
    elseif ($array['months'] < 12) {
        $str = round($array['months']) . (round($array['months']) > 1? ' months' : ' month');
    }
    else {
        $str = round($array['years']) . (round($array['years']) > 1? ' years' : ' year');
    }
    return $str;
}

//Count bytes in array
function countBytes(array $Array, $continue=false) {
    static $size = 0;
    if(!$continue) {
        $size = 0;
    }
    foreach($Array as $Key => $Value) {
        $size += strlen($Key);
        if(is_array($Value)) {
            countBytes($Value, true);
        } else {
            $size += strlen($Value);
        }
    }
    return $size;
}

//Rowclass
// $rows = different rows, default is 2 (false = reset);
function rowClass($rows = 2,$id=1) {
    static $i = array();
    if(!isset($i[$id])) {
        $i[$id] = 0;
    }
    if(!$rows) {
        $i[$id] = 0;
        return true;
    }
    return 'row'.(($i[$id]++%$rows)+1);
}

//Show an formated date
// $date = an valid date
function showDate($date) {
    if(!is_numeric($date)) {
        $timestamp = strtotime($date);
    } else {
        $timestamp = $date;
    }
    return date(DATE_FORMAT,$timestamp);
}

//Show an formated time
// $time = an valid time
function showTime($time) {
    if(!is_numeric($time)) {
        $timestamp = strtotime($time);
    } else {
        $timestamp = $time;
    }
    return date(TIME_FORMAT,$timestamp);
}

function showDateTime($date) {
    if(!is_numeric($date)) {
        $timestamp = strtotime($date);
    } else {
        $timestamp = $date;
    }
    return date(DATE_FORMAT.', '.TIME_FORMAT,$timestamp);
}

//Clear http cache
function clearHttpCache() {
    $mem = new cache;
    $mem->key = 'Keys';
    if($keys = $mem->get()) {
        foreach($keys as $key) {
            $data = $mem->get($_SESSION['userLevel'].'_'.$key);
            $data['modified'] = $modified = gmdate('D, d M Y H:i:s', time()).' GMT';
            $mem->replace($_SESSION['userLevel'].'_'.$key,$data,MEMCACHE_COMPRESSED,$data['memcache_time']);
        }
    }
}
