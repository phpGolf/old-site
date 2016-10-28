<?php

if(!defined('INDEX')) {
    die('No access');
}

if(!include_func('stats')) {
    error();
}
if(check_page('stats')) {
    show_page(0,'stats');
    exit;
}
?>
<h1>General stats</h1>
<ul>
    <li>Total members: <?=getTotalMembers();?></li>
    <li>Total submissions: <?=getTotalSubmissions();?></li>
    <li>Users online peak: <?=getUsersOnlinePeak();?></li>
</ul>

<h1>Countries stats</h1>
<table cellpadding="0" cellspacing="0" style="border:0px; width:100%;">
    <tr class="row_header">
        <td style="font-weight:bold;">Country</td>
		<td style="font-weight:bold;">Users</td>
		<td style="font-weight:bold;">Total submissions</td>
		<td style="font-weight:bold;">Challenges per user</td>
		<td style="font-weight:bold;">Submissions per user</td>
    </tr>
<?php
rowClass(false);
foreach (getCountriesStats() as $key => $val) {
    print
    '<tr class="'. rowClass(2) . '">
         <td><img src="' . GFX . 'flags/' . str_replace(' ','_',$val['country']). '.png" /> ' . $val['country'] . '</td>
         <td>' . $val['AmountOfUsers'] . '</td>
		 <td>' . $val['TotalSubmissions'] . '</td>
		 <td>' . $val['ChallengesPerUser'] . '</td>
		 <td>' . $val['SubmissionsPerUser'] . '</td>
    </tr>';
}
?>
</table>
<h1>Submission stats</h1>
<table cellpadding="0" cellspacing="0" style="border:0px; width:100%;">
    <tr class="row_header">
        <td style="font-weight:bold;">Challenge name</td>
        <td style="font-weight:bold;">Submissions</td>
        <td style="font-weight:bold;">Passed</td>
        <td style="font-weight:bold;">Failed</td>
    </tr>
<?php
rowClass(false);

foreach (getAllChallengesStats($pdo) as $key => $value) {
    print
    '<tr class="'. rowClass(2) . '">
         <td><a href="/challenge/' . preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$value['name'])) . '">' . $value['name'] . '</a></td>
         <td>' . $value['total'] . '</td>
         <td style="color:#189111;">' . $value['passed'] . '</td>
         <td style="color:#FF0000;">' . $value['failed'] . '</td>
    </tr>';
}

?>
</table>

<?php
show_page('Stats','stats');
