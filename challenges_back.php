<?php
if (!defined('INDEX')) {
    header('location: /');
}

header('Content-Type: text/html; charset=ISO-8859-1');

$PDO =&DB::$PDO;

show_right();

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
        ?>
    <h1>Challenge: <?=$challenge_name?></h1>
    <h3>Challenge information</h3>
    <table style="text-align: left">
        <tr>
            <th style="width:150px;">Challenge Type</th>
            <td><?=htmlspecialchars(ucfirst($challenge_type))?></td>
        </tr>
        <tr>
            <th>Challenge Status</th>
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
            <th>End date</th>
            <td <?=(isChallengeOpen($challenge_id)? '' : 'style="color:#FF0000;"')?>><?=showDate($time)?></td>
        </tr>
        <?php
        }
        if (!empty($challenge_constant)) {
        ?>
        <tr>
            <th>Constant name</th>
            <td><?=$challenge_constant?></td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <th>Trim method</th>
            <td><?=$challenge_trim_type?></td>
        </tr>
        <?php
        if (!empty($challenge_disabled_func)) {
        ?>
        <tr>
            <th>Disabled functions</th>
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
            <th>Rating</th>
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
    // Show submissions if the challenge is public or protected if the challenge is closed by end date
    if ($challenge_type == 'public' || access('view_code') || ($challenge_type == 'protected' && strtotime($challenge_enddate) < time())) {
        $sql = 'SELECT a.id,a.time,a.code as code,a.size,a.user_id,a.input,a.version,a.passed,a.executed,a.challenge_id,c.name,u.username
                FROM attempts a, challenges c, users u
                WHERE a.passed = \'1\' AND a.challenge_id = c.id AND u.id = a.user_id AND c.id = :challenge_id ORDER BY a.size LIMIT 50';
        $stmt = $PDO->prepare($sql);
        $stmt->execute(array(':challenge_id' => $challenge_id));
        $i = 0;
        foreach ($stmt->fetchAll() as $row) {
        ?>
            <table cellspacing="0" cellpadding="0" style="width:100%; margin-top:10px;">
                <tr>
                    <td class="row_header" style="font-size:11px;padding-left:5px;">
                        <?=$row['size']?> bytes (ver. <?=$row['version']?>)
                        by <a href="/users/<?=$row['username']?>"><?=$row['username']?></a> 
                    </td>
                    <td class="row_header" style="text-align:right;font-size:11px;padding-right:5px;">
                        <a class="spoiler" name="code_<?=$i?>" style="cursor: pointer;">Show / Hide</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" name="spoiler_code_<?=$i?>" style="overflow:scroll;display:none;text-align:left;border:1px solid #000;background-color:#CCC;">
                        <?highlight_string($row['code'])?>
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
    <h1>Challenges</h1>
    <table cellpadding="1" cellspacing="0" style="width: 100%;">
        <tr class="row_header">
            <th style="text-align:left;">Challenge</th>
            <th style="text-align:left;">Type</th>
            <th style="text-align:left;">Status</th>
            <?php//<th style="text-align:right;">Rating</th>?>
            <th style="text-align:right;">End date</th>
        </tr>
        <?php
        rowClass(false);
        foreach (challenges() as $cid => $challenge) {
            $status = isChallengeOpen($cid) ? '<img src="gfx/icons/open.png" alt="" /> Open' : '<img src="gfx/icons/closed.png" alt="" /> Closed';
            if ($challenge['type'] == 'private') {
                $type = '<img src="gfx/icons/private.png" title="Private" alt="" /> Private';
            } elseif($challenge['type'] == 'public') {
                $type = '<img src="gfx/icons/public.png" title="Public" alt="" /> Public';
            } else {
                $type = 'Protected';
            }
        ?>
        <tr class="<?=rowClass(2)?>">
            <td><a href="/challenge/<?=$challenge['safename']?>"><?=$challenge['name']?></a></td>
            <td><?=$type?></td>
            <td><?=$status?><?=(!$challenge['active']) ? ' (Inactive)': ''?></td>
            <?php//<td style="text-align:right;"><?=$challenge['ups']-$challenge['downs']?>
            <td style="text-align:right;"><?=($challenge['enddate']? showDate($challenge['enddate']) : '&infin;')?></a>
        </tr>
        <?php
        }
        ?>
    </table>
        <?php
        show_page('Challenges','challenges');
    break;
}
