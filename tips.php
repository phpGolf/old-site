<?php
if(!defined('INDEX')) {
    header('Location: /');
}

header('Content-Type: text/html; charset=utf-8');

include ('functions/function_bbcode.php');
?>

<h1>Tips & Tricks</h1>

<p>This tips & tricks-chart is written by <a href="/user/JWvdVeer">JWvdVeer</a> and <a href="/user/iWinkWim">iWinkWim</a>. There are many tips to create a near-optimal solution. We don't think this is an exhausting list of all tips, but these might be the most important ones. Of course you might contact us to supplement the list with other tips&amp;tricks.</p>
<p>There are many examples included in this tip&amp;trick-chart. Most examples are written for the example and are not meant as an optimal solution for the given problem. Most tricks are written for the situation that error reporting is set to E_ALL & ~E_NOTICE, or in each case E_NOTICE is not on. So keep that in mind. Even not all tricks are to be applied in all contexts. So although we give some tip&amp;tricks, you should be able to use your common sense.</p>

<h3>Common Tips</h3>
<ol>
    <li>In dutch we have the proverb: `Who isn't strong, has to be smart`. In English it is comparable to the proverb `necessity is the mother of invention`. Analogue in PHP-golfing: A good idea is worth more than good coding. The most challenges you will be good at are accomplished by good coding, but by good inspiration. So take your time to think about a solution.</li>
    <li>Know the behavior of PHP. PHP behaves in a consistent way, so you can always predict the outcome of the code, if you know the behavior of PHP. Sometimes you might even exploit known odd behavior of PHP.</li>
    <li>As almost all things: PHP-golfing is learning by doing. Don't expect the first time you're doing a golf you will be the best of all in one time. So don't be hard on yourself and take the time needed to learn.</li>
    <li>Know the environment your code will run in. Most challenges do use error_reporting E_ALL & ~E_NOTICE. So notices and warnings about the fact that functions are deprecated are accepted, but any other warning or error isn't. With this setting of error reporting you don't have to care about undefined array indexes or undefined variables.</li>
    <li>Use Google. Some challenges are copies of other challenges on the Internet. Or they are much the same. So you can get some inspiration of sometimes complete challenges.</li>
    <li>Most of the time the less variables you use in your code,will result in a smaller solution in bytes. Think about your variables: do I really need them, or might the value of this variable also be derived from any other variable? Or can I combine two values into one in order to save even 1 byte?</li>
</ol>

<h3>Control Structures</h3>
<ol>
    <li>Know where you need brackets and where you don't. If a statement is only one line, you don't need brackets. You even might try to keep your statements one line if possible.<br /><br />
        Compare these two examples that print chars below 1000 that do have a nine in it.<br />
        <?=bbcode('[code]<?for(;++$i<1000;){if(is_int(strpos($i,\'9\'))){echo$i."\n";}}[/code]')?>
        <?=bbcode('[code]<?for(;++$i<1000;)if(is_int(strpos($i,\'9\')))echo$i."\n";[/code]')?><br />
    </li>
    <li>Never use a while loop. A for loop is always at least as short as a while loop, most times it is shorter. Except perhaps in case of a do-while-loop, but we never came across this in any of the challenges we've ever done. And we've done a lot.
See for example these two code-snippets (a very not optimal version of the ROT13-challenge):<br />
        <?=bbcode('[code]<?$a=WORD;while($a[$i]){$b=ord($a[$i++]);echo chr(($b>109?-13:13)+$b);}[/code]')?>
        <?=bbcode('[code]<?for($a=WORD;$a[$i];print chr(($b>109?-13:13)+$b))$b=ord($a[$i++]);[/code]')?><br />
    </li>
    <li>Try to avoid the use of if-structure. Most times the same action can be done by using the ternary operator of PHP. The next three code-snippets are exactly the same:<br />
        <?=bbcode('[code]<?if($i==2)++$j;[/code]')?>
        <?=bbcode('[code]<?$i==2?++$j:0; # Saves one byte.[/code]')?>
        <?=bbcode('[code]<?$i-2?:++$j; # Saves another two bytes, available since PHP 5.3[/code]')?>
        <br /><br />
        Compare the examples below that both print all primes below 1000.<br />
        <?=bbcode('[code]1<?for($a=array(),$b=1;++$b<=1000;){foreach($a as$c)if($b%$c==0)continue 2;$a[]=$b;echo"\n".$b;}[/code]')?>
        <?=bbcode('[code]1<?for($a=array(),$b=1;++$b<=1000;){foreach($a as$c)continue($b%$c?0:2);$a[]=$b;echo"\n".$b;}[/code]')?><br /><br />
        This can also be used in many other contexts. So a whole if-else-structure can be done in a ternary operator. See the operator section about the ternary operator for more detailed information.<br />
    </li>
    <li>Try to avoid the need of keywords like `break` and `continue`. They need a lot of bytes, while it even might be done using a variable, that perhaps even might be used for other purposes.<br /><br />
        Again our non-optimal solution to find out all primes below 1000.<br />
        <?=bbcode('[code]1<?for($a=array(),$b=1;++$b<=1000;){foreach($a as$c)if($b%$c==0)continue 2;$a[]=$b;echo"\n".$b;}[/code]')?><br />
        <br />This is just a better piece of code, although it is far from optimal:<br />
        <?=bbcode('[code]1<?for($a=array(),$b=1;$d=++$b<=1000;){foreach($a as$c)$b%$c?:$d=0;if($d){$a[]=$b;echo"\n".$b;}}[/code]')?><br />
    </li>
    <li>Try to use as few control structures as possible. Loops for example can often be folded into one loop.</li>
</ol>

<h3>Functions</h3>
<ol>
    <li>You should (almost) never write your own functions. In most cases it is unnecessary and it costs a lot of bytes.
    So spend your time and bytes on something else.</li>
    <li>Some built-in functions of PHP should never be used. Some examples here. For each function an equivalent is given.
        <ul>
            <li><a href="http://php.net/rtrim">rtrim</a> -> <a href="http://php.net/chop">chop</a></li>
            <li><a href="http://php.net/explode">explode</a> -> <a href="http://php.net/split">split</a></li>
            <li><a href="http://php.net/implode">implode</a> -> <a href="http://php.net/join">join</a></li>
            <li><a href="http://php.net/preg_split">preg_split</a> -> split can be used most times</li>
            <li><a href="http://php.net/preg_replace">preg_replace</a> -> <a href="http://php.net/preg_filter">preg_filter</a> is one byte shorter,
            and in most cases exactly the same.</li>
            <li><a href="http://php.net/print">print</a> -> <a href="http://php.net/echo">echo</a></li>
        </ul>
        <br /><br />
        In some cases, print might be useful though. Since print can be used as a function. While echo can't, see examples below:<br />
        <?=bbcode('[code]<?for(;++$i<11;){echo str_repeat(\' \',10-$i);for($a=0;$a<$i;)echo$a,(++$a-$i?\' \':"\n");}[/code]')?>
        <?=bbcode('[code]<?for(;++$i<11;print"\n")for(print str_pad($a=0,11-$i,\' \',0);++$a<$i;)echo" $a"; # echo instead of print would give an error[/code]')?>
        <br />
        <br />
        <ul>
            <li><a href="http://php.net/lcfirst">lcfirst($a)</a> -> $a|~ß or $a|' ' if E_NOTICE is on.
            Works only when $a is a letter. This method doesn't make the difference between characters of the alphabeth and other characters.</li>
            <li><a href="http://php.net/strtoupper">strtoupper($a)</a> -> if $a is only one char, you can use: $a&ß or $a&'ß' if E_NOTICE is on.
            Works only if $a is always a letter.
            It doesn't separate letters from other chars.</li>
            <li><a href="http://php.net/ucfirst">ucfirst</a>: see strtoupper</li>
            <li>echo <a href="http://php.net/sprintf">sprintf(...)</a> -> <a href="http://php.net/printf">printf(...)</a></li>
            <li><a href="http://php.net/str_replace">str_replace</a> -> consider whether <a href="http://php.net/strtr">strtr</a> can be used</li>
            <li><a href="http://php.net/array_unique">array_unique</a> -> <a href="http://php.net/in_array">in_array</a> or other method.
            Most times this function is useless... If something has to be unique, you can do it in many different ways dependent on context.
            For strings it is shown below:</li>
        </ul>
        <br />
        All three examples below show all used letters in the string, uppercase:<br />
        <?=bbcode('[code]<?$b=array_unique(str_split(strtoupper(\'This is a string\')));sort($b);if($b[0]==\' \')unset($b[0]);echo join($b,"\n");[/code]')?>
        <?=bbcode('[code]<?for($a=\'This is a string\';$b=$a[$i++];sort($c))@in_array($b&=ß,$c)?:$b==\' \'?:$c[]=$b;echo join($c,"\n");[/code]')?>
        <?=bbcode('[code]<?for($a=count_chars(strtoupper(\'This is a string\'),3);$c=$a[$b++];)$c==\' \'?:$d[]=$c;echo join($d,"\n");[/code]')?>
        <?=bbcode('[code]<?for($a=\'This is a string\';$b=$a[$i++];)$b==\' \'?:$c[$b&=ß]=$b;sort($c);echo join($c,"\n");[/code]')?><br />
        <br />
        <ul>
            <li><a href="http://php.net/sizeof">sizeof</a> -> most times unnecessary, if needed use <a href="http://php.net/count">count</a>.</li>
            <li><a href="http://php.net/count">count</a> -> most times unnecessary:</li>
        </ul>
        <br />
        See examples below:<br />
        <?=bbcode('[code]<?for($a=array(5,24,89);$i<count($a);)echo$a[+$i++],"\n";[/code]')?>
        <?=bbcode('[code]<?for($a=array(5,24,89);$b=$a[+$i++];)echo"$b\n";[/code]')?>
        <br />
        <br />
        <ul>
            <li><a href="http://php.net/floor">floor</a> -> Often can be converted to int (int)$a for example.
            If you only want to do: echo floor($a); you might consider printf('%u',$a);</li>
            <li><a href="http://php.net/preg_replace">preg_replace</a> and <a href="http://php.net/preg_filter">preg_filter</a> do have an e-flag.
            This flag means that the replacement is being executed.</li>
        </ul>
        <br />
        <br />
        Both examples written by JWvdVeer are a solution to the <a href="http://stackoverflow.com/questions/3190914/code-golf-pig-latin">PIG-latin golf</a>:<br />
        <?=bbcode('[code]<?foreach(split(~ß,SENTENCE)as$a)echo($b++?~ß:\'\').(strpos(\' aeuio\',$a[0])?$a.w:substr($a,1).$a[0]).ay;[/code]')?>
        <?=bbcode('[code]<?=preg_filter(\'#b(([aioue]w*)|(w)(w*))b#ie\',\'"$2"?"$2way":"$4$3ay"\',SENTENCE);[/code]')?>
        <br />
        As you can see both the second is much shorter than is the first. Besides the second is even better, since it even can handle strings with punctuation.<br />
        <br />
    </li>
    <li>Sometimes you have to leave a space between echo and the argument. In those cases it is often better to use:<br />
        <?=bbcode('[code]?><?=[/code]')?>
    </li>
</ol>


<h3>Operators</h3>
<ol>
    <li>Know the precedence of operators. A table with information about the precedence might be found at: <a href="http://php.net/manual/language.operators.precedence.php">http://php.net/manual/language.operators.precedence.php</a>. This is important. Because then you know when (not) to put parentheses around your piece of code.</li>
    <li>Try to concatenate as much as possible operators when you need them. Have the variables a, b, c conditionally to be set one, d to 'None' en e has to be incremented? Then it should be:
        <?=bbcode('[code]<?condition?$e+=$a=$b=$c=1|$d=None:0;[/code]')?><br />
        Not:
        <?=bbcode('[code]<?if(condition){++$e;$a=$b=$c=1;$d=None;}[/code]')?><br />
        <br />
        Or has $b to be incremented with $c, then added to a, and is it necessary to now whether $a is odd or even after that increment?
        <?=bbcode('[code]<?echo\'$a is \',(1&$a+=$b+=$c)?odd:even;[/code]')?>
    </li>
    <li>Modulo operator (%)
        <ol>
            <li>Is a really useful operator for doing actions that only have to be done once in so many times in a loop or with some given condition. The condition to that loop is that it uses a variable you can use for this purpose.<br />
                <br />
                So if something has to be done each nine times:
                <?=bbcode('[code]<?for(;$i<100;)++$i%9?:doSomething();[/code]')?>
            </li>
            <li>Is comparable to the bitwise &-operator in the cases that if a%b is given, b is a power of two. In that case it is exactly the same as a&(b-1). So $a%8 is exactly the same as $a&7. Only the precedence of these two operators is different. So use the right one in your context.</li>
        </ol>
    </li>
    <li>Ternary operator (condition?true-action:false-action)</br />
        In many ways it's a great replacement for if-else-structures, switch-structures and even for many other uses such like setting a first value.<br />
        <br />
        See for example the way it shows the values of pow(3,n), n&lt;10, starting with n=0:
        <?=bbcode('[code]<?for(;$n++<9;)echo$a=3*$a?:1,"\n";[/code]')?><br />
        <br />
        If you do have only an if (and no else) structure, try to negate the condition. Since the middle part of the of ternary operator might be left out.<br />
        <br />
        So:
        <?=bbcode('[code]<?if($a==$b)doSomething();[/code]')?>
        Equals:
        <?=bbcode('[code]<?$a!=$b?:doSomething(); # Since PHP 5.3[/code]')?>
        Even equals:
        <?=bbcode('[code]<?$a!=$b||doSomething();[/code]')?><br />
        <br />
        Since the associativity of this operator is left, nested ternary-operators should be preferable done in the true-action, since you otherwise have to user parentheses.:
        <?=bbcode('[code]<?print$a==$b?$a==27?$b!=30?:\'This situation will never happen\':\'\':\'\';[/code]')?>
        Is often preferable above:
        <?=bbcode('[code]<?print$a!=$b?:($a!=27?\'\':$b!=30?\'\':\'This situation will never happen\'[/code]')?>
        <br />
        <br />
    </li>
    <li>Often it happens that you have multiple statements inside an if statement for example:
        <?=bbcode('[code]<?
if($c%4){
    $q++;
    print$a;
}[/code]')?>
        <br />
        <br />
        You can rewrite this to:
        <?=bbcode('[code]<?if($c%4&&$q++)print$a;[/code]')?>
        Or even better:
        <?=bbcode('[code]<?if($c%4)$q+=print$a;[/code]')?>
        For as far as we know the best in this case is:
        <?=bbcode('[code]<?$c%4?$q+=print$a:0;[/code]')?>
        <br />
        Because print always returns 1 this is a valid solution.
    </li>
</ol>


<h3>Assignment Operators</h3>
<ol>
    <li>If possible always try to use +=, -=, %=, &=, |=, etc.</li>
    <li>Mind the fact is associativity is right. So first the most right assignment operator in the expression will be executed.<br />
        <br />
        So:
        <?=bbcode('[code]<?$a+=$b%=2;[/code]')?>
        Is exactly the same as:
        <?=bbcode('[code]<?$b%=2;$a+=$b;[/code]')?>
        And not the same as:
        <?=bbcode('[code]<?$a+=$b;$b%=2;[/code]')?>
        <br /><br />
    </li>
</ol>

<h3>Bitwise Operators</h3>
<ol>
    <li>Bitwise operators are the sweetest operators PHP has, because you can save plenty of bytes with them. These operators are almost the most important operators in golfing with PHP. Therefore you should know or learn how to use these operators.:
        <br />
        All bytes consists of eight bits. A number consist, dependent on the CPU and platform you're working on, of 4 or 8 bytes, equals 32 or 64 bits. With the bitwise operators you can manipulate or check out some bytes.
        <br />
        Bitwise operators don't only work on numbers, but even on strings. This is even one of the most byte-saving properties of this kind of operators.<br />
        Look at <a href="http://php.net/manual/language.operators.bitwise.php">http://php.net/manual/language.operators.bitwise.php</a> to see how these operators work.
    </li>
    <li>Bitwise AND (&amp;)<br />
        The bitwise AND can be used for several purposes. One of these purposes has already been explained in the description of the modulo-operator.<br />
        Since it also works on strings, it can also be used to uppercase a single letter (see function strtoupper and ucfirst).
        <br />
        <br />
        It even can be used to make complicated patterns. For example, if you have to print this:<br />
        <pre>HH   HH EEEEEE LL     LL      OOOOO
HH   HH EE     LL     LL     OO   OO
HHHHHHH EEEE   LL     LL     OO   OO
HH   HH EE     LL     LL     OO   OO
HH   HH EEEEEE LLLLLL LLLLLL  OOOOO </pre>
        <br />
        <br />
        My first shot would be something like:
        <?=bbcode('[code]<?for($a=\'Æ~``>Æ```cþx``cÆ```cÆ~~~>\';$c=ord($a[+$b]);$f^4?:print~õ)for($d=HELLO,$e=7+!$f=$b++%5;$e--;)echo$c>>$e&1?$d[$f]:~ß;[/code]')?><br />
        <br />
        This script is created by this script:
        <?=bbcode('[code]<?php
$a=\'HH   HH EEEEEE LL     LL      OOOOO 
HH   HH EE     LL     LL     OO   OO
HHHHHHH EEEE   LL     LL     OO   OO
HH   HH EE     LL     LL     OO   OO
HH   HH EEEEEE LLLLLL LLLLLL  OOOOO \';

$b = array(8,7,7,7,7);
$c = 0;
$f = \'\';
while($a[$c]) {
    foreach($b as $d) {
       $e = 0;
       while($d--) {
           # This works since space is char 32 and ~ï is char 16. 32^16=48, so space^~ï = char 48 = 0 (evaluates to false).
            $e=$e<<1|($a[$c++]^~\'ï\'?1:0);
        }
        $f .= chr($e);
    }
    ++$c;
}

fwrite(fopen(\'file.php\', \'w\'), \'<?for($a=\'\'.$f.\'\';$c=ord($a[+$b]);$f^4?:print~õ)for($d=HELLO,$e=7+!$f=$b++%5;$e--;)echo$c>>$e&1?$d[$f]:~ß;\');

?>[/code]')?><br />
        <br />
        I think this example gives a good impression of the power that bitwise operators hold.
    </li>
    <li>Bitwise OR (|)<br />
        Even used for several purposes. One of them is converting letters to lowercase (see strtolower and lcfirst in section `functions`).<br />
        Mind the fact that $int|$nonNumericString==$int==true. So sometimes this might be useful, because you don't need a semicolon instead and your code might be written in one expression (for example in a ternary-operator).
    </li>
    <li>Bitwise inversion (~)<br />
        One of the most used bitwise operators. Especially for the use with strings. Since this operator makes it possible to save bytes on whitespace and characters that have a meaning to PHP.<br />
        Here a listing of the most important chars (see also the `String invertor` on the `stuff`-page).
        <ul>
            <li>Tab (char 9) -> ~ö</li>
            <li>Line-feed (char 10) -> ~õ</li>
            <li>Space (char 32) -> ~ß</li>
            <li>'!' (char 33) -> ~Þ</li>
            <li>'"' (char 34) -> ~Ý</li>
            <li>'#' (char 35) -> ~Ü</li>
            <li>'$' (char 36) -> ~Û</li>
            <li>'%' (char 37) -> ~Ú</li>
            <li>'&' (char 38) -> ~Ù</li>
            <li>''' (char 39) -> ~Ø</li>
            <li>'(' (char 40) -> ~×</li>
            <li>')' (char 41) -> ~Ö</li>
            <li>'*' (char 42) -> ~Õ</li>
            <li>'+' (char 43) -> ~Ô</li>
            <li>',' (char 44) -> ~Ó</li>
            <li>'-' (char 45) -> ~Ò</li>
            <li>'.' (char 46) -> ~Ñ</li>
            <li>'/' (char 47) -> ~Ð</li>
            <li>':' (char 58) -> ~Å</li>
            <li>';' (char 59) -> ~Ä</li>
            <li>'<' (char 60) -> ~Ã</li>
            <li>'=' (char 61) -> ~Â</li>
            <li>'>' (char 62) -> ~Á</li>
            <li>'?' (char 63) -> ~À</li>
            <li>'[' (char 91) -> ~¤</li>
            <li>'\' (char 92) -> ~£</li>
            <li>']' (char 93) -> ~¢</li>
            <li>'^' (char 94) -> ~¡</li>
            <li>'_' (char 95) -> ~  (char 160, the fact you are not able to see whitespace doesn't mean that PHP treat is as whitespace!)</li>
        </ul>
        <br />
        <br />
        This trick can of course also be used on strings with a length of more than 1 char. So especially regular expressions are a good example of a kind of strings you can save bytes on using this trick.<br />
        <br />
        So:
        <?=bbcode('[code]<?=preg_filter(\'#(.)\1+#i\',\'$1\',\'Aa striing  wiith soomee reeduundaant chaars\');[/code]')?>
        <br />
        There might be saved two bytes by doing this:
        <?=bbcode('[code]<?=preg_filter(~Ü×ÑÖ£ÎÔÜ?,~ÛÎ,\'Aa striing  wiith soomee reeduundaant chaars\');[/code]')?>
        <br />
        Such kind of script can be made in the easiest way doing this:
        <?=bbcode('[code]<?php
fwrite(fopen(\'yourfile.php\', \'w\'), \'<?=preg_filter(~\'.~\'#(.)\1+#i\'.\',~\'.~\'$1\'.\',\\\'Aa striing  wiith soomee reeduundaant chaars\\\');\');
?>[/code]')?><br /><br />
    </li>
    <li>Bitwise XOR (^)<br />
        Bitwise XOR for integers is a replacement for !=.<br />
        <br />
        So (numeric example):
        <?=bbcode('[code]<?$i!=7?:print\'$i is seven\';[/code]')?>
        Equals:
        <?=bbcode('[code]<?$i^7?:print\'$i is seven\';[/code]')?>
        <br />
        On strings it might be very useful to determine whether the given character equals a given char. This can be done by XOR the given char to '0', since '0' evaluates false.<br />
        <br />
        Example check whether char equals '_':
        <?=bbcode('[code]<?$c=_;echo\'Char is \'.($c^o?\'not \':\'\').\'an underscore\';[/code]')?>
        <br />
        This trick only can be used on one char. Since '0' evaluates false, but '00...' evaluates true.
        <br />
        <br />
        Since a^b=c, c^b=a and a^c=b, the char needed for this trick can be found by:
        <?=bbcode('[code]<?var_dump(\'0\'^$sCharToTackle);[/code]')?>
    </li>
</ol>

<h3>Strings</h3>
<ol>
    <li>Many strings don't need to be quoted when E_NOTICE is not turned on. So, this will work:
        <?=bbcode('[code]<?$a=HELLO;[/code]')?>
        <br />
        If you have a string with white-space or characters that need to be quoted, it might be worth a shot to try to invert the string.
        <?=bbcode('[code]<?$a=#[ABC]+#; # Will not work[/code]')?>
        <?=bbcode('[code]<?$a=~Ü¤¾½¼¢ÔÜ; # Will work[/code]')?>
        <br />
        Make sure to set your IDE to latin1 (ISO-8859-1 or Windows-1252) instead of UTF8 cause otherwise you will save those inverted bytes as multi-bytes which will do the opposite of what we are trying to do here: Save bytes. Besides, a small remark: it then even doesn't work...
    </li>
    <li>Strings can be accessed like arrays. So substr functionality is rarely needed.</li>
    <li>Sometimes your code will be much shorter if you invert the input-constant. Or create your output inverted, as you can see in the piano-golf example below.</li>
</ol>

<h3>Some examples of golfing on other sites</h3>
<ol>
    <li><b>Convert number to Excel-like header number:</b><br />
        Accept any positive number and convert it to an Excel-like header number. So 0 = A and 26 = AA. Let's assume it is given in constant NUM.
        See: <a href="http://stackoverflow.com/questions/4447550/convert-decimal-number-to-excel-header-like-number">http://stackoverflow.com/questions/4447550/convert-decimal-number-to-excel-header-like-number</a>
        <br />
        <br />
        First implemement it in a strict way in PHP:
        <?=bbcode('[code]<?php
$a = NUM+1;
$b = range(\'A\',\'Z\');
while($a>=1){
    $a-=1;
    $c=$b[$a%26].$c;
    $a=$a/26;
}
echo$c;
?>[/code]')?>
        <br />
        Next we're gonna make it as optimal as possible for a golf:
        <?=bbcode('[code]<?for($a=NUM+1;$a>=1;$a=$a/26)$c=chr(--$a%26+65).$c;echo$c;[/code]')?>
        <br />
        The really most optimal solution for this problem is:
        <?=bbcode('[code]<?for($a=A;$i++<NUM;++$a);echo$a;[/code]');?>
    </li>
    <li><b>Piano-golf</b><br />
        The input will be given in the constant PIANO, and consists of the note and the number of keys have to be shown. So the format will: ([ACDFG]#|[A-G])\s[1-9][0-9]*<br />
        <br />
        The challenge can be found at: <a href="http://stackoverflow.com/questions/2202897/code-golf-piano">http://stackoverflow.com/questions/2202897/code-golf-piano</a>
        First a neat implementation:
        <?=bbcode('[code]<?php
$a = PIANO; # Get input
$b = \'ABCDEFG\'; # Notes
$c = $a[1]==\'#\'?4:0; # Horizontal position.
$d = strpos($b,$a[0]); # Note position
$e = trim(substr($a,2))+($c?1:0); # Amount of keys that have to be printed.
$f = array(); # Array for lines.
while($e--){ # Each key
    while(1){ # Each column of each key.
        $h=$c%5;
        ++$c;
        $i = 0;
        while(++$i%10){ # Each row of each column of each key.
            if(($i<6) && ((($h<2) && ($d!=2) && ($d!=5)) || (($h>3) && ($d!=1) && ($d!=4)))){
                $f[$i].= \'#\';
            } else {
                $f[$i].= $h? ($i!=9?\' \':\'_\') : \'|\';
            }
        }
        if($h == 4) break;
    }
    $d = ++$d%7;
}
foreach($f as $i=>$j) echo $j,$i<6&&$d!=2&&$d!=5?\'#\':\'|\',"\n";
?>[/code]')?>
        <br />
        A shorter solution might be:
        <?=bbcode('[code]<?$e=45*substr($a=PIANO,2+$d=!($a[1]^~ì))+9+$d*45;$j=9*$c=4*$d;for($b=ord($a[0])-65,--$c;$j<$e;$f[$i=$j++%9].=($c=($c+!$i)%5)%4<2&$i>3&$b%3!=2?Ü:($c?$i?ß: :ƒ))$j%45-36?:$b=++$b%7;for(;$a=$f[$i--];)echo~$a,~õ;[/code]')?>
        <br />
        Created by this script:
        <?=bbcode('[code]<?php
fwrite(fopen(\'file.php\',\'w\'),\'<?$e=45*substr($a=PIANO,2+$d=!($a[1]^~ì))+9+$d*45;$j=9*$c=4*$d;for($b=ord($a[0])-65,--$c;$j<$e;$f[$i=$j++%9].=($c=($c+!$i)%5)%4<2&$i>3&$b%3!=2?\'.~\'#\'.\':($c?$i?\'.~\' \'.\':\'.~_.\':\'.~\'|\'.\'))$j%45-36?:$b=++$b%7;for(;$a=$f[$i--];)echo~$a,~õ;\');
?>[/code]');?>
    </li>
</ol>
 
<?php
show_right();
show_page('Tips &amp; Tricks');
?>
