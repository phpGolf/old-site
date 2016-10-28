<?php
if(!defined('INDEX')) {
    header('location: /');
}

if(!access('show_tools_useradmin')) {
    error(403);
}

$groups = groups();
?>
<h1>User administration</h1>
<?php
switch($_GET['action']) {
    case 'edit':
    if(!$groups[$_GET['id']]) {
        msg('Invalid groupid');
    } else {
        $groupPermissions = getGroupPermissions($_GET['id']);
        $title = 'Edit '.$groups[$_GET['id']]['name'];
        $key = 'edit';
        $name = $groups[$_GET['id']]['name'];
        $order = $groups[$_GET['id']]['order'];
    }
    case 'add':
    $title = ($title) ? $title : 'Add group';
    $key = ($key) ? $key : 'add';
    $order = ($order) ? $order : 500;
    $permissions = permissions();
?>
<a href="/tools/useradmin">List groups</a>
<h3><?=$title?></h3>
<form action="/tools/useradmin" method="POST">
<table style="width:100%">
<tr>
    <td>Name:</td>
    <td><input type="text" name="name" value="<?=$name?>" style="width: 200px;"></td>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>    
    <td>Order:</td>
    <td><input type="text" name="order" value="<?=$order?>" style="width: 200px"></td>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <th colspan="4">Permissions</th>
</tr>
<?php
    $i=0;
    foreach($permissions as $permId => $permission) {
        if($i==4) {
            echo '</tr><tr>';
            $i=0;
        }
        ?>
        <td><input type="checkbox" name="perm[<?=$permId?>]" <?php echo ($groupPermissions[$permission['key']]) ? 'checked="checked"' : ''?>> <?=$permission['name']?> </td>
        <?php
        $i++;
    }
?>
</table>
<input type="hidden" name="id" value="<?=$_GET['id']?>">
<input type="submit" name="submit_<?=$key?>" value="<?=$title?>">
</form>
<?php
        break;
    case 'delete':
        if(deleteGroup($_GET['id'])) {
            msg('Group deleted');
        } else {
            msg('Unknow ID',0);
        }
    default:
    if($_POST['submit_add']) {
        if($_POST['perm']) {
            $perm = implode(';',array_keys($_POST['perm']));
        } else {
            $perm = '';
        }
        if(addGroup($_POST['name'],$perm,$_POST['order'])) {
            msg('Group created');
        } else {
            msg('Group exists or invalid name',0);
        }
    }
    if($_POST['submit_edit']) {
        if($_POST['perm']) {
            $perm = implode(';',array_keys($_POST['perm']));
        } else {
            $perm = '';
        }
        if(editGroup($_POST['id'],$_POST['name'],$perm,$_POST['order'])) {
            msg('Group changed');
        } else {
            msg('Unknown ID',0);
        }
    }
    $groups = groups();
?>
<a href="/tools/useradmin?action=add">Add group</a>
<h3>List groups</h3>
<table cellpadding="0" cellspacing="0" style="width: 100%">
    <tr class="row_header">
        <th>Group</th>
        <th>Order</th>
        <th colspan="2">Options</th>
    </tr>
<?php
    rowClass(false);
    foreach($groups as $id => $group) {
        ?>
    <tr class="<?=rowClass(2)?>">
        <td><?=$group['name']?></td>
        <td><?=$group['order']?></td>
        <td><a href="?action=delete&id=<?=$id?>">Delete</a></td>
        <td><a href="?action=edit&id=<?=$id?>">Edit</a></td>
    </tr>
        <?php
    }
?>
</table>
<?php
    break;
}

show_page('User administration');
