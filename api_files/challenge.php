<?php
if(!defined('INDEX')) {
    header('Location: /');
    die('No access here');
}
$challenge_id = getChallengeIdFromSafeName($_GET['value'][1]);
if(!isChallenge($challenge_id)) {
    error(404);
}
switch($_GET['value'][2]) {
    case 'get':
        show_api(200,'text/plain');
        break;
    case 'info':
        $challenges = challenges();
        $challenge = $challenges[$challenge_id];
        switch ($challenge['trim_type']) {
            case 0: $trim_type = 'no trim'; break;
            case 1: $trim_type = 'right trim'; break;
            case 2: $trim_type = 'left trim'; break;
            case 3: $trim_type = 'full trim'; break;
        }
        $info['name']=$challenge['name'];
        $info['status']=(isChallengeOpen($challenge_id)) ? 'open' : 'closed';
        $info['type']=$challenge['type'];
        $info['trim']=$trim_type;
        $info['rating']=$challenge['ups']-$challenge['downs'];
        $info['description'] = $challenge['instructions'];
        foreach(getChallToplist($challenge_id,20) as $rank => $place) {
            $info['toplist'][$rank]['rank'] = $rank+1;
            $info['toplist'][$rank]['username'] = $place['username'];
            $info['toplist'][$rank]['size'] = $place['size'];
            $info['toplist'][$rank]['points'] = round($place['points']);
        }
        echo makeOutput($info,'json');
        show_api(200,'application/json');
        break;
    case 'ajax':
        switch ($_GET['value'][3]) {
            case 'rate':
                $http = 200;
                $response = array();
                if(!$_SESSION['id']) {
                    msg('You do not have access to view this page',0);
                    echo makeOutput($response,'json');
                    show_api($http,'application/json');
                    exit;
                }
                $direction['up'] = true;
                $direction['down'] = false;
                if($direction[$_GET['value'][4]] === NULL) {
                    msg('Not an valid direction',0);
                } else {
                    if(rateChallenge($challenge_id,$direction[$_GET['value'][4]])) {
                        msg('Your vote have been saved',1);
                    }
                }
                echo makeOutput($response,'json');
                show_api($http,'application/json');
                break;
            case 'rating':
                $challenges = challenges();
                $challenge = $challenges[$challenge_id];
                $respons['rating'] = $challenge['ups'] - $challenge['downs'];
                echo makeOutput($respons,'json');
                show_api();
            break;
            default:
                error(404);
                break;
        }
        break;
    default:
?>
USAGE:
get: Get example input and output
info: Get info about challenge
ajax: AJAX requests
:USAGE END
<?php
        show_api(200,'text/plain');
        break;
}
