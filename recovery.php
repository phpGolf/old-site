<?php
if(!defined('INDEX')) {
    header('location: /');
}
include_func('recovery');

if ($_GET['value'][0] == 'id' && is_numeric($_GET['value'][1]) && $_GET['value'][2] == 'hash' && !empty($_GET['value'][3])) {
    validateRecoverHash($_GET['value'][1], $_GET['value'][3]);
}

show_page('PasswordRecovery');
