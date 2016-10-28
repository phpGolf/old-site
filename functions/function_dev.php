<?php
if(!defined('INDEX')) {
    header('location: /');
}

/*Get toplist*/
// $limit = how long list
// $extended = Get some more info
function dev_getTop20() {
    $PDO =&DB::$PDO;
    static $toplist;
    $challenges = challenges();
    if (!$toplist) {
        $top = array();
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
  (c.type<>'public') AND c.open AND c.active
GROUP BY
  2
ORDER BY
  1 DESC
LIMIT 0,20");
        while(list($points,$name,$userid,$country)=$res->fetch()) {
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

function dev_getTop250() {
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
      * (c.type<>'public')
      * (c.active<>0)
      * (c.open)
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
