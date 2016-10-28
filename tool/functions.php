<?php
if(!defined('INDEX')) {
    header('location: /');
}
if(!access('debug')) {
    error(403);
}
//Include all functions
$dir = scandir(FUNCTIONS);
unset($dir[0]);
unset($dir[1]);
foreach($dir as $file) {
    $file = str_replace('function_','',basename($file,'.php'));
    include_func($file);
}

//Get functions
include('functions_desc.php');
$funcs = get_defined_functions();
$funcs = $funcs['user'];
foreach($funcs as $function) {
    unset($tmp);
    $tmp = array();
    if(isset($Desc[$function])) {
        $tmp = $Desc[$function];
    }
    if(!isset($tmp['Name'])) {
        $tmp['Name'] = $function;
    }
    $Functions[] = $tmp;
}

?>
<h1>Function list</h1>
<h3><?=count($Functions)?> functions</h3>
<table cellspacing="0" cellpadding="1" style="width:100%">
    <tr class="row_header">
        <th colspan="2">Functions</th>
    </tr>
<?php
foreach($Functions as $Function) {
    $title = $Function['Name'].'(';
    if(!isset($Function['Args']['NONE']) && isset($Function['Args'])) {
        $first = true;
        foreach($Function['Args'] as $var => $arg) {
            if(!$first) {
                $title .= ',';
            }
            if(isset($arg['Type'])) {
                $title .= ' '.$arg['Type'].' ';
            }
            if(isset($arg['Default'])) {
                $title .= ' ['.$var.'='.$arg['Default'].'] ';
            } else {
                $title .= ' '.$var.' ';
            }
            $first = false;
        }
    } else {
        $title .= ' void ';
    }
    $title .= ')';
    if($Function['Desc']) {
        $title = '<a class="spoiler" name="'.$Function['Name'].'">'.$title.'</a>';
    } else {
        $title = '*'.$title;
    }
?>
    <tr class="row2">
        <td colspan="2"><?=$title?></td>
    </tr>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td>Description</td>
        <td><?=$Function['Desc']?></td>
    </tr>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td colspan="2" style="padding-left: 5px; padding-top: 5px;">Arguments</td>
    </tr>
<?php
    if(!isset($Function['Args']['NONE']) && isset($Function['Args'])) {
        foreach($Function['Args'] as $var => $arg) {
?>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td><?=$var?></td>
        <td><?=$arg['Desc'].(($arg['Default']) ? ' (default: '.$arg['Default'].')' : '')?></td>
    </tr>
<?php
        }
    } else {
?>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td>None</td>
        <td>This function don't take any arguments</td>
    </tr>
<?php
    }
?>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td colspan="2" style="padding-left: 5px; padding-top: 5px;">Return</td>
    </tr>
<?php
    if(!isset($Function['Return']) || isset($Function['Return']['NULL'])) {
        $type = 'NULL';
        $text = 'This function don\'t return anything';
    } else {
        $type = key($Function['Return']);
        $text = $Function['Return'][$type];
    }
?>
    <tr class="row1" name="spoiler_<?=$Function['Name']?>" style="display: none;">
        <td><?=$type?></td>
        <td><?=$text?></td>
    </tr>
<?php
}
?>
</table>
<?php
show_page('Function list');
