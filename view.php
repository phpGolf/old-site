<?php

if (!defined('INDEX')) {
    die('No access');
}

$PDO =&DB::$PDO;

if ($_GET['value'][0] == 'code' || $_GET['value'][0] == 'diff') {
    header('Content-Type: text/html; charset=ISO-8859-1');
}

if (!isset($_SESSION['id'])) {
    print '<h1>You must be logged in to view code.</h1>';
    show_page('Error');
    exit();
}

switch ($_GET['value'][0]) {
    // show page: /view/user/<username>
    case 'user':
        include ('view_user.php');
    break;
    // Show page: /view/challenge/<challenge name>
    case 'challenge':
        include ('view_challenge.php');
    break;
    // show page: /view/code/<id>
    case 'code':
        include ('view_code.php');
    break;
    // show page: /view/diff/<id>
    case 'diff':
        include ('view_diff.php');
    break;
    // show page: /view/input/<id>
    case 'input':
        include ('view_input.php');
    break;
    // show page: /view/errors/<id>
    case 'error':
        include ('view_errors.php');
    break;
    default:
        header('Location: /view/user');
        exit();
    break;
}

?>
