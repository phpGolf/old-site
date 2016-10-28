<?php

if (is_numeric($_GET['value'][1])) {
    $stmt = $PDO->prepare('SELECT a.result,a.valid, a.input, a.user_id, a.size, c.name 
                       FROM attempts a, challenges c 
                       WHERE a.challenge_id = c.id AND a.id = :id');
    $stmt->execute(array(':id' => $_GET['value'][1]));
    $row = $stmt->fetch();
?>
<html>
    <head>
        <title>The difference from your output and the valid output (<?=$row['name']; ?>)</title>
        <script src="/js/jquery.js" language="javascript"></script>
        <script src="/js/debugging.js" language="javascript"></script>
    </head>
    <body style="background-color:#123456;">
        <?php
        if ($row['user_id'] == $_SESSION['id'] || access('show_submissions')) {
            $valid = explode("\n",str_replace("\r",'',$row['valid']));
            $result = explode("\n",str_replace("\r",'',$row['result']));
        ?>
        <table cellspacing="0" cellpadding="0" style="background-color:#E8E8E8; margin:5px; width:100%;">
            <tr>
                <th style="text-align:left;">Your code result</th>
                <th style="text-align:left;">Valid result</th>
            </tr>
            <tr>
                <td style="text-align:left;">Input: 
                <?=($row['input']) ? '<a href="http://'.DOMAIN.'/view/input/'.$_GET['value'][1].'">Input</a>' : 'No input'?></td>
                <td style="text-align:left;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:left;">User MD5sum: <?=md5($row['result'])?></td>
                <td style="text-align:left;">Valid MD5sum: <?=md5($row['valid'])?></td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align:left; font-size:10px; vertical-align:top;"><pre><?php
                foreach ($result as $key => $line) {
                    if ($line != $valid[$key]) {
                        echo '<span class="user" style="color: red; vertical-align: top">'.htmlspecialchars($line)."</span>\n";
                    } else {
                        echo '<span class="user">'.htmlspecialchars($line)."</span>\n";
                    }
                    
                }
                ?></pre></td>
                <td style="border: 1px solid black; vertical-align: top; text-align:left; font-size:10px;"><pre><?php
                foreach ($valid as $key => $line) {
                    echo '<span class="valid">'.htmlspecialchars($line)."</span>\n";
                }
                ?></pre></td>
            </tr>
            <tr>
                <td style="text-align:left;">User controls</td>
                <td style="text-align:left;">Valid controls</td>
            </tr>
            <tr>
                <td style="text-align:left;">Show whitespace <input type="checkbox" class="user" name="space"></td>
                <td style="text-align:left;">Show whitespace <input type="checkbox" class="valid" name="space"></td>
            </tr>
        </table>
        <?php
        } else {
        ?>
            <h1>This is not your code.</h1>
        <?php
        }
        ?>
    </body>
</html>

<?php
}
?>
