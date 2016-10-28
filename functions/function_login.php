<?php
if(!defined('INDEX')) {
    header('location: /');
}

//Sets sessions, cookies etc
// $id = userid
// $username = username
// $hash = Autologin hash
// $level = Userlevel
// $permissions = Permissions that will be set
// $remember = remember the user
function login($id,$username,$hash,$level,$timezone,$permissions=false,$remember=false) {
    $PDO = &DB::$PDO;
    $_SESSION['id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['permissions'] = $permissions;
    $_SESSION['userLevel'] = $level;
    $_SESSION['hash'] = $hash;
    $_SESSION['timezone'] = $timezone;
    if($remember) {
        setcookie('autologin',$hash,strtotime('+2months'),'/');
    }
    //Save login
    $pre = $PDO->prepare('INSERT INTO logins SET user_id=:user_id, ipa=INET_ATON(:ip), logintime=NOW()');
    $pre->execute(array(':ip' => $_SERVER['REMOTE_ADDR'], ':user_id' => $id));
    //Clear http cache
    clearHttpCache();
    return true;
}

//Validating the username and password
// $username = username
// $password = password
// $remember = remember the user
function login_valid($username,$password,$remember=false) {
    $PDO = &DB::$PDO;
    $pre = $PDO->prepare('SELECT id,username,permissions,hash,`group`,timezone FROM users WHERE username=:username AND password=:password');
    $pre->execute(array(':username' => $username,
                        ':password' => md5($password)));
    $user = $pre->fetchAll();
    if(count($user) == 0) {
        msg('Wrong username/password',0);
        return false;
    }
    $user = $user[0];
    //Make new hash
    $hash = randStr(128);
    $PDO->exec('UPDATE users SET hash="'.$hash.'" WHERE id='.$user['id']);
    $groups = groups();
    $permissions = array_merge(convertPermissions(($user['permissions'])),getGroupPermissions($user['group']));
    login($user['id'],$user['username'],$hash,$groups[$user['group']]['order'],$user['timezone'],$permissions,$remember);
    msg('Welcome '.$user['username']);
    return true;
}

//Checks for cookie and login the user
function autologin() {
    $PDO = &DB::$PDO;
    if(!$_SESSION['id'] && $_COOKIE['autologin']) {
        $hash = $_COOKIE['autologin'];
        $pre = $PDO->prepare('SELECT id,username,permissions,`group`,timezone FROM users WHERE hash=:hash');
        $pre->execute(array(':hash' => $hash));
        $user = $pre->fetch();
        if(count($user) == 0) {
            return false;
        }
        //Make new hash
        $hash = randStr(128);
        $PDO->exec('UPDATE users SET hash="'.$hash.'" WHERE id='.$user['id']);
        date_default_timezone_set($user['timezone']);
        $permissions = array_merge(convertPermissions(($user['permissions'])),getGroupPermissions($user['group']));
        $groups = groups();
        return login($user['id'],$user['username'],$hash,$groups[$user['group']]['order'],$user['timezone'],$permissions,true);
    }
}

//Register an user
// $username = wanted username
// $password = plaintext password
// $email = email
// $sendpass = send password to user (forced if $password is false)
function register($username,$password,$email,$sendpass=false) {
    $PDO = &DB::$PDO;
    
    //Check username
    if(isUsername($username)) {
        msg('Username is taken',0);
        $E = true;
    }
    if(!preg_match('/^[a-zA-Z0-9]{3,32}$/',$username)) {
        msg('Username is invalid',0);
        $E = true;
    }
    
    //Check email
    if(!preg_match('/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-+]+(\.[a-z0-9-+]+)*(\.[a-z+]{2,4})$/',$email)) {
        msg('Email is invalid',0);
        $E = true;
    } else {
        $pre = $PDO->prepare('SELECT id FROM users WHERE email=:email');
        $pre->execute(array(':email' => $email));
        if(count($pre->fetchAll()) != 0) {
            msg('Email is already in use',0);
            $E = true;
        }
    }
    //Check password
    if(!$password) { //Make random password
        $sendpass=true;
        $password = randstr(8);
    }
    $hash = randStr(255);
    
    if(strlen($password) < 5) {
        msg('Password is too short',0);
        $E = true;
    }
    
    if(!$E) {
        //Get country
        if(!$country = geoip_country_name_by_name($_SERVER['REMOTE_ADDR'])) {
            $country = 'World';
        }

        //Insert into DB
        $pre = $PDO->prepare('INSERT INTO users (username,password,email,regdate,country,hash) VALUES (:username,:password,:email,CURDATE(),:country,:hash)');
        $pre->execute(array(':username' => $username,
                            ':password' => md5($password),
                            ':email' => $email,
                            ':country' => $country,
                            ':hash' => $hash));
        
        //Send email
         mail($email, 'phpGolf User Info', 
                            "Here are your login information at phpgolf.org\r\n
                            Username: " . $username . "\r\n
                            You can change your password on your profile\r\n\r\n",
                            "FROM: noreply@phpgolf.org\r\n");

        #msg('You are now registered!<br>Your password will be sent to your email.');
        msg('You are now registered!<br>You can now login.');
        users(true);
        return true;
    } else {
        msg('Registration failed',0);
        return false;
    }
}

//Logging out the user
// $id = userid for the user to be logged out, false = $_SESSION['id'];
function logout() {
    if($_SESSION['id']) {
        session_destroy();
        unset($_SESSION);
        setcookie('autologin','deleted',time()-3600);
        //Clear http cache
        clearHttpCache();
        return true;
    } else {
        return false;
    }
}
?>
