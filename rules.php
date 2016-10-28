<?php
if(!defined('INDEX')) {
    header('location: /');
}
?>
<h1>Rules & Guidelines</h1>
<pre>
<h3>Bugs</h3>
    There is no guarentee that there are no bugs.
    If we find bugs on a challenge we will revalidate all submissions on that challenge.

    If you find any security issues, bugs etc, don't exploit it.
    Please inform us right away instead.


<h3>Charset</h3>
    To get the points right, we advise you to use ISO-8859-1 or Windows-1251 
    in your text editor.

<h3>Points</h3>
    Points are calculated with &lt;best result&gt; / &lt;your result&gt; * 1000.
    
    If two or more users have the same amount of points, the user who uploaded first
    will be sat as leader.

    The size of the file you upload will not count the trailing newline.
    
    Your script will be evaluated ten times. If it fails one time, it fails overall.
    
    Points from closed challenges will not count on the site ranking (top250).

<h3>Input</h3>
    Some challenges have input, you get the content by using 
    the constant named in the challenge description.
    
    You are not to set the constant yourself, we set it for you.

<h3>Challenge Types</h3>
    Private challenges:
        No one can see others submissions.
        There is no end date.

    Public challenges:
        Everyone can see others submissions.
        On new submissions the code must be smaller then previous attempts.
        The top250 list will not include submissions from public challenges.
        There is no end date.

    Protected challenges:
        Same as private, but it has an end date.
        After this endate the challenge will be closed and
        submissions will be shown as public.

<h3>Trimming</h3>
    We trim the result of the submitted code and the servers code.
    Challenges may have different forms of trimming to prevent exploits,
    but in most challenges we use full trim. (<a href="http://php.net/trim">trim()</a>)

<h3>PHP</h3>
    The php binary will only have core functions, 
    no custom extensions is available (PECL, PEAR).

    Some core functions is disabled for security reasons on all challenges.
    
    Particular challenges has disabled functions to not make it too easy.

    Your script will not have internet access when being run.

    The code can not generate any warnings from the PHP interpreter itself. 

    You can assume that the environment is correctly set up when
    your code is invoked.

<h3>Server Settings</h3>
    <?php
    $mem = new cache;
    $mem->key = 'ServerInfo';
    if(!$data = $mem->get()) {
        $ini = parse_ini_file('/home/phpgolf/validation/php.ini');
        $php = exec("/home/phpgolf/php-5.3.3-secure/bin/php -v | head -n 1 | awk '{print $2}'");
        $info .=  'OS = Linux (Ubuntu 10.10 32bit)'."\n";
        $info .=  '    PHP version: '.$php."\n";
        $info .=  '    short_tags = '.($ini['short_open_tag'] ? 'On' : 'Off')."\n";
        $info .=  '    precision = '.$ini['precision']."\n";
        $info .=  '    allow_url_fopen = '.($ini['allow_url_fopen'] ? 'On' : 'Off')."\n";
        $info .=  '    max_input_time = '.$ini['max_input_time']."\n";
        $info .=  '    max_execution_time = '.$ini['max_execution_time']."\n";
        $info .=  '    magic_quotes_gpc = '.($ini['magic_quotes_gpc'] ? 'On' : 'Off')."\n";
        $info .=  '    memory_limit = '.$ini['memory_limit']."\n";
        $info .=  '    error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED'."\n";
        $mem->set(0,$info,0,(3600*24));
    } else {
        $info = $data;
    }
    unset($mem);
    echo $info;
    ?>
</pre>
<?php
show_right();
show_page('Rules');
?>
