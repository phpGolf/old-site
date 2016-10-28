<?php

function sendRecoverMail($user_mail) {
    if (empty($user_mail)) {
        error('Email field empty.');
        return false;
    }
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(array(':email' => $user_mail));
    list($user_id) = $stmt->fetch();

    if (!$user_id) {
        error(0, 'Not found', 'Email not in the database.');
        return false;
    }
    $hash = randStr(128);
    $stmt = $PDO->prepare('UPDATE users SET recover_hash = :hash WHERE id = :userid');
    $stmt->execute(array(':userid' => $user_id, ':hash' => $hash));

    $headline = 'phpGolf - Password Recovery';
    $message =  '<html>
                <body>
                <h2>phpGolf - Password Recovery</h2>
                <p>You (or someone) have requested to recover your password at <a href="http://phpgolf.org">phpgolf.org</a><br /><br />

                Since the passwords in the datebase is encrypted it will be impossible to send you the password, <br />
                but we can make a new password for you.<br /><br />

                <a href="http://phpgolf.org/recovery/id/'.$user_id.'/hash/'.$hash.'">
                Click here to get a new password.</a><br /><br /></p>
                </body>
                </html>';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: phpgolf2@gmail.com\r\n";

    if (mail($user_mail, $headline, $message, $headers)) {
        msg('Recovery email sent.');
    } else {
        error(0, 'Error', 'Could not send email. Contact an admin.');
    }
}

function validateRecoverHash($user_id, $hash) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT id, email, username, password, recover_hash FROM users WHERE id = :userid');
    $stmt->execute(array(':userid' => $user_id));
    list($user_id, $user_email, $user_username, $user_password, $user_recover_hash) = $stmt->fetch();
    if ($user_recover_hash == $hash) {
        $new_password = randStr(10);
        $new_password_hash = md5($new_password);
        $update = updatePassword($user_id, $new_password_hash);
        if ($update) {
            $headline = 'phpGolf - Your new password';
            $message = '<html>
                        <body>
                        <h2>phpGolf - Your new password</h2>
                        <p>Here are your new login information at <a href="http://phpgolf.org">phpgolf.org</a>:<br />
                        Username -> '.$user_username.'<br />
                        Password -> '.$new_password.'<br /><br />

                        <a href="http://phpgolf.org/user/'.$user_username.'">Click here to change your password.</a></p>
                        </body>
                        </html>';

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "From: phpgolf2@gmail.com\r\n";
            if (mail($user_email, $headline, $message, $headers)) {
                $stmt = $PDO->prepare("UPDATE users SET recover_hash = '' WHERE id = :userid");
                $stmt->execute(array(':userid' => $user_id));
                msg('Your new password was sent successfully.');
            } else {
                error(0, 'Error', 'Could not send email.');
            }
        } else {
            error(0, 'Error', 'Could not change password');
        }
    } else {
        error(0, 'Error', 'Wrong hash.');
    }
}

function updatePassword($user_id, $newpass) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE users SET password = :newpass WHERE id = :userid');
    $result = $stmt->execute(array(':newpass' => $newpass, ':userid' => $user_id));
    if ($result) {
        return true;
    }
}

?>
