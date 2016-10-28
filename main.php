<?php
if(!defined('INDEX')) {
    header('location: /');
}

include_class('twitter');
include_class('bitly');
include_class('news');
include_func('news');
include_func('bbcode');
show_subcontent();
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
<h3>What is phpGolf?</h3>

<p>phpGolf is a code golf server for the <a style="font-size:14px;color:#123456;" href="http://php.net">PHP programming language</a>.</p>
<p>The term comes from the original <a href="http://en.wikipedia.org/wiki/Perl_golf#Perl_golf">Perl Golf</a>, where the goal is to solve programming challenges with as few bytes as possible.<br />
Just like in real golf where players aspire to get the ball in the hole with as few strokes as possible.</p>

<p>This is not a place to learn about good programming practice. This is just for fun.</p>

<p>Please read the <a style="font-size:14px;color:#123456;" href="/doc">documentation</a> page before you continue.<br />
Come and join us for a friendly chat on <a style="font-size:14px;color:#123456;" href="/irc">#phpgolf@Freenode</a> and get updates <a style="font-size:14px;color:#123456;" href="http://twitter.com/phpGolf">@phpGolf</a>. Also here is a RSS for <a style="font-size:14px;color:#123456;" href="/rss">recent activity</a> and <a style="font-size:14px;color:#123456;" href="/rsscomments">comments</a>.</p>

<p>Want to learn to golf like a pro? Check out our <a style="font-size:14px;color:#123456;" href="/tips">tips &amp; tricks</a> article.</p>

<div class="fb-like" data-href="https://www.facebook.com/pages/PHPGolf/290343524428202?fref=ts" data-send="true" data-width="450" data-show-faces="false"></div>

<?php

show_page($title,$mem_key);
