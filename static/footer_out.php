<?php
if(!defined('INDEX')) {
    header('location: /');
}
if(!include_func('stats')) {
    error();
}
?>        </div>
    </div>
    <div id="footer">
        &copy; Copyright phpGolf 2010 - <?=date('Y')?> | Users online: <?=$ONLINE?> | Members: <?=getTotalMembers();?> | Total submissions: <?=getTotalSubmissions();?> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y8PX4FETFDKS6">Support us</a>
    </div>
</body>
</html>
