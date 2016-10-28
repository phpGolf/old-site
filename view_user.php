<?php

if (is_numeric($_GET['value'][1])) {
    if (!getUsernameFromId($_GET['value'][1])) {
        error(0, 'Not found', 'User not found in the database');
    }
} else {
    if (!getIdFromUsername($_GET['value'][1])) {
        error(0, 'Not found', 'User not found in the database');
    }
}

$mem = new cache;
$sql = 'SELECT a.id,a.time,a.size,a.user_id,
IF(LENGTH(a.errors) >0,1,0) AS errors,
IF(LENGTH(a.input) >0,1,0) AS input,
a.version,a.passed,a.executed,a.challenge_id,c.name,u.username
        FROM attempts a, challenges c, users u
        WHERE a.challenge_id = c.id AND u.id = a.user_id AND u.username LIKE :username ORDER BY ';
        
switch ($_GET['value'][2]) {
    default:
        $mem->key = 'AllAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'a.challenge_id ASC';
        break;
    case 'latest':
        $mem->key = 'LatestAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'a.id DESC';
    break;
    case 'bytes':
        $mem->key = 'BytesAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'a.size ASC';
    break;
    case 'username':
        $mem->key = 'UsernameAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'u.username ASC';
    break;
    case 'challenge':
        $mem->key = 'ChallengeAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'c.name ASC';
    break;
    case 'version':
        $mem->key = 'VersionAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'a.version ASC';
    break;
    case 'result':
        $mem->key = 'ResultAtt_' . (!empty($_GET['value'][2])? $_GET['value'][2] : $_GET['value'][1]);
        $sql .= 'a.passed DESC';
    break;
}

$sql .= ', a.version DESC';

if (!$data = $mem->get()) {
    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(':username' => $_GET['value'][1]));
    $result = $stmt->fetchAll();
    $mem->set(0,$result,MEMCACHE_COMPRESSED,3600);
} else {
    $result = $data;
}

?>

<h1>Submissions for <a href="/user/<?=getUsernameFromId(getIdFromUsername($_GET['value'][1]))?>"><?=getUsernameFromId(getIdFromUsername($_GET['value'][1]))?></a></h1>
<table cellpadding="0" cellspacing="0" style="width:100%;font-size:10px;">
    <tr class="row_header">
        <td style="text-align:left;"><a href="/view/user/<?=$result[0]['username']?>/challenge">Challenge</a></td>
        <td style="text-align:left;"><a href="/view/user/<?=$result[0]['username']?>/bytes">Bytes</a></td>
        <td style="text-align:left;"><a href="/view/user/<?=$result[0]['username']?>/result">Result</a></td>
        <td style="text-align:left;"><a href="/view/user/<?=$result[0]['username']?>/version">Version</a></td>
        <td style="text-align:left;">Code</td>
        <td style="text-align:left;">Difference</td>
        <td style="text-align:left;">Input</td>
        <td style="text-align:left;">Errors</td>
        <td style="text-align:right;"><a href="/view/user/<?=$result[0]['username']?>/latest">Submitted</a></td>
    </tr>
    
<?php
rowClass(false);
foreach ($result as $row) {
    if($oldChallenge != $row['challenge_id']) {
        $oldChallenge = $row['challenge_id'];
?>
    <tr class="row_subheader">
        <td colspan="9"><a class="spoiler" name="list_<?=getSafenameFromId($row['challenge_id'])?>"><?=$row['name']?></a></td>
    </tr>
<?php
    }
?>
    <tr class="<?=rowClass(2)?>" name="spoiler_list_<?=getSafenameFromId($row['challenge_id'])?>" style="display: none;">
        <td style="text-align:left;"><a href="/challenge/<?=getSafenameFromId($row['challenge_id'])?>"><?=$row['name']?></a></td>
        <td style="text-align:left;"><?=$row['size']?></td>
        <td style="text-align:left;">
    <?php 
    if ($row['executed'] == 0) {
    ?>
            <img src="<?=GFX?>icons/pending.png" alt="" title="Pending" />
    <?php
    } else {
        if($row['passed']) {
        ?> 
            <img src="<?=GFX?>icons/valid.png" alt="" title="valid" />
        <?php 
        } else {
        ?>
            <img src="<?=GFX?>icons/invalid.png" alt="" title="invalid" />
    <?php 
        }
    }
    ?>
        </td>
        <td style="text-align:left;"><?=$row['version']?></td>
        <td style="text-align:left;"><a href="/view/code/<?=$row['id']?>">View</a></td>
        <td style="text-align:left;"><?=($row['passed']? '' : '<a href="/view/diff/' . $row['id'] . '">View</a>'); ?></td>
        <td style="text-align:left;"><?=($row['input']? '<a href="/view/input/' . $row['id'] . '">View</a>' : ''); ?></td>
        <!--<td style="text-align:left;"><a href="/view/input/<?=$row['id']?>">View</a></td>-->
        <td style="text-align:left;"><?=(trim($row['errors'])? '<a href="/view/error/' . $row['id'] . '">View</a>' : ''); ?></td>
        <td style="text-align:right;"><?=showDateTime($row['time'])?></td>
    <tr>
<?php
}
?>
</table>

<?php
show_page('View submissions');
?>
