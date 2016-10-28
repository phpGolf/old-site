<?php

header('Content-Type: text/html; charset=ISO-8859-1');

if (!defined('INDEX')) {
    header('location: /');
}

function ordUTF8($c, $index = 0, &$bytes = null) {
    $len = strlen($c);
    $bytes = 0;
    if ($index >= $len) {
        return false;
    }
    $h = ord($c{$index});
    if ($h <= 0x7F) {
        $bytes = 1;
        return $h;
    } else if ($h < 0xC2) {
        return false;
    } else if ($h <= 0xDF && $index < $len - 1) {
        $bytes = 2;
        return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
    } else if ($h <= 0xEF && $index < $len - 2) {
        $bytes = 3;
        return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6 | (ord($c{$index + 2}) & 0x3F);
    } else if ($h <= 0xF4 && $index < $len - 3) {
        $bytes = 4;
        return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                                | (ord($c{$index + 2}) & 0x3F) << 6
                                | (ord($c{$index + 3}) & 0x3F);
    } else {
        return false;
    }
}

$normal = '';
$invert = '';
$find = array("\n", "\r", "\t");
$replace = array('\n', '\r', '\t');

if (isset($_POST['submit_invert']) && !empty($_POST['normal'])) {
    $invert = str_replace("ò", "", htmlspecialchars(~$_POST['normal']));
    $normal = str_replace("", '', htmlspecialchars($_POST['normal']));
}
if (isset($_POST['submit_normal']) && !empty($_POST['inverted'])) {
    $invert = str_replace("\r", "", htmlspecialchars($_POST['inverted']));
    $normal = str_replace($find, $replace, htmlspecialchars(~$_POST['inverted']));
}
?>

<h1>Stuff</h1>
<h3>Invert Strings</h3>
What is this useful for? Read the <a href="/tips">Tips & Tricks</a> section.<br /><br />
<form action="" method="post">
Normal string: <br />
<textarea name="normal" cols="40" rows="3"><?=$normal?></textarea><br />
Inverted string: <br />
<textarea name="inverted" cols="40" rows="3"><?=$invert?></textarea><br />
<input name="submit_invert" type="submit" value="To invert" />
<input name="submit_normal" type="submit" value="To normal" />
</form>

<?php
show_page('Stuff');
?>
