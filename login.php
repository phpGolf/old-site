<?php
if(!defined('INDEX')) {
    header('location: /');
}
if($_SESSION['id']) {
    error(404);
}
include_func('recovery');

if(isset($_POST['register_submit'])) {
    if ($_POST['password1'] == $_POST['password2']) {
        register($_POST['username'],$_POST['password1'],$_POST['email']);
    } else {
        msg('Password and retyped password is not the same.', 0);
    }
} elseif(isset($_POST['login_submit'])) {
    login_valid($_POST['username'],$_POST['password'],1);
    include_site('main');
    exit;
} elseif(isset($_POST['recover_submit'])) {
    sendRecoverMail($_POST['email']);
}
?>
<h1>Login</h1>
<form method="post" action="">
    <input name="username" type="text" value="username" onfocus="if(this.value=='username')this.value='';" />
    <input name="password" type="text" value="password" onfocus="if(this.value=='password')this.value='';this.type='password';" />
    <input name="login_submit" type="submit" value="Login" />
</form>

<h1>Register a new account</h1>
<!--<i>* Your random generated password will be sent to you by email.</i>-->
<br>
<form method="post" action="">
    <input name="username" type="text" value="username" onfocus="if(this.value=='username')this.value='';" />
    <input name="email" type="text" value="email@email.com" onfocus="if(this.value=='email@email.com')this.value='';" /><br />
    <input name="password1" type="text" value="password" onfocus="if(this.value=='password')this.value='';this.type='password';" />
    <input name="password2" type="text" value="retype password" onfocus="if(this.value=='retype password')this.value='';this.type='password';" />
    <input name="register_submit" type="submit" value="Register" />
</form>

<h1>Forgotten your password?</h1>
<br>
<form method="post" action="">
    <input name="email" type="text" value="email@email.com" onfocus="if(this.value=='email@email.com')this.value='';" /><br />
    <input name="recover_submit" type="submit" value="Recover" />
</form>
<?php
show_right();
show_page('Login');
?>
