<?php
if (!defined('INDEX')) {
    header('location: /');
}
if (!include_class('user') || !include_func('stats') || !include_func('gravatar')) {
    error();
}

include_class('image');

global $countries;

if (empty($_GET['value'][0])) {
    $User = new User($_SESSION['id']);
} else {
    if( is_numeric($_GET['value'][0])) {
        $User = new User($_GET['value'][0]);
    } else {
        $User = new User($_GET['value'][0],true);
    }
}

//Avatar
if (isset($_POST['submit_avatar']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id'])) {
    if (!isset($_POST['gravatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_NO_FILE) {
        $User->remAvatar();
        msg('Disabled avatar',1);
    } elseif(isset($_POST['gravatar']) != $User->gravatar && $_FILES['avatar']['error'] == UPLOAD_ERR_NO_FILE) {
        if ($User->setAvatar(0,isset($_POST['gravatar']))) {
            msg('Using gravatar as avatar',1);
        }
    } else {
        //Upload image
        $Image = new Image();
        if ($id = $Image->uploadFile($_FILES['avatar'])) {
            if ($User->setAvatar($id)) {
                msg('Your avatar have been uploaded',1);
            }
        }
    }
}

//Password
if (isset($_POST['submit_password']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id'])) {
    if ($_POST['newPassword1'] != $_POST['newPassword2']) {
        msg('The new password and repeat was not like',0);
        $E=true;
    }
    if (!$User->checkPassword($_POST['oldPassword']) && !(userLevel('600') && access('user_edit') && userLevel($User->getUserLevel()))) {
        msg('The old password was wrong',0);
        $E=true;
    }
    if (!$E) {
        if($User->setPassword($_POST['newPassword1'])) {
            msg('Password was changed');
        }
    }
}
//Userhash
if (isset($_POST['submit_hash']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id'])) {
    $User->genHash();
    msg('Autologin string updated');
}


//Email
if (isset($_POST['submit_email']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id'])) {
    if ($_POST['email'] != $User->getEmail()) {
        if ($User->setEmail($_POST['email'])) {
            msg('Email changed');
        }
    }
    $publicEmail = (isset($_POST['public_email'])) ? 1 : 0;
    if ($publicEmail != $User->publicEmail) {
        if ($User->setEmailPublic($publicEmail)) {
            if ($publicEmail) {
                msg('Email is now public');
            } else {
                msg('Email is now hidden');
            }
        }
    }
}

//Country
if (isset($_POST['submit_country']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || ($User->getUserid() == $_SESSION['id'])))
{
    if ($_POST['submit_country'] != $User->getCountry()) {
        if (in_array($_POST['country'], $countries)) {
            if ($User->setCountry($_POST['country'])) {
                msg('Country changed');
            }
        } else {
            msg('Not a valid country', 0);
        }
    }
}

//Timezone
if (isset($_POST['submit_timezone']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || ($User->getUserid() == $_SESSION['id'])))
{
    if ($_POST['timezone'] != $User->timezone) {
        if ($User->setTimezone($_POST['timezone'])) {
            msg('Timezone changed to '.$_POST['timezone'].'. Time is now '.showDateTime(time()));
        }
    }
}

//Username
if (isset($_POST['submit_username']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || ($User->getUserid() == $_SESSION['id'] && USER_CHANGE_NAME===TRUE))) {
    if ($_POST['username'] != $User->getUsername()) {
        if ($User->setUsername($_POST['username'])) {
            msg('Username is changed');
        }
    }
}

//Website
if (isset($_POST['submit_website']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || ($User->getUserid() == $_SESSION['id']))) {
    if ($_POST['website'] != $User->getWebsite()) {
        if ($User->setWebsite($_POST['website'])) {
            msg('Website is changed');
        }
    }
}

//Regdate
if (isset($_POST['submit_regdate']) && ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())))) {
    $date = date('Y-m-d',strtotime($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']));
    if ($date != $User->getRegdate()) {
        if ($User->setRegdate($date)) {
            msg('Registration date is changed');
        }
    }
}

//Group
if (isset($_POST['submit_group']) && (userLevel('600') && access('user_edit') && userLevel($User->getUserLevel()))) {
    if ($_POST['group'] != $User->getGroup()) {
        if ($User->setGroup($_POST['group'])) {
            msg('Group is changed');
        }
    }
}

//Permissions
if (isset($_POST['submit_permissions']) && (userLevel('600') && access('user_edit') && userLevel($User->getUserLevel()))) {
    if (count($_POST['perm']) == 0) {
        $permissions = array();
    } else {
        $permissions = array_keys($_POST['perm']);
        $permissions = implode(';',$permissions);
    }
    if ($User->setMultPermissions($permissions)) {
        msg('Permissions changed');
    } else {
        msg('Something wrong happend',0);
    }

}
?>
<h1>Profile for <?=$User->getUsername();?></h1>
<form action="" method="post" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" style="border:0px; width: 100%;">
<?php
$pic = $User->getAvatar(100)? $User->getAvatar(100) : GFX."profile_default.jpg";
?>
    <tr>
        <td colspan="2"><image style="border: 1px solid #000000;" src="<?=$pic?>"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:left; font-weight:bold;">Personal</td>
    </tr>
<?php
//Change username
if ((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || ($User->getUserid() == $_SESSION['id'] && USER_CHANGE_NAME===TRUE)) {
    ?>
    <tr>
        <th colspan="2" style="margin-top: 10px;">Change username</th>
    </tr>
    <tr>
        <td>Username</td>
        <td>
            <input type="text" name="username" style="width: 200px;" value="<?=$User->getUsername()?>"> 
            <input type="submit" name="submit_username" value="Change username">
        </td>
    </tr>
<?php
} else {
    ?>
    <tr>
        <td style="width:200px;">Username</td>
        <td><?=$User->getUsername()?></td>
    </tr>
<?php
}
//Change website
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
?>
    <tr>
        <td>Website</td>
        <td><input type="text" name="website" style="width: 200px;" value="<?=$User->getWebsite()?>"> 
            <input type="submit" name="submit_website" value="Change website"></td>
    </tr>
<?php
} else {
?>
    <tr>
        <td>Website</td>
        <td><a href="<?=$User->getWebsite()?>"><?=$User->getWebsite()?></a></td>
    </tr>
<?php
}
//Change avatar
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
?>
    <tr>
        <td>Avatar:</td>
        <td><input type="hidden" name="MAX_FILE_SIZE" value="<?=Image::getMaxSize()?>"><input type="file" name="avatar"></td>
    <tr>
    </tr>
        <td>Use <a href="http://www.gravatar.com/">Gravatar</a>?</td>
        <td><input type="checkbox" name="gravatar" <?=($User->gravatar) ? 'checked="checked"' : ''?>>
        <input type="submit" name="submit_avatar" value="Update avatar"></td>
    </tr>
<?php
}
//Change country
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
?>
    <tr>
        <td>Country</td>
        <td>
            <select name="country">
            <?php
            foreach ($countries as $country) {
                print '
                <option value="' . $country . '"' . ($User->getCountry() == $country ? ' selected="selected"':'') . '>' . $country . "</option>\n";
            }
            ?>
            </select>
            <input type="submit" name="submit_country" value="Change country"></td>
    </tr>
<?php
} else {
?>
    <tr>
        <td>Country</td>
        <td><img src="<?=GFX?>flags/<?=str_replace(' ','_',$User->getCountry())?>.png" alt="" /> <?=$User->getCountry()?></td>
    </tr>
<?php
}
//Timezone
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
?>
    <tr>
        <td>Timezone</td>
        <td>
            <select name="timezone">
            <?php
            foreach (timezone_identifiers_list() as $zone) {
                if($zone == 'localtime' || !preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//',$zone)) {
                    continue;
                }
?>
                <option value="<?=$zone?>"<?=($User->timezone == $zone ? ' selected="selected"' : '')?>><?=$zone?></option>
<?php
            }
            ?>
            </select>
            <input type="submit" name="submit_timezone" value="Change timezone"></td>
    </tr>
<?php
}
//Change reg date
if(userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) {
$timestamp = strtotime($User->getRegdate());
$reg['year'] = date('Y',$timestamp);
$reg['month'] = date('m',$timestamp);
$reg['day'] = date('d',$timestamp);
?>
    <tr>
        <td>Registration date</td>
        <td><select name="year">
<?php
            foreach(range(((2010 > $reg['year'] && isset($reg['year'])) ? $reg['year'] : 2010),date('Y')+5) as $year) {
?>
                <option value="<?=$year?>"<?=($reg['year'] == $year) ? ' selected="selected"': ''?>><?=$year?></option>
<?php
            }
?>
            </select>
            <select name="month">
<?php
            foreach(range(1,12) as $month) {
?>
                <option value="<?=$month?>"<?=($reg['month'] == $month || (!isset($reg['month']) && $month = date('m'))) ? ' selected="selected"': ''?>>
                <?=date('F',strtotime('2010-'.$month.'-1'))?></option>
<?php
            }
?>
            </select>
            <select name="day">
<?php
            foreach(range(1,31) as $day) {
?>
                <option value="<?=$day?>"<?=($reg['day'] == $day || (!isset($reg['day']) && $day = date('d'))) ? ' selected="selected"': ''?>>
                    <?=$day?>
                </option>
<?php
            }
            ?>
            </select>
            <input type="submit" name="submit_regdate" value="Change regdate"></td>
    </tr>

<?php
} else {
?>
    <tr>
        <td>Registration date</td>
        <td><?=showDate($User->getRegdate())?></td>
    </tr>
<?php
}
//Change group
if(userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) {
?>
    <tr>
        <th colspan="2" style="margin-top: 10px;">Change group</th>
    </tr>
    <tr>
        <td>Group</td>
        <td>
            <select style="width: 200px;" name="group">
        <?php
        foreach(groups() as $gid => $group) {
            echo '<option value="'.$gid.'"'.(($gid==$User->getGroup()) ? ' selected="selected"' : '').'>'.$group['name'].'</option>';
        }
        ?></select>
            <input type="submit" name="submit_group" value="Change group">
        </td>
    </tr>
<?php
} else {
    ?>
    <tr>
        <td>Group</td>
        <td><?=$User->getGroupName()?></td>
    </tr>
    <tr>
        <td>Last time online</td>
        <td><?=showDateTime($User->getLastTime())?></td>
    </tr>
<?php
}
//Change email
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
?>
    <tr>
        <th colspan="2" style="margin-top: 10px;">Change Email</th>
    </tr>
    <tr>
        <td>Email</td>
        <td><input type="text" name="email" style="width: 300px;" value="<?=$User->getEmail()?>"></td>
    </tr>
    <tr>
        <td>Public email</td>
        <td>
            <input type="checkbox" name="public_email"<?php echo ($User->publicEmail) ? ' checked="checked"' : ''?>>
            <input type="submit" name="submit_email" value="Change email">
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
<?php
} elseif($User->publicEmail) {
    ?>
    <tr>
        <td>Email</td>
        <td><?=$User->getEmail()?></td>
    </tr>
<?php
}
//Change password
if((userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) || $User->getUserid() == $_SESSION['id']) {
    ?>
    <tr>
        <th colspan="2" style="margin-top: 10px;">Change Password</th>
    </tr>
    <tr>
        <td>Old password</td>
        <td><input type="password" name="oldPassword" style="width: 200px;" value=""></td>
    </tr>
    <tr>
        <td>New password</td>
        <td><input type="password" name="newPassword1" style="width: 200px;" value=""></td>
    </tr>
    <tr>
        <td>Repeat password</td>
        <td><input type="password" name="newPassword2" style="width: 200px;" value=""></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" name="submit_password" value="Change password"></td>
    </tr>
    <tr>
        <td><input type="submit" name="submit_hash" value="Change autologin string"></td>
        <td>Will reset the autologin string</td>
    </tr>
    <?php
}
//Change permissions
if(userLevel('600') && access('user_edit') && userLevel($User->getUserLevel())) {
?>
    <tr>
        <th colspan="2"><a class="spoiler" name="userperm">Permissions</a></th>
    </tr>
    <tr>
        <td colspan="2">
            <div style="border: 1px solid black; padding-top:10px;">
                <div name="spoiler_userperm" style="display:none;">
                <table>
                    <tr>
                    <?php
                        $i=0;
                        $permissions = permissions();
                        $groupPermissions = $User->getGroupPermissions();
                        $userPermissions = $User->getPermissions();
                        foreach($permissions as $permId => $permission) {
                            if($i==4) {
                                echo '</tr><tr>';
                                $i=0;
                            }
                            ?>
                            <td><input type="checkbox" name="perm[<?=$permId?>]" <?php echo ($groupPermissions[$permission['key']] || $userPermissions[$permission['key']]) ? 'checked="checked"' : ''?> <?php echo ($groupPermissions[$permission['key']]) ? 'disabled="disabled"' : ''?>> <?=$permission['name']?> </td>
                            <?php
                            $i++;
                        }
                    ?>
                    </tr>
                    <tr>
                        <td colspan="4"><input type="submit" name="submit_permissions" value="Change permissions"></td>
                    </tr>
                </table>
                </div>
            </div>
        </td>
    </tr>
<?php
}
?>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="row_subheader">
        <th colspan="2">General Stats</th>
    </tr>
    <tr class="<?=rowClass(2)?>">
        <td>Total submissions</td> 
        <td><?=getTotalUserSubmissions($User->getUserid())?></td>
    </tr>
    <tr class="<?=rowClass(2)?>">
        <td>Challenges complete</td>
        <td><?=count(getUserChalls($User->getUserid(), true)).'/'.getCountOpenActiveChalls();?></td>
    </tr>
    <?php
    if(count(getUserChalls($User->getUserid())) != 0) {
    $total = getUserRank($User->getUserid());
    ?>
    <tr class="<?=rowClass(2)?>">
        <td>Site ranking</td>
        <td><?=medals($total['rank'],true);?> (<?=round($total['points'])?> points)</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="row_subheader">
        <th colspan="2">Challenges ranking</th>
    </tr>
    <?php
        $rec = DB::$PDO->prepare('SELECT
          a.size,
          ROUND(
            (
              SELECT
                MIN(size)
              FROM
                attempts
              WHERE
                  (challenge_id=a.challenge_id)
                AND
                  passed
            ) / a.size * 1000) points,
          a.challenge_id,
          c.name,
          (
            SELECT
              COUNT(1)
            FROM
              attempts d
            WHERE
                (challenge_id=a.challenge_id)
              AND
                passed
              AND
                (
                  (size<a.size)
                OR
                  (
                    (size=a.size)
                  AND
                    (
                      (time<a.time)
                    OR
                      (
                        (time=a.time)
                      AND
                        (id<=a.id)
                      )
                    )
                  )
                )
              AND
                NOT EXISTS(
                  SELECT
                    1
                  FROM
                    attempts
                  WHERE
                      passed
                    AND
                      (challenge_id=d.challenge_id)
                    AND
                      (user_id=d.user_id)
                    AND
                      (
                        (size<d.size)
                      OR
                        (
                          (size=d.size)
                        AND
                          (
                            (time<d.time)
                          OR
                            (
                              (time=d.time)
                            AND
                              (id<d.id)
                            )
                          )
                        )
                      )
                )
          ) rank
        FROM
          challenges c
        INNER JOIN
          attempts a
        ON
          ((a.challenge_id=c.id) AND a.passed)
        WHERE
            a.passed
          AND
            (a.user_id=:cid)
          AND
            NOT EXISTS(
              SELECT
                1
              FROM
                attempts
              WHERE
                  passed
                AND
                  (challenge_id=c.id)
                AND
                  (user_id=a.user_id)
                AND
                  (
                    (size<a.size)
                  OR
                    ((size=a.size) AND ((time<a.time) OR ((time=a.time) AND (id<a.id))))
                  )
            )
        ORDER BY
          c.id');
        rowClass(false);
        $rec->execute(Array(':cid' => $User->getUserid()));
        while(is_array($rank = $rec->fetch(PDO::FETCH_ASSOC))){
            ?>
    <tr class="<?=rowClass(2)?>">
        <td><a href="/challenge/<?=getSafenameFromID($rank['challenge_id'])?>"><?=$rank['name']?></a></td>
        <td><?=medals($rank['rank']);?> (<?=$rank['size']?> bytes - <?=$rank['points']?> points)</td>
    </tr>
            <?php
        }
    } else {
        ?>
    <tr>
        <td colspan="2">The user have not passed any challenges yet!</td>
    </tr>   
        <?php
    }
    ?>
</table>
</form>
<pre>
</pre>
<?php

show_page('Profile for '.$User->getUsername());
