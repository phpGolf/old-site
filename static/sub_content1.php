<div id="activity_main">
    <table cellpadding="2" cellspacing="0" style="margin-top:0px;font-size:10px;">
        <tr>
            <td colspan="5" style="background-color:#123456;color:#FFFFFF;font-size:14px;font-weight:bold;text-align:center; width:500px;">Recent Activity</td>
        </tr>
        <tr class="row_subheader">
            <th style="text-align:left;">Username</th>
            <th style="text-align:left;">Challenge</th>
            <th style="text-align:left;">Time ago</th>
            <th style="text-align:left;">Bytes</th>
            <th style="text-align:right;">Result</th>
        </tr>
            <?php
            rowClass(false);
            foreach (getRecent(15) as $data) {
            ?>
        <tr style="text-align:left;" class="<?=rowClass(2)?>">
            <td style="font-size:12px;"><img src="<?=GFX?>flags/<?=str_replace(' ','_',$data['country'])?>.png" alt="" title="<?=$data['country']?>" /> 
            <a href="/user/<?=$data['username']?>"><?=$data['username']?></a></td>
            <td style="font-size:12px;"><a href="/challenge/<?=getSafenameFromId($data['challenge_id'])?>"><?=$data['challenge_name']?></a></td>
            <td style="font-size:12px;" class="showdate" data-date="<?=getGMTdate($data['time'])?> GMT"><?=showDateTime($data['time'])?></td>
            <td style="text-align:left; font-size:12px;"><?=$data['size'];?></td>
            <td style="text-align:right; font-size:12px;"><img src="<?=GFX?>icons/<?=$data['result']?>.png" alt="" title="<?=ucfirst($data['result'])?>" /></td>
        </tr>
            <?php
            }
        ?>
    </table>
</div>

