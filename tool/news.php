<?php
if(!defined('INDEX')) {
    header('location: /');
}

if(!access('show_tools_news')) {
    error(403);
}
include_class('twitter');
include_class('news');
include_class('bitly');
include_func('news');
?>
<h1>News administrator</h1>
<?php
switch($_GET['action']) {
    case 'edit':
    //Edit post
    $post = new News($_GET['id']);
    
    if($post->getTwitter()) {
        msg('Twitter post will not be updated!',0);
    }
    $nTitle = $post->getTitle();
    $nText = $post->getText();
    $title = 'Edit post';
    $submit = 'edit';
    case 'add':
    $title = ($title) ? $title : 'Add post';
    $submit = ($submit) ? $submit : 'add';
    addScript('news_form');
    ?>
<a href="/tools/news">List all news</a>
<h3><?=$title?></h3>
<form action="/tools/news" method="post">
<table>
    <?php
    if($_GET['action'] == 'add') {
    ?>
    <tr>
        <th colspan="2">First</th>
    </tr>
    <tr>
        <td>Select challenge</td>
        <td><select name="challenge">
            <option value="0">None</option>
<?php
            foreach(challenges() as $cid => $challenge) {
                ?>
            <option value="<?=$cid?>"><?=$challenge['name']?></option>
<?php
            }
            ?>
        </select></td>
    </tr>
    <tr>
        <th colspan="2">Second</th>
    </tr>
    <tr>
        <td>Preset</td>
        <td><select name="preset">
            <option value="0">None</option>
            <option value="1">New Challenge</option>
        </select></td>
    </tr>
    <tr>
        <th colspan="2">Third</th>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td>Title</td>
        <td><input type="text" name="title" value="<?=$nTitle?>" style="width: 500px;"></td>
    </tr>
    <tr>
        <td>Post</td>
        <td><textarea name="text" style="width: 500px; height: 300px;"><?=$nText?></textarea></td>
    </tr>
    <?php
    if($_GET['action'] == 'add') {
    ?>
    <tr>
        <th colspan="2">Twitter options</th>
    </tr>
    <tr>
        <td>Twitter</td>
        <td><input type="checkbox" name="twitter" value="on"> (only title will be posted)</td>
    </tr>
    <tr>
        <td>Link to </td>
        <td><input type="radio" name="link" value="none" checked="checked"> None<input type="radio" name="link" value="challenge" disabled="disabled"> Challenge <input type="radio" name="link" value="post"> Post</td>
    </tr>
    <tr>
        <td>Bit.ly url</td>
        <td><input type="checkbox" name="bitly" value="on" checked="checked"></td>
    </tr>
<?php
    } else {
    ?>
    <input type="hidden" name="id" value="<?=$_GET['id']?>">
<?php
    }
    ?>
    <tr>
        <td><input type="submit" name="submit_<?=$submit?>" value="<?=$title?>"></td>
        <td><?php if($_GET['action'] == 'add') { echo '<input type="reset" name="reset" value="Reset">'; }?></td>
    </tr>
</table>
</form>
    <?php
    break;
    //Delete
    case 'delete':
        $news = new News($_GET['id']);
        if($news->getId()) {
            if(isset($_GET['force'])) {
                $respons = $news->delete(true);
            } else {
                $respons = $news->delete();
            }
            if($respons) {
                msg('Post was deleted');
                news(true);
            }
        }
    default:
    $challenges = challenges();
    //Add
    if($_POST['submit_add']) {
        $news = new News;
        if(($_POST['twitter'] && empty($_POST['text'])) && $_POST['challenge'] != 0) {
            $text = '[url=http://'.DOMAIN.'/challenge/'.$challenges[$_POST['challenge']]['safename'].'/]'.$challenges[$_POST['challenge']]['name'].'![/url]';
        } else {
            $text = $_POST['text'];
        }
        switch($_POST['link']) {
            case 'post':
                $link = 1;
                break;
            case 'challenge':
                $link = 2;
                if($_POST['challenge'] != 0) {
                    break;
                }
            default:
            case 'none':
                $link = false;
                break;
        }
        $news->add($_POST['title'],$text,$_SESSION['id'],date('Y-m-d H:i:s'),$_POST['challenge'],$link,$_POST['twitter'],$_POST['bitly']);
    }
    //Edit
    if($_POST['submit_edit']) {
        print_r($_POST);
        $news = new News($_POST['id']);
        if($_POST['title'] != $news->getTitle()) {
            if($news->setTitle($_POST['title'])) {
                msg('Title is changed!');
            }
        }
        if($_POST['text'] != $news->getText()) {
            if($news->setText($_POST['text'])) {
                msg('Text is changed!');
            }
        }
        news(true);
    }
    unset($news);
    $news = news();
    ?>
<a href="?action=add">Add new</a>
<h3>All posts</h3>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tr class="row_header">
        <th>Title</th>
        <th colspan="3">Details</th>
        <th colspan="2">Options</th>
    </tr>
    <tr class="row_subheader">
        <td>&nbsp;</td>
        <th>Bit.ly address</th>
        <th>Twitter id</th>
        <th>Challenge</th>
        <td colspan="2">&nbsp;</td>
    </tr>
    <?php
    rowClass(false);
    if(count($news)!=0) {
        foreach($news as $postid => $new) {
            $challenge_id = $new['challenge_id'];
            if($challenges[$challenge_id]) {
                $challenge = $challenges[$challenge_id]['name'];
            } else {
                $challenge = 'No challenge related';
            }
            ?>
    <tr class="<?=rowClass(2)?>">
        <td><?=$new['title']?></td>
        <td style="text-align: center;"><?=($new['bitly']) ? $new['bitly'] : 'No bit.ly address'?></td>
        <td style="text-align: center;"><?=($new['twitter']) ? $new['twitter'] : 'No twitter post'?></td>
        <td style="text-align: center;"><?=$challenge?></td>
        <td style="text-align: center;"><a href="?action=edit&id=<?=$postid?>">Edit</a></td>
        <td style="text-align: center;"><a href="?action=delete&id=<?=$postid?>">Delete</a></td>
    </tr>
            <?php
        }
    } else {
        ?>
    <tr class="<?=rowClass(2)?>">
        <th colspan="5">No news</th>
    </tr>
        <?php
    }
    ?>
</table>
    <?php
    
    break;
}
show_page('News administration');
