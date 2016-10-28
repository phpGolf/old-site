<?php
if(!defined('INDEX')) {
    header('location: /');
}
if(check_page('Top250')) {
    show_page(0,'Top250');
    exit;
}
?><center><h1>Top 250 Golfers</h1></center>

<table cellpadding="2" cellspacing="0" style="width:400px;" align="center">
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr class="row_header">
        <td style="text-align:left; font-weight:bold;">Rank</td>
        <td style="text-align:left; font-weight:bold;">Username</td>
        <td style="text-align:right; font-weight:bold;">Completed</td>
        <td style="text-align:right; font-weight:bold;">Score</td>
    </tr>
<?php
$i = 1;
$toplist = getTop250();

rowClass(false);
foreach ($toplist as $data) {
    ?>
    <tr class="<?=rowClass(2)?>">
        <td style="text-align:left;"><?=$i++?></td>
        <td style="text-align:left;">
            <img src="<?=GFX?>flags/<?=str_replace(' ','_',$data['country'])?>.png" alt="" /> <a href="/user/<?=$data['username']?>"><?=$data['username']?></a>
        </td>
        <td style="text-align:right;"><?=$data['total']?>/<?=getCountOpenActiveChalls();?></td>
        <td style="text-align:right;"><?=round($data['points'])?></td>
    </tr>
    <?php
}

?>
</table>

<?php
show_page('Top 250 Golfers','Top250');
?>
