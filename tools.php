<?php
if(!defined('INDEX')) {
    header('location: /');
}

if(!access('show_tools')) {
    error(403);
    exit;
}

switch($_GET['value'][0]) {
    default:
?>
<h1>Tools</h1>
<table>
    <?php if(access('show_tools_useradmin')){?><tr><td><a href="/tools/useradmin">User administration</a></td></tr><?php }?>
    <?php if(access('show_tools_permission')){?><tr><td><a href="/tools/permission">Permission administration</a></td></tr><?php }?>
    <?php if(access('show_tools_challenge')){?><tr><td><a href="/tools/challenge">Challenge administration</a></td></tr><?php }?>
    <?php if(access('show_tools_news')){?><tr><td><a href="/tools/news">News administration</a></td></tr><?php }?>
    <?php if(access('debug')){?><tr><td><a href="/tools/functions">Functions list</a></td></tr><?php }?>
</table>
<?php
        show_page('Tools');
        break;
    case 'useradmin':
        include_site('tool/useradmin');
        break;
    case 'permission':
        include_site('tool/permission');
    break;
    case 'challenge':
        include_site('tool/challenge');
    break;
    case 'news':
        include_site('tool/news');
    break;
    case 'functions':
        include_site('tool/functions');
    break;
}
