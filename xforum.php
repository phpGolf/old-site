<?php
if(!defined('INDEX')) {
    header('location: /');
}

include_class('xforum');
echo '<pre>';
$Category = new xForum_category(1);
$Category->update();
print_r($Category->getCategories());
print_r($Category->getTopics());
echo '</pre>';
show_page('xForum');
