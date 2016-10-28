<?php
if(!defined('INDEX')) {
    header('location: /');
}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title><?=$title.' - '.TITLE?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?=$CSS?>
    <link rel="icon" type="image/vnd.microsoft.icon" href="<?=GFX?>favicon.ico" />
    <script language="javascript" src="/js/jquery.js"></script>
    <script language="javascript" src="/js/msg.js"></script>
<?=$SCRIPT?>
   <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-13093339-2']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
</head>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="container">
    <div id="header">
        <div id="header_right">
Logged in as <b><a href="/user/<?=$USER?>"><?=$USER?></a></b> [<a href="/logout">Logout</a>]        </div>
        <div id="header_left"><a href="/"><img src="<?=GFX?>phpGolf5.jpg" alt="" style="height:120px;border:0px;" /></a></div>
    </div>
    <div id="menu">
        [ <a class="menulink" href="/">Main</a> ]
        [ <a class="menulink" href="/news">News</a> ]
        [ <a class="menulink" href="/doc">Documentation</a> ]
        [ <a class="menulink" href="/challenges">Challenges</a> ]
        [ <a class="menulink" href="/view/user/<?=$_SESSION['username']?>">View code</a> ]
        [ <a class="menulink" href="/toplist">Top 250</a> ]
        [ <a class="menulink" href="/stuff">Stuff</a> ]
<?php if(access('show_tools')) {?>        [ <a href="/tools">Admin Tools</a> ]<?php }?>
    </div>
    <div class="msgs">
<?=$MSG?>
    </div>
    <div id="content">
