<?php

if(!defined('INDEX')) {
    die('No access');
}

?>

<h1>Come and join the chat on IRC</h1>

<a href="irc://irc.freenode.net/phpgolf">#phpgolf at irc.freenode.net</a><br /><br />

<iframe src="http://webchat.freenode.net?channels=%23phpgolf&uio=MT11bmRlZmluZWQb1" style="width: 100%; height: 600px; margin: 0 auto;"></iframe>

<?php
show_page('IRC');
