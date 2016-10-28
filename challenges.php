<?php
if (!defined('INDEX')) {
    header('location: /');
}

header('Content-Type: text/html; charset=ISO-8859-1');

$PDO =&DB::$PDO;

if ($_GET['value'][0]) {
    show_right();
}

include_func('forum');
include_func('bbcode');

switch ($_GET['p']) {
    //Challenge
    case 'challenge':
        if(check_page('challenge_'.$_GET['value'][0])) {
            show_page('','challenge_'.$_GET['value'][0]);
            exit;
        }
        $challenges = challenges();
        $challenge_id = getChallengeIdFromSafename($_GET['value'][0]);
        if (!isChallenge($challenge_id)) {
            error(404);
            exit;
        }
        addScript('rate');
        $challenge_name = getChallengeName($challenge_id);
        $challenge_ins = $challenges[$challenge_id]['instructions'];
        $challenge_output = $challenges[$challenge_id]['output'];
        $challenge_input = $challenges[$challenge_id]['input'];
        $challenge_type = $challenges[$challenge_id]['type'];
        $challenge_enddate = $challenges[$challenge_id]['enddate'];
        $challenge_input = $challenges[$challenge_id]['input'];
        $challenge_topic_id = $challenges[$challenge_id]['topic_id'];
        $challenge_output = $challenges[$challenge_id]['output'];
        $challenge_constant = $challenges[$challenge_id]['constant'];
        $challenge_disabled_func = $challenges[$challenge_id]['disabled_func'];
        $challenge_enddate = $challenges[$challenge_id]['enddate'];
        $challenge_active = ($challenges[$challenge_id]['active']? 'active':'inactive');
        $challenge_output_type = $challenges[$challenge_id]['output_type'];
        $challenge_ups = $challenges[$challenge_id]['ups'];
        $challenge_downs = $challenges[$challenge_id]['downs'];
        $challenge_rating = $challenge_ups - $challenge_downs;

        switch ($challenges[$challenge_id]['trim_type']) {
            case 0: $challenge_trim_type = 'No trim'; break;
            case 1: $challenge_trim_type = 'Right trim'; break;
            case 2: $challenge_trim_type = 'Left trim'; break;
            case 3: $challenge_trim_type = 'Full trim'; break;
        }

        if (empty($challenge_ins)) {
            error(0,'Error loading challenge','The was an error loading the challenge.<br>If the problem continue, please contact an admins.');
            exit;
        }
        //Upload
        if (access('challenges_upload') && isset($_POST['submit_attempt'])) {
            attempt($_FILES['phpfile'], $challenge_id, $_SESSION['id']);
        }

        // Add reply
        if (isset($_POST['message_submit'])) {
            if (!empty($_POST['message'])) {
                postForumReply($_SESSION['id'], $challenge_topic_id, 5, $_POST['message']);
                $mem = new cache;
                $mem->delete('ForumReplies_' . $challenge_topic_id);
                $mem->close();
            }
        }

        ?>
    <h1>Challenge: <?=$challenge_name?></h1>
    <h3>Challenge information</h3>
    <table style="text-align: left">
        <tr>
            <th style="width:150px; text-align:left;">Challenge Type</th>
            <td><?=htmlspecialchars(ucfirst($challenge_type))?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Challenge Status</th>
            <td>
            <?php
            if (isChallengeOpen($challenge_id)) {
                print 'Open';
            } else {
                print 'Closed';
            }
            ?>
            </td>
        </tr>
        <?php
        $time = strtotime($challenge_enddate);
        if ($challenge_type == 'protected' && is_int($time)) {
        ?>
        <tr>
            <th style="text-align:left;">End date</th>
            <td <?=(isChallengeOpen($challenge_id)? '' : 'style="color:#FF0000;"')?>><?=showDate($time)?></td>
        </tr>
        <?php
        }
        if (!empty($challenge_constant)) {
        ?>
        <tr>
            <th style="text-align:left;">Constant name</th>
            <td><?=$challenge_constant?></td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <th style="text-align:left;">Trim method</th>
            <td><?=$challenge_trim_type?></td>
        </tr>
        <?php
        if (!empty($challenge_disabled_func)) {
        ?>
        <tr>
            <th style="text-align:left;">Disabled functions</th>
            <td>
            <?php 
            foreach (explode(',', $challenge_disabled_func) as $func) {
            ?>
                <a href="http://php.net/<?=htmlspecialchars($func)?>"><?=htmlspecialchars($func)?></a>
            <?php
            }
            ?>
            </td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <th style="text-align:left;">Rating</th>
            <td>[ <a href="#" onclick="rate('up')">up</a> ] 
            <?php
            print $challenge_rating >= 0? '<span id="rating" style="color:#21852D;">' : '<span id="rating" style="color:#FF0000;">';
            ?></span> [ <a href="#" onclick="rate('down')">down</a> ]
            </td>
        </tr>
    </table>
    <h3>Instructions</h3>
    <pre><?=$challenge_ins?></pre>

    <h3>Example</h3>
<?php
if (!empty($challenge_input)) {
?>
    <a class="spoiler" name="input">Show/hide Input</a>
    <div class="example_box_outer">
        <div class="example_box_inner" name="spoiler_input"<?=(strlen($challenge_input) > 200) ? '' : ' style="display: block;"'?>>
            <pre><?=htmlspecialchars($challenge_input)?></pre>
        </div>
    </div>
<?php
}
if (!empty($challenge_output)) {
?>
    <a class="spoiler" name="output">Show/hide Output</a>
    <div class="example_box_outer">
        <div class="example_box_inner" name="spoiler_output"<?=(strlen($challenge_output) > 200) ? '' : ' style="display: block;"'?>>
            <pre><?=htmlspecialchars($challenge_output)?></pre>
        </div>
    </div>
<?php
}
if (!empty($challenge_output) && $challenge_output_type=='static') {
?>
    <h3>MD5 checksum:</h3>
    <div class="example_box_outer">
    <pre style="margin-top:-10px;margin-bottom: -1px;"><?=md5($challenge_output)?></pre>
    </div>
<?php
}
if (access('challenges_upload') && isChallengeOpen($challenge_id)) {
?>
    <h3>Upload php-file:</h3>
    <div style="background-color:#CCCCCC; color:#000000;border:1px solid #000000; padding-top:10px;">
        <form action="" method="post" enctype="multipart/form-data">
            <input name="phpfile" type="file"><br />
            <input name="submit_attempt" type="submit" value="Upload">
        </form>
    </div>
<?php
}
?>
    <h3>Comments</h3>
    <a class="spoiler" name="comments">Show/hide comments (<?=getCountTopicReplies($challenge_topic_id)?>)</a>
    <div style="background-color:#CCCCCC; color:#000000;border:1px solid #000000; padding-top:10px;">
        <div name="spoiler_comments" style="display:none;">
            <?php
            foreach (getForumReplies($challenge_topic_id) as $row) {
                // Edit reply NEEDS TO BE HERE BECAUSE OF THE NEED FOR THE USER ID
                if (access('forum_edit_post') || $_SESSION['id'] == $row['user_id']) {
                    if (isset($_POST['edit_reply_submit'])) {
                        forumEditReply($_POST['reply_id'], $_POST['edit_reply']);
                        $mem = new cache;
                        $mem->delete('ForumReplies_' . $challenge_topic_id);
                        $mem->close();
                    }
                }
                // Deletes topics and replies
                if (access('forum_delete_post') || $_SESSION['id'] == $row['user_id']) {
                    if (isset($_POST['delete_reply'])) {
                        forumDeleteReply($_POST['reply_id']);
                        $mem = new cache;
                        $mem->delete('ForumReplies_' . $challenge_topic_id);
                        $mem->close();
                    }
                }

            ?>
            <form action="" method="post">
            <input name="reply_id" type="hidden" value="<?=$row['id']?>" />
            <table cellspacing="0" cellspacing="0" style="border:1px solid #000000; border-collapse:collapse; width:100%; margin-top:5px;">
                <tr class="row_header" style="border:1px solid #000000;">
            <?php
                if($avatar = getUserAvatar($row['user_id'],60)) {
            ?>
                    <td rowspan="2" style="width:60px; padding: 5px;vertical-align: top;"><img style="border: 1px solid black;" src="<?=$avatar?>"></td>
            <?php
                } else {
            ?>
                    <td rowspan="2" style="width:60px; padding: 5px;vertical-align: top;"><img style="border: 1px solid black; width:60px; height:60px;" src="../gfx/profile_default.jpg"></td>
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
                            <textarea name="edit_reply" rows="3" cols="60"><?=utf8_encode(stripslashes($row['text']))?></textarea>
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
                <textarea style="border:1px solid #000000;" name="message" rows="5" cols="70"></textarea><br />
                <input name="message_submit" type="submit" value="post" /> [<a href="/bbcodes.html">BBcodes are enabled</a>]
            </form>
        </div>
        <?php
        }
        ?>
        </div>
    </div>

<?php
    // Show submissions if the challenge is public or protected if the challenge is closed by end date
if ($challenge_type == 'public' || access('view_code') || ($challenge_type == 'protected' && strtotime($challenge_enddate) < time())) {
    $sql = 'SELECT a.id,a.time,a.code as code,a.size,a.user_id,a.input,a.version,a.passed,a.executed,a.challenge_id,c.name,u.username
            FROM attempts a, challenges c, users u
            WHERE a.passed = \'1\' AND a.challenge_id = c.id AND u.id = a.user_id AND c.id = :challenge_id ORDER BY a.size LIMIT 50';
    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(':challenge_id' => $challenge_id));
    $i = 0;
    print "<h3>Submissions</h3>\n";
    foreach ($stmt->fetchAll() as $row) {
    ?>
        <table cellspacing="0" cellpadding="0" style="width:100%; margin-top:10px;">
            <tr>
                <td class="row_header" style="font-size:11px;padding-left:5px;"><?=$row['size']?> bytes (ver. <?=$row['version']?>) by <a href="/users/<?=$row['username']?>"><?=$row['username']?></a></td>
                <td class="row_header" style="text-align:right;font-size:11px;padding-right:5px;">
                    <a class="spoiler" name="code_<?=$i?>" style="cursor: pointer;">Show / Hide</a>
                </td>
            </tr>
            <tr>
                <td colspan="2" name="spoiler_code_<?=$i?>" style="overflow:auto;display:none;text-align:left;border:1px solid #000;background-color:#CCC;">
                <?highlight_string($row['code']);?>
                </td>
            </tr>
        </table>
    <?php
        $i++;
    }
}
?>
        <?php
        show_page($challenge_name.' challenge','challenge_'.$_GET['value'][0]);
        break;
    //List challenges
    case 'challenges':
    if (check_page('challenges')) {
        show_page('Challenges','challenges');
        exit;
    }
    default:
    ?>
    <h1>Private Challenges</h1>
    Only you can see your submissions. Points from these challenges is what counts on site ranking.
    <table cellpadding="1" cellspacing="0" style="width: 100%;">
        <tr class="row_header">
            <th style="text-align:left;">Name</th>
            <th style="text-align:left;">Type</th>
            <th style="text-align:left;">Status</th>
            <?php//<th style="text-align:right;">Rating</th>?>
            <th style="text-align:left;">Leader</th>
            <th style="text-align:left;">Bytes</th>
            <th style="text-align:left;">Comments</td>
            <th style="text-align:right;">End date</th>
        </tr>
        <?php
        rowClass(false);
        if(
            is_object(
                $oStmt = DB::$PDO->prepare(
                    'SELECT
                      a.username,
                      a.country,
                      a.size,
                      c.name,
                      c.type,
                      c.active,
                      (c.open AND ((enddate IS NULL) OR (enddate > NOW()))) open,
                      c.enddate,
                      (SELECT
                        COUNT(1)
                      FROM
                        forum_replies
                      WHERE
                        (topic_id=c.topic_id)
                      ) replycount
                    FROM
                      challenges c
                    LEFT JOIN
                      (
                        SELECT
                          b.challenge_id,
                          a.size,
                          u.username,
                          u.country
                        FROM
                          (
                            SELECT
                              MIN(size)            len,
                              challenge_id
                            FROM
                              attempts
                            WHERE
                              passed
                            GROUP BY
                              2
                          ) b
                        INNER JOIN
                          attempts a
                        ON
                          ((a.challenge_id=b.challenge_id) AND a.passed AND (a.size=b.len))
                        INNER JOIN
                          users u
                        ON
                          (u.id=a.user_id)
                        WHERE
                            a.passed
                          AND
                            NOT EXISTS(
                              SELECT
                                1
                              FROM
                                attempts
                              WHERE
                                  passed
                                AND
                                  (challenge_id=a.challenge_id)
                                AND
                                  (size=a.size)
                                AND
                                  (
                                    (time<a.time)
                                  OR
                                    (
                                      (time=a.time)
                                    AND
                                       (id<a.id)
                                    )
                                 )
                            )
                      ) a
                    ON
                      (a.challenge_id=c.id)
                    WHERE
                      (
                          c.type="private"
                        AND
                          (c.active
                        OR
                          :AllChallengeViewRight)
                      )
                    ORDER BY
                      c.id DESC'
                )
            )
        &&
            $oStmt->execute(
                Array(
                    ':AllChallengeViewRight' => (access('show_unactive_challenges')? 1 : 0)
                )
            )
        ){

            while(is_array($challenge = $oStmt->fetch(PDO::FETCH_ASSOC))){
                $status = $challenge['open'] ? '<img src="gfx/icons/open.png" alt="" /> Open' : '<img src="gfx/icons/closed.png" alt="" /> Closed';
                if ($challenge['type'] == 'private') {
                    $type = '<img src="gfx/icons/private.png" title="Private" alt="" /> Private';
                } elseif($challenge['type'] == 'public') {
                    $type = '<img src="gfx/icons/public.png" title="Public" alt="" /> Public';
                } else {
                    $type = '<img src="gfx/icons/protected.png" title="Protected" alt="" /> Protected';
                }
                ?>
        <tr class="<?=rowClass(2)?>" style="font-size:10px;">
            <td style="text-align:left;"><a href="/challenge/<?=preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$challenge['name']))?>"><?=$challenge['name']?></a></td>
            <td style="text-align:left;"><?=$type?></td>
            <td style="text-align:left;"><?=$status?><?=(!$challenge['active']) ? ' (Inactive)': ''?></td>
            <?php//<td style="text-align:right;"><?=$challenge['ups']-$challenge['downs']?>
            <td style="text-align:left;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',$challenge['country'])?>.png" alt="" title="<?=$challenge['country']?>" />
            <a href="/user/<?=$challenge['username']?>"><?=$challenge['username']?></a></td>
            <td style="text-align:left;"><?=$challenge['size']?></td>
            <td style="text-align:left;"><?=$challenge['replycount']?></td>
            <td style="text-align:right;"><?=($challenge['enddate']? showDate($challenge['enddate']) : '&infin;')?></a>
        </tr>
                <?php
            }
        }
        ?>
    </table>
<h1>Public Challenges</h1>
Other users may see your submissions, but points from these challenges does not count on your total site rank.<br />
    <table cellpadding="1" cellspacing="0" style="width: 100%;">
        <tr class="row_header">
            <th style="text-align:left;">Name</th>
            <th style="text-align:left;">Type</th>
            <th style="text-align:left;">Status</th>
            <?php//<th style="text-align:right;">Rating</th>?>
            <th style="text-align:left;">Leader</th>
            <th style="text-align:left;">Bytes</th>
            <th style="text-align:left;">Comments</td>
            <th style="text-align:right;">End date</th>
        </tr>
        <?php
        rowClass(false);
        if(
            is_object(
                $oStmt = DB::$PDO->prepare(
                    'SELECT
                      a.username,
                      a.country,
                      a.size,
                      c.name,
                      c.type,
                      c.active,
                      (c.open AND ((enddate IS NULL) OR (enddate > NOW()))) open,
                      c.enddate,
                      (SELECT
                        COUNT(1)
                      FROM
                        forum_replies
                      WHERE
                        (topic_id=c.topic_id)
                      ) replycount
                    FROM
                      challenges c
                    LEFT JOIN
                      (
                        SELECT
                          b.challenge_id,
                          a.size,
                          u.username,
                          u.country
                        FROM
                          (
                            SELECT
                              MIN(size)            len,
                              challenge_id
                            FROM
                              attempts
                            WHERE
                              passed
                            GROUP BY
                              2
                          ) b
                        INNER JOIN
                          attempts a
                        ON
                          ((a.challenge_id=b.challenge_id) AND a.passed AND (a.size=b.len))
                        INNER JOIN
                          users u
                        ON
                          (u.id=a.user_id)
                        WHERE
                            a.passed
                          AND
                            NOT EXISTS(
                              SELECT
                                1
                              FROM
                                attempts
                              WHERE
                                  passed
                                AND
                                  (challenge_id=a.challenge_id)
                                AND
                                  (size=a.size)
                                AND
                                  (
                                    (time<a.time)
                                  OR
                                    (
                                      (time=a.time)
                                    AND
                                       (id<a.id)
                                    )
                                 )
                            )
                      ) a
                    ON
                      (a.challenge_id=c.id)
                    WHERE
                      (
                          c.type="public"
                        AND
                          (c.active
                        OR
                          :AllChallengeViewRight)
                      )
                    ORDER BY
                      c.id DESC'
                )
            )
        &&
            $oStmt->execute(
                Array(
                    ':AllChallengeViewRight' => (access('show_unactive_challenges')? 1 : 0)
                )
            )
        ){

            while(is_array($challenge = $oStmt->fetch(PDO::FETCH_ASSOC))){
                $status = $challenge['open'] ? '<img src="gfx/icons/open.png" alt="" /> Open' : '<img src="gfx/icons/closed.png" alt="" /> Closed';
                if ($challenge['type'] == 'private') {
                    $type = '<img src="gfx/icons/private.png" title="Private" alt="" /> Private';
                } elseif($challenge['type'] == 'public') {
                    $type = '<img src="gfx/icons/public.png" title="Public" alt="" /> Public';
                } else {
                    $type = '<img src="gfx/icons/protected.png" title="Protected" alt="" /> Protected';
                }
                ?>
        <tr class="<?=rowClass(2)?>" style="font-size:10px;">
            <td style="text-align:left;"><a href="/challenge/<?=preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$challenge['name']))?>"><?=$challenge['name']?></a></td>
            <td style="text-align:left;"><?=$type?></td>
            <td style="text-align:left;"><?=$status?><?=(!$challenge['active']) ? ' (Inactive)': ''?></td>
            <?php//<td style="text-align:right;"><?=$challenge['ups']-$challenge['downs']?>
            <td style="text-align:left;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',$challenge['country'])?>.png" alt="" title="<?=$challenge['country']?>" />
            <a href="/user/<?=$challenge['username']?>"><?=$challenge['username']?></a></td>
            <td style="text-align:left;"><?=$challenge['size']?></td>
            <td style="text-align:left;"><?=$challenge['replycount']?></td>
            <td style="text-align:right;"><?=($challenge['enddate']? showDate($challenge['enddate']) : '&infin;')?></a>
        </tr>
                <?php
            }
        }
        ?>
    </table>
        <?php
        show_page('Challenges','challenges');
    break;
}
