<?php

if (is_numeric($_GET['value'][1])) {
    $stmt = $PDO->prepare('SELECT a.valid, a.user_id, a.size, c.name, c.enddate
                           FROM attempts a, challenges c 
                           WHERE a.challenge_id = c.id AND a.id = :id');
    $stmt->execute(array(':id' => $_GET['value'][1]));
    $row = $stmt->fetch();
?>

<html>
    <head>
        <title>Valid output for <?=$row['name']; ?></title>
    </head>
    <body style="background-color:#123456;font-size:12px;">
        <div style="background-color:#E8E8E8; margin:5px;">
        <?php
        if ($row['user_id'] == $_SESSION['id'] || access('show_submissions') || ($row['type'] == 'protected' && strtotime($row['enddate']) < time())) {
            print $row['valid'];
        } else {
        ?>
            <h1>This is not your code.</h1>
        <?php
        }
        ?>
        </div>
    </body>
</html>

<?php
}
?>
