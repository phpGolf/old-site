<?php

function bbcode($string) {
    $pattern = array(
                 '/\[b\](.*?)\[\/b\]/mis',
                 '/\[i\](.*?)\[\/i\]/mis',
                 '/\[u\](.*?)\[\/u\]/mis',
                 '/\[quote\](.*?)\[\/quote\]/mis',
                 '/\[color=(.*?)\](.*)\[\/color\]/mis',
                 '/\[left\](.*?)\[\/left\]/mis',
                 '/\[center\](.*?)\[\/center\]/mis',
                 '/\[right\](.*?)\[\/right\]/mis',
                 '/\[size=(\d{1,2})\](.*)\[\/size\]/mis',
                 '/\[url=((https?|ftp|irc):\/\/(www\d?)?.{3,}?)\](.*?)\[\/url\]/mis',
                 '/\[img]((https?|ftp)\:\/\/(www\d?)?.{3,}?)\[\/img\]/mis');

    $tag = array(
                 '/\[b\]/i',
                 '/\[i\]/i',
                 '/\[u\]/i',
                 '/\[quote\]/i',
                 '/\[color=(.*)\]/i',
                 '/\[left\]/i',
                 '/\[center\]/i',
                 '/\[right\]/i',
                 '/\[size=(\d{1,2})\]/i',
                 '/\[url=((https?|ftp|irc):\/\/(www\d?)?.{3,}?)\]/i',
                 '/\[img\]/i');

    $replace = array(
                 '<span style="font-weight:bold;">$1</span>',
                 '<span style="font-style:italic;">$1</span>',
                 '<span style="text-decoration:underline;">$1</span>',
                 '<blockquote><fieldset><legend>Quote</legend>$1</fieldset></blockquote>',
                 '<span style="color: $1;">$2</span>',
                 '<div align="left">$1</div>',
                 '<div align="center">$1</div>',
                 '<div align="right">$1</div>',
                 '<span style="font-size:$1px">$2</span>',
                 '<a href="$1" target="_blank">$4</a>',
                 '<a href="$1" target="_blank"><img src="$1" border="0" alt="" /></a>');

    $string = htmlspecialchars($string);
    foreach ($pattern as $key => $value) {
        preg_match_all($tag[$key], $string, $m);
        for ($i = 0; $i < count($m[0]); $i++)  {
            $string = preg_replace($pattern[$key], $replace[$key], $string);
        }
    }
    return nl2br(highlightCode($string));
    #return $string;
}

function highlightCode($string) {
    preg_match_all('/\[code\]/mis', $string, $m);
    for ($i = 0; $i < count($m[0]); $i++) {
        $string = preg_replace_callback(
            '/\r?\n?\[code\](.*?)\[\/code\]\r?\n?/mis',
            create_function(
                '$matches',
                'return \'<div style="background-color:#EEEEEE;margin:3px;padding:3px;border:1px inset;overflow:auto;">\'.highlight_string(htmlspecialchars_decode(trim($matches[1])), true).\'</div>\'; '
            ),
            trim($string)
        );
    }
    return trim($string);
}

?>
