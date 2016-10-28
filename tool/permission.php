<?php
if(!defined('INDEX')) {
    header('location: /');
}

if(!access('show_tools_permission')) {
    error(403);
}
?>
<h1>Permissions administration</h1>
<?php
$permissions = permissions();
switch ($_GET['action']) {
    case 'edit':
    $title = 'Edit';
    $submit = 'edit';
    if($permissions[$_GET['id']]) {
        $name = $permissions[$_GET['id']]['name'];
        $key = $permissions[$_GET['id']]['key'];
    }
    case 'add':
    $title = ($title) ? $title : 'Add new';
    $submit = ($submit) ? $submit : 'add';
?>
<a href="/tools/permission">List all</a>
<h3><?=$title?></h3>

<form action="/tools/permission" method="POST">
<table>
    <tr>
        <td>Name:</td>
        <td><input name="name" type="text" value="<?=$name?>" style="width: 300px;"></td>
    </tr>
    <tr>
        <td>Key:</td>
        <td><input name="key" type="text" value="<?=$key?>" style="width: 300px;"></td>
    </tr>
</table>
<input name="id" type="hidden" value="<?=$_GET['id']?>">
<input name="submit_<?=$submit?>" type="submit" value="<?=$title?>">
</form>
<?php
        break;
    case 'delete':
    if(deletePermission($_GET['id'])) {
        msg('Deleted');
    } else {
        msg('Unknown ID',0);
    }
    default:
    if($_POST['submit_edit']) {
        if(editPermission($_POST['id'],$_POST['name'],$_POST['key'])) {
            msg('Saved');
        } else {
            msg('Duplicate name/key or unknown ID',0);
        }
    } elseif($_POST['submit_add']) {
        if(addPermission($_POST['name'],$_POST['key'])) {
            msg('Added new');
        } else {
            msg('Duplicate name/key',0);
        }
    }
    $permissions = permissions();
?>
<a href="/tools/permission?action=add">Add new</a>
<h3>All permissions</h3>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
    <tr class="row_header">
        <th>Name</th>
        <th>Key</th>
        <th colspan="2">Options</th>
    </tr>
<?php
rowClass(false);
foreach($permissions as $id => $permission) {
    list($name,$key)=$permission;
    ?>
    <tr class="<?=rowClass(2)?>">
        <td><?=$permission['name']?></td>
        <td><?=$permission['key']?></td>
        <td><a href="/tools/permission?id=<?=$id?>&action=delete">Delete</a></td>
        <td><a href="/tools/permission?id=<?=$id?>&action=edit">Edit</a></td>
    </tr>
    <?php
}
?>
</table>
<?php
    break;
}
show_page('Permissions administration');
