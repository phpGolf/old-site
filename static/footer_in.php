<?php
if (!defined('INDEX')) {
    header('location: /');
}
if(!include_func('stats')) {
    error();
}
?>        </div>
    </div>
    <div id="footer">
        &copy; Copyright phpGolf 2010 - <?=date('Y')?> | Users online: <?=$ONLINE?> | Members: <?=getTotalMembers();?> | Total submissions: <?=getTotalSubmissions();?> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y8PX4FETFDKS6">Support us</a>
    </div>
    <?php
    if (access('show_pdolog') || access('show_cachelog')) {
    ?>
    <div style="background-color: #123456; border: 3px solid #224466; margin-top: 10px; color: grey; padding-left: 5px; margin-bottom: 50px;">
    <?php
    //PDO Log
    if (access('show_pdolog')) {
        $Log = LogPDO::getLog();
        foreach($Log as $Line) {
		    $Time+=$Line['Time'];
	    }
	    $Count = count($Log);
        ?>
        <div style="background-color: #224466; color: white; font-weight: bold; padding: 5px; margin-left: -5px;">
            <a class="spoiler" name="logpdo">Show/Hide <?=$Count?> querie(s) ran in <?=round($Time,3)?> ms</a>
        </div>
        <div name="spoiler_logpdo" style="display:none;">
        <?php
       
	    ?><table style="width:100%;">
	    <tr>
	        <th>Type</th>
		    <th>Query (<?=$Count?> queries)</th>
		    <th>Time (<?=round($Time,3)?> ms)</th>
	    </tr>
	    <?php
	    foreach ($Log as $Line) {
		    ?>
		    <tr>
		        <td style="border: 1px solid #224466;"><?=$Line['Type']?></td>
			    <td style="border: 1px solid #224466;"><pre><?=$Line['Query']?></pre></td>
			    <td style="border: 1px solid #224466;"><?=$Line['Time']?></td>
		    </tr>
		    <?php
	    }
	    echo '</table>';
	    ?>
        </div>
        <?php
    }//PDO end
    //memcache log
    if (access('show_cachelog')) {
        $Log = cache::getLog();
        $Bytes = 0;
        foreach ($Log as $Name => $Element) {
            if (is_array($Element)) {
                $Bytes += countBytes($Element);
            } else {
                $Bytes += strlen($Element);
            }
        }
        ?>
        <div style="background-color: #224466; color: white; font-weight: bold; padding: 5px; margin-left: -5px;">
            <a class="spoiler" name="logcache">Show/Hide <?=count($Log)?> (<?=$Bytes?> bytes) cached values</a>
        </div>
        <div name="spoiler_logcache" style="display:none;">
            <?php
            foreach ($Log as $Name => $Element) {
                if (is_array($Element)) {
                    $Bytes = countBytes($Element);
                } else {
                    $Bytes = strlen($Element);
                }
                echo '<a class="spoiler" name="logcache_'.$Name.'">'.$Name." ($Bytes bytes)</a>:<br>";
                ?>
                <div name="spoiler_logcache_<?=$Name?>" style="display:none; border-bottom: 1px solid black">
                <pre>
<?=htmlspecialchars(print_r($Element,1))?>
                </pre>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }//memcache end
    if (access('show_server_arr')) {
        ?>
        <div style="background-color: #224466; color: white; font-weight: bold; padding: 5px; margin-left: -5px;">
            <a class="spoiler" name="logserver">Show/Hide $_SERVER variables</a>
        </div>
        <div name="spoiler_logserver" style="display:none;">
            <pre>
<?php
print_r($_SERVER);
?>
            </pre>
        </div>
        <?php
    }//server end
    if (access('show_request_arr')) {
        ?>
        <div style="background-color: #224466; color: white; font-weight: bold; padding: 5px; margin-left: -5px;">
            <a class="spoiler" name="requests">Show/Hide $_REQUEST variables</a>
        </div>
        <div name="spoiler_requests" style="display:none;">
            <pre>
<?php
print_r(array('GET'=>$_GET,'POST'=>$_POST));
?>
            </pre>
        </div>
        <?php
    }//request end
    if (access('show_session_arr')) {
        ?>
        <div style="background-color: #224466; color: white; font-weight: bold; padding: 5px; margin-left: -5px;">
            <a class="spoiler" name="session">Show/Hide $_SESSION/$_COOKIE variables</a>
        </div>
        <div name="spoiler_session" style="display:none;">
            <pre>
<?php
print_r(array('SESSION'=>$_SESSION, 'COOKIE' => $_COOKIE));
?>
            </pre>
        </div>
        <?php
    }//session end
    ?>
    </div>
    <?php
    }
    ?>
</body>
</html>
