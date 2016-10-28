<?php

if (empty($_GET['value'][1])) { 
    header('Location: /view/user/' . $_SESSION['username']);
}

$stmt = $PDO->prepare('SELECT id FROM challenges WHERE id = :challenge_id');
$stmt->execute(array(':challenge_id' => getChallengeIdFromSafename($_GET['value'][1])));
$result = $stmt->fetchAll();
if (count($result) == 0) {
    error(0, 'Not found', 'Challenge not found');
}

$mem = new cache;
$sql = 'SELECT a.id,a.time,a.code,a.size,a.user_id,a.input,a.version,a.passed,a.executed,a.challenge_id,c.name,u.username
        FROM attempts a, challenges c, users u
        WHERE a.challenge_id = c.id AND u.id = a.user_id AND c.id = :challenge_id ORDER BY ';
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

if (!$data = $mem->get()) {
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':challenge_id', getChallengeIdFromSafename($_GET['value'][1]));
    $stmt->execute();
    $result = $stmt->fetchAll();
    $mem->set(0,$result,MEMCACHE_COMPRESSED,3600);
} else {
    $result = $data;
}

?>

<h1>Submissions for <a href="/challenge/<?=$result[0]['name']?>"><?=$result[0]['name']?></a></h1>
<table cellpadding="0" cellspacing="0" style="width:100%;font-size:10px;">
    <tr class="row_header">
        <td style="text-align:left;"><a href="/view/challenge/<?=getSafenameFromId($result[0]['challenge_id'])?>/username">User</td>
        <td style="text-align:left;"><a href="/view/challenge/<?=getSafenameFromId($result[0]['challenge_id'])?>/bytes">Bytes</a></td>
        <td style="text-align:left;"><a href="/view/challenge/<?=getSafenameFromId($result[0]['challenge_id'])?>/result">Result</a></td>
        <td style="text-align:left;"><a href="/view/challenge/<?=getSafenameFromId($result[0]['challenge_id'])?>/version">Version</a></td>
        <td style="text-align:left;">Code</td>
        <td style="text-align:left;">Difference</td>
        <td style="text-align:left;">Input</td>
        <td style="text-align:left;">Valid</td>
        <td style="text-align:right;"><a href="/view/challenge/<?=getSafenameFromId($result[0]['challenge_id'])?>/latest">Submitted</a></td>
    </tr>
<?php
rowClass(false);
foreach ($result as $row) {
?>
    <tr class="<?=rowClass(2)?>">
        <td style="text-align:left;"><a href="/user/<?=$row['username']?>"><?=$row['username']?></a></td>
        <td style="text-align:left;"><?=$row['size']?></td>
        <td style="text-align:left;">
    <?php 
    if ($row['executed'] == 0) {
    ?>
            <span style="color:#BDA612;">Pending</span>
    <?php
    } else {
        if($row['passed']) {
        ?> 
            <span style="color:#189111;">Passed</span>
        <?php 
        } else {
        ?>  
            <span style="color:#FF0000;">Failed</span>
    <?php 
        }
    }
    ?>
        </td>
        <td style="text-align:left;"><?=$row['version']?></td>
        <td style="text-align:left;"><a href="/view/code/<?=$row['id']?>">View</a></td>
        <td style="text-align:left;"><?=($row['passed']? '' : '<a href="/view/diff/' . $row['id'] . '">View</a>'); ?></td>
        <td style="text-align:left;"><?=($row['input']? '<a href="/view/input/' . $row['id'] . '">View</a>' : ''); ?></td>
        <td style="text-align:left;"><a href="/view/valid/<?=$row['id']?>">View</a></td>
        <td style="text-align:right;"><?=$row['time']?></td>
    <tr>
<?php
}
?>
</table>

<?php
show_page('View submissions');
?>
