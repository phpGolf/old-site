<?php
if(!defined('INDEX')) {
    header('location: /');
}
/*Get challenge list*/
// $all = list all challenges for everyone
// $reset = On true it get the data all over again
function challenges($all=false,$reset = false) {
    $PDO = &DB::$PDO;
    static $challenges = array();
    if ($reset) {
        $challenges = array();
    }
    if (count($challenges) == 0) {
        $challs = $PDO->query('
                        SELECT 
                            c.id,c.name,c.instructions,c.active,(c.open AND ((c.enddate IS NULL) OR (c.enddate > NOW()))),c.enddate,c.type,c.output_type,
                            c.disabled_func,c.constant,c.input,c.output,c.trim_type,c.topic_id,
                        (SELECT count(*) FROM challenges_rating WHERE challenge_id=c.id AND direction) AS ups,
                        (SELECT count(*) FROM challenges_rating WHERE challenge_id=c.id AND NOT direction) AS downs
                        FROM challenges AS c');
        while (list($id,$name,$instructions,$active,$open,$enddate,$type,$output_type,
                    $disabled_func,$constant,$input,$output,$trim_type,$topic_id,$ups,$downs)=$challs->fetch()) {
            $safename = preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$name));
            $challenges[$id] = array('name' => $name,
                                     'instructions' => $instructions,
                                     'safename' => $safename, 
                                     'active' => (boolean)$active,
                                     'open' => (boolean)$open,
                                     'enddate' => $enddate,
                                     'trim_type' => $trim_type,
                                     'type' => $type,
                                     'output_type' => $output_type,
                                     'constant' => $constant,
                                     'input' => $input,
                                     'output' => $output,
                                     'disabled_func' => $disabled_func,
                                     'topic_id' => $topic_id,
                                     'ups' => $ups,
                                     'downs' => $downs);
        }
    }
    if ($all || access('show_unactive_challenges')) {
        return $challenges;
    } else {
        foreach($challenges as $id => $data) {
            if($data['active'] == true) {
                $return[$id] = $data;
            }
        }
        return $return;
    }
    
}

// Checks if the challenge is open (returns true if the end date isn't expired)
function isChallengeOpen($challenge_id) {
    $challenges = challenges();
    $enddate = strtotime($challenges[$challenge_id]['enddate']);
    $type = $challenges[$challenge_id]['type'];
    $open = $challenges[$challenge_id]['open'];
    if(!$open) {
        return false;
    }
    if ($type == 'public' || $type == 'private') {
        return true;
    }
    if ($type == 'protected') {
        if ($enddate > time()) {
            return true;
        } else {
            return false;
        }
    }
}

//OLD, use getTop20() and getTop250()
/*Get toplist*/
// $limit = how long list
// $extended = Get some more info
function getToplist($limit=false, $extended=false) {
    static $toplist;
    $challenges = challenges();
    if (!$toplist) {
        $top = array();
        $lists = array();
        $info = array();
        foreach ($challenges as $challenge_id => $data) {
            if ($data['type'] == 'private' && isChallengeOpen($challenge_id)) {
                $lists[] = getChallToplist($challenge_id, false);
            }
        }
        foreach ($lists as $list) {
            if (!$list) {
                continue;
            }
            foreach ($list as $data) {
                if (array_key_exists($data['userid'], $top)) {
                    $top[$data['userid']] += $data['points'];
                } else {
                    $top[$data['userid']] = $data['points'];
                    $info[$data['userid']] = array('username' => $data['username'], 'country' => $data['country']);
                }
            }
        }
        arsort($top);
        foreach ($top as $userid => $points) {
            unset($top[$userid]);
            $tmp['username'] = $info[$userid]['username'];
            $tmp['country'] = $info[$userid]['country'];
            $tmp['userid'] = $userid;
            $tmp['points'] = $points;
            if ($extended) {
                $tmp['total'] = count(getUserChalls($userid));
            }
            $newTop[] = $tmp;
        }
        $top = $newTop;
        $toplist = $top;
    } else {
        $top = $toplist;
    }
    if ($limit) {
        $top = array_slice($top,0,$limit,true);
    }
    return $top;
}

function getTop20() {
    $PDO =&DB::$PDO;
    static $toplist;
    $top = array();
    if (!$toplist) {
        $res = $PDO->query("SELECT
  ROUND(SUM(b.len / a.len * 1000))    score,
  u.username                          name,
  u.id                                user_id,
  u.country                           country
FROM
  challenges c
INNER JOIN
  (
    SELECT
      MIN(size)    len,
      challenge_id,
      user_id
    FROM
      attempts
    WHERE
      passed
    GROUP BY
      2,3
  ) a
ON
  (a.challenge_id=c.id)
INNER JOIN
  (
    SELECT
      MIN(size)    len,
      challenge_id
    FROM
      attempts
    WHERE
      passed
    GROUP BY
      2
  ) b
ON
  (b.challenge_id=c.id)
INNER JOIN
  users u
ON
  (u.id=a.user_id)
WHERE
  (c.type='private') AND c.open AND c.active
GROUP BY
  2
ORDER BY
  1 DESC
LIMIT 0,20");
        while(list($points,$username,$userid,$country)=$res->fetch(PDO::FETCH_NUM)) {
            $tmp['username'] = $username;
            $tmp['country'] = $country;
            $tmp['userid'] = $userid;
            $tmp['points'] = $points;
            $top[] = $tmp;
        }
        $toplist = $top;
    } else {
        $top = $toplist;
    }
    return $top;
}

function getTop250() {
    $PDO =&DB::$PDO;
    static $toplist;
    if(!$toplist) {
        $res = $PDO->query("SELECT
  u.username                       name,
  u.id                             user_id,
  u.country                        country,
  ROUND(
    SUM(
      b.len / a.len
      * 1000
      * (c.type='private')
      * (c.active='1')
      * (c.open='1')
    )
  )                                score,
  COUNT(DISTINCT(a.challenge_id))  finishedchallenges
FROM
  challenges c
INNER JOIN
  (
    SELECT
      MIN(size)    len,
      challenge_id,
      user_id
    FROM
      attempts
    WHERE
      passed
    GROUP BY
      2,3
  ) a
ON
  (a.challenge_id=c.id)
INNER JOIN
  (
    SELECT
      MIN(size)    len,
      challenge_id
    FROM
      attempts
    WHERE
      passed
    GROUP BY
      2
  ) b
ON
  (b.challenge_id=c.id)
INNER JOIN
  users u
ON
  (u.id=a.user_id)
WHERE
  (c.type='private') AND c.open AND c.active
GROUP BY
  1
ORDER BY
  4 DESC
LIMIT 0,250");
        while(list($username,$userid,$country,$points,$total)=$res->fetch()) {
            $tmp['username'] = $username;
            $tmp['country'] = $country;
            $tmp['userid'] = $userid;
            $tmp['points'] = $points;
            $tmp['total'] = $total;
            $toplist[] = $tmp;
        }
    }
    
    return $toplist;
}

/*Get challenge toplist */
// $challenge_id = an challenge_id
// $limit = how long list
function getChallToplist($challenge_id,$limit) {
    $PDO = &DB::$PDO;
    static $toplist = array();
    $challenges = challenges(true);
    if (!$challenges[$challenge_id]) {
        return false;
    }

    $rec = DB::$PDO->prepare('SELECT
      u.username,
      u.country,
      a.size,
      ROUND((
        SELECT
          MIN(size)
        FROM
          attempts
        WHERE
          (challenge_id=a.challenge_id)
        AND
          passed
      ) / a.size * 1000) points
    FROM
      attempts a
    INNER JOIN
      users u
    ON
      (u.id=a.user_id)
    WHERE
        passed
      AND
        NOT EXISTS(
          SELECT
            1
          FROM
            attempts
          WHERE
            (challenge_id=a.challenge_id)
          AND
            passed
          AND
            (user_id=a.user_id)
          AND
            ((size<a.size) OR ((size=a.size) AND ((time<a.time) OR ((time=a.time) AND (id<a.id)))))
        )
      AND
        (challenge_id=:cid)
    ORDER BY
      3, a.time
    LIMIT 0, '.($limit? $limit : 99999));
    $rec->execute(Array(':cid' => $challenge_id));
    while(is_array($aRow = $rec->fetch(PDO::FETCH_ASSOC))){
        $list[] = $aRow;
    }
    return $list;
}

//Get challenge toplist rank
// $userid = Valid Userid
// $cid = Valid challenge id
function getUserChallRank($userid,$cid) {
    static $rank = array();
    if (!isset($rank[$userid][$cid])) {
        if (!$toplist = getChallTopList($cid,false)) {
            return false;
        }
        foreach (getUserChalls($userid) as $challenge) {
            if($challenge['challenge_id'] == $cid) {
                $done = true;
                break;
                
            }
        }
        if (!$done) {
            $rank[$userid][$cid] = false;
            return false;
        }
        foreach ($toplist as $pos => $line) {
            $pos++;
            if($line['userid'] == $userid) {
                $rank[$userid][$cid] = array('rank' => $pos,'points' => $line['points']);
                break;
            }
        }
    }
    return $rank[$userid][$cid];
}

//Get total user rank
// $userid = Valid userid
function getUserRank($userid) {
    static $rank = array();
    if (!isset($rank[$userid])) {
        $rec = DB::$PDO->prepare('SELECT
          ROUND(1000 * a.total) points,
          COUNT(1) rank
        FROM
          (
            SELECT
              SUM(points) total
            FROM
              (
                SELECT
                  (
                    (
                      SELECT
                        MIN(size)
                      FROM
                        attempts
                      WHERE
                          passed
                        AND
                          (challenge_id=c.id)
                    )
                  /
                    MIN(a.size)
                  ) points
                FROM
                  challenges c
                INNER JOIN
                  attempts a
                ON
                  (a.passed AND (a.user_id=:cid) AND (a.challenge_id=c.id))
                WHERE
                  c.open AND c.active AND (c.type<>\'public\')
                GROUP BY
                  c.id
              ) a
          ) a
        INNER JOIN
          (
            SELECT
              SUM(points) total
            FROM
              (
                SELECT
                  a.user_id,
                  (b.minsize / MIN(a.size)) points
                FROM
                  challenges c
                INNER JOIN
                  attempts a
                ON
                  (a.passed AND (a.challenge_id=c.id))
                INNER JOIN
                  (
                    SELECT
                      MIN(size) minsize,
                      challenge_id
                    FROM
                      attempts
                    WHERE
                      passed
                    GROUP BY
                      2
                  ) b
                ON
                  (b.challenge_id=c.id)
                WHERE
                  c.open AND c.active AND (c.type<>\'public\')
                GROUP BY
                  1, c.id
              ) a
            GROUP BY
              a.user_id
          ) b
        ON
          (b.total>=a.total)
        GROUP BY
          1');
        if($rec->execute(Array(':cid' => $userid))) $rank[$userid] = $rec->fetch(PDO::FETCH_ASSOC);
    }
    return $rank[$userid];
}


/*getRecent*/
// $limit = how long list
function getRecent($limit) {
    $PDO = &DB::$PDO;
    static $recent;
    if (!$recent) {
        $mem = new cache;
        $mem->key = 'Recent';
        if (!$data = $mem->get()) {
            $rec = $PDO->query('SELECT u.username, u.country, a.time, a.size, c.name, c.id, a.passed, a.executed, c.active 
                FROM attempts a, users u, challenges c 
                WHERE a.user_id = u.id AND c.id=a.challenge_id AND active=\'1\'
                ORDER BY a.id DESC LIMIT 100');
                
            while (list($username,$country,$time,$size,$challenge_name,$challenge_id,$passed,$executed,$active)=$rec->fetch()) {
                if ($executed == 0) {
                    $passed = 'pending';
                }
                else {
                    $passed = $passed ? 'valid' : 'invalid';
                }
                $list[] = array('username' => $username,
                            'country' => $country,
                            'challenge_name' => $challenge_name,
                            'challenge_id' => $challenge_id,
                            'result' => $passed,
                            'time' => $time,
                            'size' => $size,
                            'challenge_active' => $active);
                
            }
            $mem->set(0,$list,MEMCACHE_COMPRESSED,1800);
        } else {
            $list = $data;
            $recent = $list;
        }
    } else {
        $list = $recent;
    }
    $list = array_slice($list,0,$limit,true);
    return $list;
}

/*getChallRecent*/
// $challenge_id = an challenge_id
// $limit = how long list
function getChallRecent($challenge_id,$limit) {
    $PDO = &DB::$PDO;
    static $recent;
    $challenges = challenges(true);
    if (!$challenges[$challenge_id]) {
        return false;
    }
    if (!$recent[$challenge_id]) {
        $mem = new cache;
        $mem->key = "Recent_$challenge_id";
        if (!$data = $mem->get()) {
            $rec = $PDO->query("SELECT u.username, u.country, a.time, a.size, c.name, a.passed, a.executed 
                    FROM attempts a, users u, challenges c 
                    WHERE a.challenge_id = '$challenge_id' AND a.user_id = u.id AND c.id = a.challenge_id 
                    ORDER BY a.id DESC LIMIT 100");
            while (list($username,$country,$time,$size,$challenge_name,$passed,$executed)=$rec->fetch()) {
                if ($executed == 0) {
                    $passed = 'pending';
                } 
                else {
                    $passed = $passed ? 'valid' : 'invalid';
                }
                $list[] = array('username' => $username,
                                'country' => $country,
                                'challenge_name' => $challenge_name,
                                'challenge_id' => $challenge_id,
                                'result' => $passed,
                                'size' => $size,
                                'time' => $time);
                $mem->set(0,$list,MEMCACHE_COMPRESSED,1800);
            }
        } else {
            $list = $data;
            $recent[$challenge_id] = $list;
        }
    } else {
        $list = $recent[$challenge_id];
    }
    if(count($list) > 0) {
        $list = array_slice($list,0,$limit,true);
    } else {
        $list = array();
    }
    return $list;
}

/*getTopSubmissions*/
function getTopSubmissions() {
    $PDO = &DB::$PDO;
    static $top;
    if (!$top) {
        $top = Array();
        $rec = $PDO->prepare('SELECT
          u.username,
          u.country,
          a.size,
          c.name    challenge_name,
          c.id      cid,
          c.type
        FROM
          challenges c
        INNER JOIN
          (
            SELECT
              MIN(size)            len,
              challenge_id         cid
            FROM
              attempts
            WHERE
              passed
            GROUP BY
              2
          ) b
        ON
          (b.cid=c.id)
        INNER JOIN
          attempts a
        ON
          ((a.challenge_id=c.id) AND a.passed AND (a.size=b.len))
        INNER JOIN
          users u
        ON
          (u.id=a.user_id)
        WHERE
            (
                c.open
            AND
              (
                c.enddate IS NULL
              OR
                (c.enddate > NOW())
              )
            )
          AND
            a.passed
          AND
            NOT EXISTS(
              SELECT
                1
              FROM
                attempts
              WHERE
                  passed
                AND
                  (challenge_id=c.id)
                AND
                  (size=a.size)
                AND
                  ((time<a.time) OR ((time=a.time) AND (id<a.id)))
            )
          AND
            c.active
        ORDER BY
          c.id');
        if(
            $rec->execute(
                Array(
                    ':RightViewAllChallenges' => access('show_unactive_challenges')? 1 : 0
                )
            )
        ){
            while(is_array($aRow = $rec->fetch(PDO::FETCH_ASSOC))){
                $top[$aRow['cid']] = $aRow;
            }
        }
    }
    return $top;
}

// Returns the amount of open and active challenges
function getCountOpenActiveChalls() {
    $array = challenges();
    foreach ($array as $key => $val) {
        if ($val['open'] != 1 || $val['active'] != 1 || $val['type'] != 'private') {
            unset($array[$key]);
        }
    }
    return count($array);
}

/*getChallengeName*/
// $challenge_id = an challenge_id
function getChallengeName($challenge_id) {
    $challenges = challenges();
    return $challenges[$challenge_id]['name'];
}

/*getChallengeId*/
// $challenge_name = an challenge name
function getChallengeId($challenge_name) {
    $challenges = challenges();
    foreach ($challenges as $challenge_id => $data) {
        if (preg_match('/'.$data['name'].'/i',$challenge_name)) {
            return $challenge_id;
        }
    }
    return false;
}

/*getChallengeIdFromSafename*/
// $safename = an challenge safename
function getChallengeIdFromSafename($safename) {
    $challenges = challenges();
    foreach ($challenges as $challenge_id => $data) {
        if (preg_match('/^'.$data['safename'].'$/i',$safename)) {
            return $challenge_id;
        }
    }
    return false;
}
/*getSafenameFromId*/
// $challenge_id = an challenge_id
function getSafenameFromId($challenge_id) {
    $challenges = challenges();
    return $challenges[$challenge_id]['safename'];
}

//Chekcs if challenge exists from name
// $challenge_name = name of the challenge too test
function isChallengeName($challenge_name) {
    foreach (challenges(true) as $key => $challenge) {
        if ($challenge_name == $challenge['name']) {
             return true;
        }
    }
    return false;
}

//Chekcs if challenge exists
// $challenge_id = id of the challenge too test
function isChallenge($challenge_id) {
    $challenges = challenges(true);
    return isset($challenges[$challenge_id]);
}

//Prosess uploaded file
function attempt($file,$challenge_id,$user_id) {
    $PDO = &DB::$PDO;
    $challenges = challenges();
    //Check fileending
    if (array_pop(explode('.', $file['name'])) != 'php') {
        msg('Not an .php file',0);
        unlink($file['tmp_name']);
        return false;
    }
    //Check size
    if ($file['size'] > 1048576) {
        msg('File is too big',0);
        unlink($file['tmp_name']);
        return false;
    }
    if ($file['size'] < 4) {
        msg('File is too small',0);
        unlink($file['tmp_name']);
        return false;
    }
    if (!isChallenge($challenge_id)) {
        msg('Did not find challenge');
        unlink($file['tmp_name']);
        return false;
    }
    $code = file_get_contents($file['tmp_name']);
    //Get true size
    $size = strlen(preg_replace('/\r?\n$/', '', $code));
    if ($challenges[$challenge_id]['type'] == 'public') {
        $topChall = getChallToplist($challenge_id,1);
        #print "top:".$topChall[0]['size'] . "size" . $size;
        if ($topChall[0]['size'] != 0 && $topChall[0]['size'] <= $size) {
            msg('Since this challenge is public, you can only submit code that is smaller then the leader\'s',0);
            return false;
        }
    }
    $pre = $PDO->prepare('INSERT INTO attempts (
                            user_id,
                            challenge_id,
                            time,
                            code,
                            size,
                            version
                          ) VALUES (
                            :user_id,
                            :challenge_id,
                            NOW(),
                            :code,
                            :size,
                            :version
                          )');
    $vers = $PDO->prepare('SELECT version FROM attempts WHERE challenge_id=:challenge_id AND user_id=:user_id ORDER BY version DESC LIMIT 1');
    $vers->execute(array(':challenge_id' => $challenge_id, ':user_id' => $user_id));
    $version = $vers->fetch();
    $version = $version[0] + 1;
    
    $pre->execute(array(
                    ':user_id' => $user_id,
                    ':challenge_id' => $challenge_id,
                    ':code' => $code,
                    ':size' => $size,
                    ':version' => $version));
    
    
    msg('The program you uploaded is ' . $size . ' bytes. It will soon be validated. 
            <br>Check on <a href="/PHPGolfV2.0/view/user/' . getUsernameFromId($user_id) . '/latest">latest submissions</a> to see if your code is valid and the output of your code.');
    unlink($file['tmp_name']);
    $mem = new cache;
    $mem->flush();
    $mem->close();
    return true;
}
/*
function openChallenge($challenge_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE challenges SET open = \'1\' WHERE id = :id');
    $stmt->execute(array(':id' => $challenge_id));
    challenges(false,true);
}

function closeChallenge($challenge_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE challenges SET open = \'0\' WHERE id = :id');
    $stmt->execute(array(':id' => $challenge_id));
    challenges(false,true);
}

function activateChallenge($challenge_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE challenges SET active = \'1\' WHERE id = :id');
    $stmt->execute(array(':id' => $challenge_id));
    challenges(false,true);
}

function deactivateChallenge($challenge_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE challenges SET active = \'0\' WHERE id = :id');
    $stmt->execute(array(':id' => $challenge_id));
    challenges(false,true);
}

function addChallenge($challenge_name, $enddate=false) {
    if (isChallengeName($challenge_name)) {
        msg('Challenge already exist', 0);
        return false;
    }
    $PDO =&DB::$PDO;
    if ($enddate) {
        $stmt = $PDO->prepare('INSERT INTO challenges (name, active, enddate) VALUES (:name, \'0\', :enddate)');
        $stmt->execute(array(':name' => trim($challenge_name), ':enddate' => $_POST['challenge_enddate']));
    }
    else {
        $stmt = $PDO->prepare('INSERT INTO challenges (name, active) VALUES (:name, \'0\')');
        $stmt->execute(array(':name' => trim($challenge_name)));
    }
    challenges(false,true);
}

function delChallenge($challenge_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('DELETE FROM challenges WHERE id = :id');
    $stmt->execute(array(':id' => $challenge_id));
    challenges(false,true);
}*/

//Get challenge stats
// $challenge_id = ID of valid challenge
function getChallengeStats($challenge_id) {
    static $stats = array();
    $PDO =&DB::$PDO;
    if (count($stats[$challenge_id]) == 0) {
        $mem = new cache;
        $mem->key = 'ChallengeStats_' . $challenge_id;
        if (!$data = $mem->get()) {
            $stmt = $PDO->prepare('SELECT count(id) AS total, 
                    (SELECT count(id) as passed FROM attempts WHERE passed=\'1\' AND challenge_id = :challenge_id) AS passed
                    FROM attempts a
                    WHERE a.challenge_id = :challenge_id');
            $stmt->execute(array(':challenge_id' => $challenge_id));
            list($countTotal,$countPassed)=$stmt->fetch();
            $stats = array('total' => (int)$countTotal, 'passed' => (int)$countPassed, 'failed' => ($countTotal - $countPassed));
            $mem->set(0,$stats,0,3600);
        }
        else {
            $stats = $data;
        }
    }
    return $stats;
}

//Get stats from all challenges
function getAllChallengesStats() {
    if(
        is_object(
            $oStmt = DB::$PDO->query(
                'SELECT
                  c.id,
                  c.name,
                  IFNULL(SUM(a.passed),0)      passed,
                  IFNULL(SUM(NOT a.passed),0)  failed,
                  IFNULL(SUM(a.executed),0)    total
                FROM
                  challenges  c
                LEFT JOIN
                  attempts    a
                ON
                  (a.challenge_id=c.id)
                WHERE
                  c.active
                GROUP BY
                  1, 2
                ORDER BY
                  1'
            )
        )
    ){
        $aStats = Array();
        while($aStat = $oStmt->fetch(PDO::FETCH_ASSOC)) $aReturn[$aStat['id']] = $aStat;
        return $aReturn;
    }
}

//Get total of submissions
function getTotalSubmissions() {
    if(
        is_object(
            $oStmt = DB::$PDO->prepare(
                'SELECT
                    COUNT(1)
                FROM
                    challenges c
                INNER JOIN
                    attempts a
                ON
                    (a.challenge_id=c.id)
                WHERE
                    (
                        c.active
                    OR
                        :RightViewAllChallenges
                    )'
            )
        )
    &&
        $oStmt->execute(
            Array(
                //':RightViewAllChallenges' => access('show_unactive_challenges')? 1 : 0
                ':RightViewAllChallenges' => 1
            )
        )
    &&
        is_array($aTotalSubs = $oStmt->fetch(PDO::FETCH_NUM))
    ){
        return $aTotalSubs[0];
    } return 0;
}

//Rate challenge up or down
function rateChallenge($cid,$direction) {
    $PDO =&DB::$PDO;
    //Check user
    if(!$_SESSION['id']) {
        error(403);
    }
    //Check if the user have voted before
    $pre = $PDO->prepare('SELECT id FROM challenges_rating WHERE challenge_id=:cid AND user_id=:uid');
    $pre->execute(array(':cid' => $cid,':uid' => $_SESSION['id']));
    if(count($pre->fetchAll())>0) {
        msg('You have already voted',0);
        return false;
    }
    //Register vote
    $pre = $PDO->prepare('INSERT INTO challenges_rating SET challenge_id=:cid, user_id=:uid, direction=:direction');
    $pre->execute(array(
                    ':cid' => $cid,
                    ':uid' => $_SESSION['id'],
                    ':direction' => (bool)$direction
                    ));
    if($pre->rowCount() == 0) {
        msg('An error happend under prosessing the vote',0);
        return false;
    }
    return true;
    
    
}

?>
