<?php

if(!defined('INDEX')) {
    header('location: /');
}

if(!include_func('forum') || !include_func('user') || !include_func('bbcode')) {
    error();
}

// Deletes topics and replies
if (access('forum_delete_post')) {
    if (isset($_POST['delete_topic'])) {
        forumDeleteTopic($_GET['value'][1]);
        $mem = new cache;
        $mem->delete('ForumTopics_' . $_GET['value'][1]);
        $mem->close();
    }
    if (isset($_POST['delete_reply'])) {
        forumDeleteReply($_POST['reply_id']);
        $mem = new cache;
        $mem->delete('ForumReplies_' . $_GET['value'][1]);
        $mem->close();
    }
}

// Add reply
// $_GET['value'][1] = topic id
if (isset($_POST['message_submit'])) {
    if (!empty($_POST['message'])) {
    $cat = getCategoryFromTopic($_GET['value'][1]);
        postForumReply($_SESSION['id'], $_GET['value'][1], $cat['id'], $_POST['message']);
        $mem = new cache;
        $mem->delete('ForumReplies_' . $_GET['value'][1]);
        $mem->close();
    }
}

// Add topic
// $_GET['value'][1] = cat id
if (isset($_POST['topic_submit'])) {
    if (!isForumCatLocked($_GET['value'][1])) {
        if (!empty($_POST['topic_message'])) {
            postForumTopic($_SESSION['id'], $_GET['value'][1], $_POST['topic'], $_POST['topic_message']);
            $mem = new cache;
            $mem->delete('ForumTopics_' . $_GET['value'][1]);
            $mem->close();
        }
    } else {
        error(0, 'Error', 'Forum category is locked');
    }
}


// Edit reply
if (access('forum_edit_post') || $_SESSION['id'] == $row['user_id']) {
    if (isset($_POST['edit_reply_submit'])) {
        forumEditReply($_POST['reply_id'], $_POST['edit_reply']);
        $mem = new cache;
        $mem->delete('ForumReplies_' . $_GET['value'][1]);
        $mem->close();
    }
    if (isset($_POST['edit_topic_submit'])) {
        forumEditTopic($_GET['value'][1], $_POST['edit_topic']);
        $mem = new cache;
        $mem->delete('ForumTopics_' . $_GET['value'][1]);
        $mem->close();
    }
}

// Show list of categories
if (empty($_GET['value'][0]) && empty($_GET['value'][1])) {
?>
<table cellspacing="6" cellpadding="0" style="border:0px; width:100%;">
    <tr>
        <td style="font-weight:bold;">Categories</td>
        <td style="font-weight:bold;">Total topics</td>
        <td style="font-weight:bold;">Total replies</td>
    </tr>
<?php
    $cats = getForumCategories();
    for ($i = 0; $i <= count($cats); $i++) {
        if ($cats[$i]['id'] == 5)
            unset($cats[$i]);
    }
    foreach ($cats as $row) {
?>
    <tr>
        <td><a href="/forum/cat/<?=$row['id']?>"><?=$row['category']?></a><br />
        <span style="font-size:10px;"><?=$row['discription']?></span></td>
        <td><?=getCountCategoryTopics($row['id'])?></td>
        <td><?=getCountCategoryReplies($row['id'])?></td>
    </tr>
<?php
    }
?>
</table>
<?php
}
// Show list of topics
elseif ($_GET['value'][0] == 'cat' && is_numeric($_GET['value'][1])) {
    $cat = getCategoryFromTopic($_GET['value'][1]);
?>
    <!--<a href="/forum">Forum</a> -> <a href="/forum/cat/<?$cat['id']?>"><?=$cat['name']?></a><br /><br /> Under testing-->
<table cellspacing="0" cellpadding="0" style="border:1px solid #000000; width:100%;">
    <tr class="row_header">
        <td style="font-weight:bold; text-align:left;">Topic</td>
        <td style="font-weight:bold; text-align:left;">By</td>
        <td style="font-weight:bold; text-align:left;">Replies</td>
        <td style="font-weight:bold; text-align:right;">Created</td>
    </tr>
<?php
    rowClass(false);
    foreach (getForumTopics($_GET['value'][1]) as $row) {
?>
    <tr class="<?=rowClass(2)?>">
        <td style="text-align:left;"><a href="/forum/topic/<?=$row['id']?>"><?=$row['topic']?></a></td>
        <td style="text-align:left;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',getCountryFromUserid($row['user_id']))?>.png" alt="" title="<?=getCountryFromUserid($row['user_id'])?>" /> <a href="/user/<?=getUsernameFromId($row['user_id'])?>"><?=getUsernameFromId($row['user_id'])?></a></td>
        <td style="text-algin:left;"><?=getCountTopicReplies($row['id'])?></td>
        <td style="text-align:right;"><?=showDateTime($row['timestamp'])?></td>
    </tr>
<?php
    }
?>
</table>
<?php
    if (isset($_SESSION['id'])) {
?>
<div style="padding:10px; margin-top:20px;">
    <form action="" method="post">
        Topic:<br />
        <input style="border:1px solid #000000;" name="topic" type="text" size="70" /><br />
        Content:<br />
        <textarea style="border:1px solid #000000;" name="topic_message" rows="10" cols="70"></textarea><br />
        <input name="topic_submit" type="submit" value="post" /> [<a href="/bbcodes.html">BBcodes are enabled</a>]
    </form>
</div>
<?php
    }
}
// Show current topic
elseif ($_GET['value'][0] == 'topic' && is_numeric($_GET['value'][1])) {
    $cat = getCategoryFromTopic($_GET['value'][1]);
    $row = getForumTopics($cat['id'], $_GET['value'][1]);
    
?>
<form action="" method="post">
<a href="/forum/">Forum</a> -> <a href="/forum/cat/<?=$cat['id']?>"><?=$cat['name']?></a> ->
<a href="/forum/topic/<?=$_GET['value'][1]?>"><?=$row[0]['topic']?></a><br /><br />
<h2 style="text-align:center;">
    <?php
    print nl2br(htmlspecialchars($row[0]['topic']));
    if (access('forum_edit_post') || $_SESSION['id'] == $row[0]['user_id']) {
    ?>
        <input class="spoiler" name="edit_<?=$row[0]['id']?>" type="button" style="color:#0000FF;"  value="Edit" />
    <?php
    }
    if (access('forum_delete_post') || $_SESSION['id'] == $row[0]['user_id']) {
    ?>
        <input name="delete_topic" type="submit" value="Del" style="color:#FF0000;" />
    <?php
    }
    ?>
</h2>

<table cellspacing="0" cellspacing="0" style="border:1px solid #000000; border-collapse:collapse; width:100%;">
    <tr class="row_header" style="border:1px solid #000000;">
<?php
if($avatar = getUserAvatar($row[0]['user_id'],100)) {
?>
        <td rowspan="2" style="width:100px; padding: 5px;vertical-align: top;"><img style="border: 1px solid black;" src="<?=$avatar?>"></td>
<?php
}
?>
        <td style="text-align:left; height: 14px;"><a href="/user/<?=getUsernameFromId($row[0]['user_id'])?>"><?=getUsernameFromId($row[0]['user_id'])?></a></td>
        <td style="text-align:right;"><?=showDateTime($row[0]['timestamp'])?></td>
    </tr>
    <tr>
       <td colspan="2" style="background-color:#FFFFFF;">
        <?php
        print bbcode($row[0]['text']);
        if (access('forum_edit_post') || $_SESSION['id'] == $row[0]['user_id']) {
        ?>
            <div id="show_edit_topic" name="spoiler_edit_<?=$row[0]['id']?>" style="display:none;">
                <textarea name="edit_topic" rows="5" cols="100"><?=utf8_encode($row[0]['text'])?></textarea>
                <input name="edit_topic_submit" type="submit" style="color:#00FF00;" value="Save" />
            </div>
        <?php
        }
        ?>
        </td>
    </tr>
</table>
</form>
<?php
    foreach (getForumReplies($_GET['value'][1]) as $row) {
        // Edit replies
       /* if (access('forum_edit_post') || $_SESSION['id'] == $row['user_id']) {
            if (isset($_POST['edit_reply_submit'])) {
                forumEditReply($_POST['reply_id'], $_POST['edit_reply']);
            }
        }*/
?>
<form action="" method="post">
<input name="reply_id" type="hidden" value="<?=$row['id']?>" />
<table cellspacing="0" cellspacing="0" style="border:1px solid #000000; border-collapse:collapse; width:100%; margin-top:5px;">
    <tr class="row_header" style="border:1px solid #000000;">
<?php
if($avatar = getUserAvatar($row['user_id'],100)) {
?>
        <td rowspan="2" style="width:100px; padding: 5px;vertical-align: top;"><img style="border: 1px solid black;" src="<?=$avatar?>"></td>
<?php
}
?>
        <td style="text-align:left; height: 14px;"><a href="/user/<?=getUsernameFromId($row['user_id'])?>"><?=getUsernameFromId($row['user_id'])?></a>
        <?php
            if (access('forum_edit_post') || $_SESSION['id'] == $row['user_id']) {
        ?>
            <input class="spoiler" name="edit_<?=$row['id']?>" type="button" style="color:#0000FF; height:16px; font-size:9px;" value="Edit" />
        <?php
        }
        if (access('forum_delete_post') || $_SESSION['id'] == $row['user_id']) {
        ?>
            <input name="delete_reply" type="submit" value="Del" style="color:#FF0000; height:16px; font-size:9px;" />
        <?php
        }
        ?>
    </td>
        <td style="text-align:right;"><?=showDateTime($row['timestamp'])?></td>
    </tr>
    <tr>
       <td colspan="2" style="background-color:#FFFFFF;">
        <?php
        print bbcode($row['text']);
        if (access('forum_edit_post') || $_SESSION['id'] == $row['user_id']) {
        ?>
            <div name="spoiler_edit_<?=$row['id']?>" style="display:none;">
                <textarea name="edit_reply" rows="5" cols="100"><?=utf8_encode(stripslashes($row['text']))?></textarea>
                <input name="edit_reply_submit" type="submit" style="color:#00FF00;" value="Save" />
            </div>
        <?php
        }
        ?>
        </td>
    </tr>
</table>
</form>
<?php
    }
    if (isset($_SESSION['id'])) {
?>
<div style="padding:10px; margin-top:20px;">
    <form action="" method="post">
        <textarea style="border:1px solid #000000;" name="message" rows="10" cols="70"></textarea><br />
        <input name="message_submit" type="submit" value="post" /> [<a href="/bbcodes.html">BBcodes are enabled</a>]
    </form>
</div>
<?php
    }
}

show_page('Forum');
