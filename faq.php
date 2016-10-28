<?php
if(!defined('INDEX')) {
    header('location: /');
}
$email="alectbm@phpgolf.org";
var_dump(filter_var($email, FILTER_VALIDATE_EMAIL));

show_page('FAQ');
?>
