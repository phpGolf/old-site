<?php
//memcache
$mem = new cache;
if(getChallengeIdFromSafename($_GET['value'][0]) && $_GET['p'] == 'challenge') {
    $mem->key = "Right_Challenge_".getChallengeIdFromSafename($_GET['value'][0]).'_'.$_SESSION['userLevel'];
} else {
    $mem->key = "Right".'_'.$_SESSION['userLevel'];
}
if(true) {
ob_start()
?>
<div id="content_right">
    <div id="scoreboard">
        <table cellpadding="2" cellspacing="0" style="width:100%;font-size:12px;">
            <tr>
                <td colspan="3" style="background-color:#123456;color:#FFFFFF;font-size:14px;font-weight:bold;text-align:center;">
                <?php
                $challenge_id = getChallengeIdFromSafename($_GET['value'][0]);
                if (!empty($challenge_id) && $_GET['p'] == 'challenge') { 
                    print getChallengeName($challenge_id) . ' Scoreboard';
                }
                else {
                    print 'Top 20 Golfers';
                }
                ?>
                </td>
            </tr>
            <tr class="row_subheader">
                <td style="text-align:left; font-weight:bold;">Rank</td>
                <td style="text-align:left; font-weight:bold;">Username</td>
                <td style="text-align:right; font-weight:bold;">Score</td>
            </tr>
        <?php
        $i = 1;
        if (!empty($challenge_id) && $_GET['p'] == 'challenge') {
            $toplist = getChallToplist($challenge_id,20);
            $cup = false;
        } else {
            $toplist = getTop20();
            $cup = true;
        }
        if(count($toplist)==0) {
            $toplist=array();
        }
        rowClass(false);
        foreach($toplist as $data) {
        ?>
        <tr class="<?=rowClass(2)?>">
            <td style="text-align:left;"><?=medals($i++, $cup)?></td>
            <td style="text-align:left;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',$data['country'])?>.png" alt="" title="<?=$data['country']?>" /> 
            <a href="/user/<?=$data['username']?>"><?=$data['username']?></a></td>
            <td style="text-align:right;"><?=round($data['points'])?><?=(($data['size']) ? ' ('.$data['size'].' B)' : '')?></td>
        </tr>
        <?php
        }
        ?>
        </table>
    </div>
    <?php
    if (!empty($challenge_id) && $_GET['p'] == 'challenge') {
    ?>
    <div id="activity">
        <table cellpadding="2" cellspacing="0" style="margin-top:0px;width:100%;">
            <tr>
                <td colspan="4" style="background-color:#123456;color:#FFFFFF;font-size:14px;font-weight:bold;text-align:center;">Recent Activity</td>
            </tr>

            <tr class="row_subheader">
                <th style="text-align:left;">Username</th>
                <th style="text-align:left;">Bytes</th>
                <th style="text-align:left;">Time ago</th> 
                <th style="text-align:right;">Result</th>
            </tr>
                <?php
                rowClass(false);
                foreach (getChallRecent($challenge_id, 15) as $data) {
                    ?>
            <tr class="<?=rowClass(2)?>">
                <td style="font-size:12px;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',$data['country'])?>.png" alt="" title="<?=$data['country']?>" /> 
                <a href="/user/<?=$data['username']?>"><?=$data['username']?></a></td>
                <td style="font-size:12px;"><?=$data['size']?></td>
                <td style="font-size:12px;" class="showdate" data-date="<?=getGMTdate($data['time'])?> GMT"><?=showDateTime($data['time'])?></td>
                <td style="text-align:right; font-size:12px;"><img src="<?=GFX?>icons/<?=$data['result']?>.png" alt="" title="<?=ucfirst($data['result'])?>" /></td>
            </tr>
                <?php
                }
                ?>
        </table>
    </div>
    <?php
    }
    /*
    ?>
    <div id="best_submissions">
        <table cellpadding="2" cellspacing="0" style="margin-top:0px;width:100%;font-size:10px;">
            <tr>
                <td colspan="3" style="background-color:#123456;color:#FFFFFF;font-size:14px;font-weight:bold;text-align:center;">Leader Board</td>
            </tr>
            <tr class="row_subheader">
                <td style="text-align:left; font-weight:bold;">Challenge</td>
                <td style="text-align:left; font-weight:bold;">Leader</td>
                <td style="text-align:right; font-weight:bold;">Bytes</td>
            </tr>
        <?php
        rowClass(false);
        foreach (getTopSubmissions() as $chall_id => $value) {
            if ($value['type'] == 'private') {
                $type = '<img src="'.GFX.'icons/private.png" title="Private" alt="" />';
            } elseif($value['type'] == 'public') {
                $type = '<img src="'.GFX.'icons/public.png" title="Public" alt="" />';
            } else {
                $type = '<img src="'.GFX.'icons/protected.png" title="Protected" alt="" />';
            }
         ?>
            <tr class="<?=rowClass(2)?>">
                <td><?=$type?> <a href="/challenge/<?=getSafenameFromId($chall_id)?>"><?=$value['challenge_name']?></a></td>
                <td><img src="<?=GFX?>flags/<?=str_replace(' ','_',$value['country'])?>.png" alt="" title="<?=$value['country']?>" /> 
                <a href="/user/<?=$value['username']?>"><?=$value['username']?></a></td>
                <td style="text-align:right;"><?=$value['size']?></td>
            </tr>
        <?php
        }
        ?>
        </table>
    </div><?php */ ?>
</div>
<?php
    $right = ob_get_flush();
    $mem->set(0,$right,MEMCACHE_COMPRESSED,(3600*24));
} else {
    echo $data;
}
?>
