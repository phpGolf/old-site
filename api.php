<?php
if(!defined('INDEX')) {
    header('Location: /');
    die('No access here');
}
switch($_GET['value'][0]) {
    case 'challenge':
        define('IS_API',true);
        include_site('api_files/challenge');
        break;
    case 'showdate':
        define('IS_API',true);
        include_site('api_files/showdate');
        break;
    default:
        //Print API info
        show_page('API');
    break;
}
