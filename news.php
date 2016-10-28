<?php
if(!defined('INDEX')) {
    header('location: /');
}

include_class('twitter');
include_class('bitly');
include_class('news');
include_func('news');
include_func('bbcode');
show_right();
$news = news();
$title = 'Main';
$mem_key = 'Main';
//Show single post
if(!empty($_GET['value'][0])) {
    $ex = explode('-',$_GET['value'][0]);
    $nid = $ex[0];
    if($news[$nid]) {
        $post = $news[$nid];
        unset($news);
        $news[$nid] = $post;
        $single = true;
        $title = $post['title'].' - News';
        $mem_key = 'NewsPost_'.$nid;
    }
}
//Check if page is cached
if(check_page($mem_key)) {
    show_page($title,$mem_key);
    exit;
}
?>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
<?php
if(!$single) {
?>
    <tr>
        <th colspan="2" style="font-size: 16px; text-align:left;">News</th>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
<?php
}
if(count($news) != 0) {
    foreach($news as $nid => $post) {
?>
    <tr class="row_header">
        <th colspan="2" style="padding-top: 3px; text-align: left;"><a href="/news/<?=$nid.'-'.$post['safetitle']?>/"><?=$post['title']?></a></th>
    </tr>
    <tr class="row1">
        <td colspan="2" style="padding: 10px; padding-left: 50px;"><?=bbcode($post['text'])?></td>
    </tr>
    <tr class="row2">
        <td style="width: 200px;"><?=showDateTime($post['date'])?></td>
        <td style="width: 150px; text-align: right;">Posted by <a href="/user/<?=$post['author_name']?>/"><?=$post['author_name']?></a></td>
    </tr>
    <tr style="height: 30px;">
        <td colspan="2">&nbsp;</td>
    </tr>
<?php
    }
} else {
?>
    <tr>
        <th colspan="2">No news</th>
    </tr>
<?php
}
?>
</table>
<?php
show_page($title,$mem_key);
