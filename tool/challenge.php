<?php
if(!defined('INDEX')) {
    header('Location: /');
}

if(!access('show_tools_challenge')) {
    error(403);
    exit;
}
//Include challenge class
if(!include_class('challenge')) {
    error();
    exit;
}
?>
<h1>Challenge Administration</h1>
<?php
switch($_GET['value'][1]) {
    //Edit challenge
    case 'edit':
        if(isChallenge($_GET['value'][2])) {
            $Challenge = new Challenge($_GET['value'][2]);
            //Edit challenge data
            if(isset($_POST['submit_edit'])) {
                //Challenge name
                if($_POST['name'] !=  $Challenge->getName()) {
                    if($Challenge->setName($_POST['name'])) {
                        msg('Challenge name is changed to "'.$_POST['name'].'"');
                    }
                }
                //enddate
                $newDate = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
                if($_POST['enddate'] == 'active') {
                    if($newDate != $Challenge->getEnddate()) {
                        if($Challenge->setEnddate($newDate)) {
                            msg('End date is changed');
                        }
                    }
                } else {
                    if($Challenge->getEnddate() && $Challenge->delEnddate()) {
                        msg('Removed end date');
                    }
                }
                //Type
                if($_POST['type'] != $Challenge->getType()) {
                    if($Challenge->setType($_POST['type'])) {
                        msg('Challenge is now '.$_POST['type']);
                    }
                }
                //Active
                $active = ($_POST['active'] == 'on') ? true : false;
                if($active != $Challenge->getStatusActive()) {
                    if($Challenge->setStatusActive($active)) {
                        if($active) {
                            msg('The challenge is now active');
                        } else {
                            msg('The challenge is now inactive');
                        }
                    }
                }
                
                //Open
                $open = ($_POST['open'] == 'on') ? true : false;
                if($open != $Challenge->getStatusOpen()) {
                    if($Challenge->setStatusOpen($open)) {
                        if($open) {
                            msg('The challenge is now open');
                        } else {
                            msg('The challenge is now closed');
                        }
                    }
                }
                //Output type
                if($_POST['output_type'] != $Challenge->getOutputType()) {
                    if($Challenge->setOutputType($_POST['output_type'])) {
                        msg('Output type is changed');
                    }
                }
                
                //Disabled func
                if($_POST['disabled_func'] != $Challenge->getDisabledFunc()) {
                    if($Challenge->setDisabledFunc($_POST['disabled_func'])) {
                        msg('Disabled functions list is changed');
                    }
                }
                
                //Constant
                if($_POST['constant'] != $Challenge->getConstant()) {
                    if($Challenge->setConstant($_POST['constant'])) {
                        if(!empty($_POST['constant'])) {
                            msg('Constant is changed to '.$_POST['constant']);
                        } else {
                            msg('Constant is removed');
                        }
                    }
                }
                
                //Reset challenges
                challenges(false,true);
            }
            $title = 'Edit '.$Challenge->getName();
            $submit = 'submit_edit';
            $submit_value = 'Edit';
            if($Challenge->getEnddate()) {
                $enddate = $Challenge->getEnddate(true);
            } else {
                $enddate['year'] = date('Y');
                $enddate['month'] = date('m');
                $enddate['day'] = date('d');
            }
            $challenge_name = $Challenge->getName();
            $challenge_type = $Challenge->getType();
            $challenge_output = $Challenge->getOutputType();
            $challenge_active = $Challenge->getStatusActive();
            $challenge_open = $Challenge->getStatusOpen();
            $challenge_constant = $Challenge->getConstant();
            $challenge_disabled = $Challenge->getDisabledFunc();
        } else {
            msg('Did not find challenge',0);
        }
    //Add challenge
    case 'add':
        $title = ($title) ? $title : 'Add challenge';
        $submit = ($submit) ? $submit : 'submit_add';
        $submit_value = ($submit_value) ? $submit_value : 'Add';
        ?>
<a href="/tools/challenge/">List all challenges</a>
<h3><?=$title?></h3>
<form action="/tools/challenge/<?=($submit == 'submit_edit') ? 'edit/'.$_GET['value'][2] : ''?>" method="post">
    <table>
        <tr>
            <td>Name: </td>
            <td><input type="text" name="name" value="<?=$challenge_name?>"></tr>
        </tr>
        <tr>
            <td>End date: </td>
            <td><input type="checkbox" name="enddate" value="active"<?=($enddate) ? ' checked="checked"' : ''?>>
            <select name="year">
            <?php
            foreach(range(((date('Y') > $enddate['year'] && isset($enddate['year'])) ? $enddate['year'] : date('Y')),date('Y')+5) as $year) {
                ?>
                <option value="<?=$year?>"<?=($enddate['year'] == $year) ? ' selected="selected"': ''?>><?=$year?></option>
            <?php
            }
            ?>
            </select>
            <select name="month">
            <?php
            foreach(range(1,12) as $month) {
                ?>
                <option value="<?=$month?>"<?=($enddate['month'] == $month || (!isset($enddate['month']) && $month = date('m'))) ? ' selected="selected"': ''?>>
                <?=date('F',strtotime('2010-'.$month.'-1'))?></option>
            <?php
            }
            ?>
            </select>
            <select name="day">
            <?php
            foreach(range(1,31) as $day) {
                ?>
                <option value="<?=$day?>"<?=($enddate['day'] == $day || (!isset($enddate['day']) && $day = date('d'))) ? ' selected="selected"': ''?>>
                    <?=$day?>
                </option>
            <?php
            }
            ?>
            </select></td>
        </tr>
        <tr>
            <td>Type: </td>
            <td><input type="radio" name="type" value="public" id="public"<?=(($challenge_type=='public' || !isset($challenge_type)) ? ' checked="checked"' : '')?>><label for="public">Public</label> 
            <input type="radio" name="type" value="private" id="private"<?=(($challenge_type=='private') ? ' checked="checked"' : '')?>><label for="private">Private</label> 
            <input type="radio" name="type" value="protected" id="protected"<?=(($challenge_type=='protected') ? ' checked="checked"' : '')?>><label for="protected">Protected</label></td>
        </tr>
        <tr>
            <td>Active</td>
            <td><input type="radio" name="active" value="on" id="active_on"<?=(($challenge_active) ? ' checked="checked"' : '')?>><label for="active_on">Active</label>
            <input type="radio" name="active" value="off" id="active_off"<?=((!$challenge_active) ? ' checked="checked"' : '')?>><label for="active_off">Inactive</label></td>
        </tr>
        
        <tr>
            <td>Open</td>
            <td><input type="radio" name="open" value="on" id="open_on"<?=(($challenge_open) ? ' checked="checked"' : '')?>><label for="open_on">Open</label>
            <input type="radio" name="open" value="off" id="open_off"<?=((!$challenge_open) ? ' checked="checked"' : '')?>><label for="open_off">Closed</label></td>
        </tr>
        <tr>
            <td>Output Type: </td>
            <td><input type="radio" name="output_type" value="variable" id="variable"<?=(($challenge_output=='variable' || !isset($challenge_output)) ? ' checked="checked"' : '')?>><label for="variable">Variable</label>
            <input type="radio" name="output_type" value="static" id="static"<?=(($challenge_output=='static') ? ' checked="checked"' : '')?>><label for="static">Static</label></tr>
        </tr>
        <tr>
            <td>Constant name</td>
            <td><input type="text" name="constant" value="<?=$challenge_constant?>"></td>
        </tr>
        <tr>
            <td>Disabled functions</td>
            <td><input type="text" name="disabled_func" value="<?=$challenge_disabled?>">(Comma separated)</td>
        </tr>
    </table>
    <?php
    if($submit=='submit_edit') {
        ?>
    <input type="hidden" name="challenge_id" value="<?=$_GET['value'][2]?>">
        <?php
    }
    ?>
    <input type="submit" name="<?=$submit?>" value="<?=$submit_value?>">
</form>
        <?php
        
        break;
    //Delete challenge
    case 'delete':
    //delete code
    //List challenges
    default:
    ?>
<a href="/tools/challenge/add">Add challenge</a>
<h3>Challenge Listing</h3>
<table cellpadding="0" cellspacing="0" style="border:0px; width:100%;">
    <tr class="row_header">
        <th style="text-align:left;">Challenge</th>
        <th style="text-align:center;">Type</th>
        <th style="text-align:center;" colspan="2">Status</th>
        <th style="text-align:center;">End date</th>
        <th style="text-align:center;">Options</th>
    </td>
    <?php
    rowClass(false);
    foreach (challenges() as $cid => $challenge) {
        ?>
    <tr class="<?=rowClass(2)?>">
        <td><?=$challenge['name']?></td>
        <td style="text-align:center;"><?=$challenge['type']?></td>
        <td style="text-align:right;padding-right: 2px;"><?=($challenge['active'] == '1')? 'Active':'Inactive'?> </td>
        <td style="padding-left:2px;"> <?=($challenge['open'] == '1')? 'Open':'Closed'?></td>
        <td style="text-align:center;"><?=($challenge['enddate']) ? showDate($challenge['enddate']) : 'No enddate'?></td>
        <td style="text-align:center;">[<a href="/tools/challenge/edit/<?=$cid?>">Edit</a>] [<a href="/tools/challenge/delete/<?=$cid?>">Delete</a>]</td>
    </tr>
        <?php
    }
    ?>
</table>
    <?php
}
show_page('Challenge Administration');
