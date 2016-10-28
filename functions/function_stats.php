<?php

//Get the total of users registrated
function getTotalMembers() {
    if(
        is_object(
            $oStmt = DB::$PDO->query(
                'SELECT
                    COUNT(1)
                FROM
                    users'
            )
        )
    &&
        is_array(
            $aTotalMems = $oStmt->fetch(PDO::FETCH_NUM)
        )
    ){
        return $aTotalMems[0];
    } return 0;
}

function getNewestMembers() {
    $PDO =&DB::$PDO;
    $newest_members = $PDO->query('SELECT username, country, regdate FROM users ORDER BY regdate DESC LIMIT 10')->fetchAll();
    return $newest_members;
}

function getUsersOnlinePeak() {
    static $peak;
    if(!isset($peak)) {
        $mem = new cache;
        $mem->key = 'UserPeak';
        if(($data = $mem->get()) === false) {
            $PDO =&DB::$PDO;
            list($peak) = $PDO->query('SELECT users_online FROM online_users_peak ORDER BY users_online DESC LIMIT 1')->fetch();
            $mem->set(0,$peak,0,3600*24);
        } else {
            $peak = $data;
        }
    }
    return $peak;
}

function getCountriesStats() {
	$PDO =&DB::$PDO;
	$sql = "SELECT
	u.country,
	COUNT(u.id)				AmountOfUsers,
	AVG(a.ChallengesPerUser)		ChallengesPerUser,
	AVG(a.CountingChallengesPerUser)	CountingChallengesPerUser,
	AVG(a.SubmissionsPerUser)		SubmissionsPerUser,
	SUM(a.SubmissionsPerUser)		TotalSubmissions,
	AVG(a.CountingSubmissionsPerUser)	CountingSubmissionsPerUser,
	SUM(a.CountingSubmissionsPerUser)	TotalCountingSubmissions,
	AVG(a.PassedSubmisionsPerUser)		PassedSubmisionsPerUser,
	SUM(a.PassedSubmisionsPerUser)		TotalPassedSubmissions,
	AVG(a.PassedCountingSubmissionsPerUser)	PassedCountingSubmissionsPerUser,
	SUM(a.PassedCountingSubmissionsPerUser)	TotalPassedCountingSubmissions,
	AVG(a.FailedSubmissionsPerUser)		FailedSubmissionsPerUser,
	SUM(a.FailedSubmissionsPerUser)		TotalFailedSubmissions,
	AVG(a.FailedCountingSubmissionsPerUser)	FailedCountingSubmissionsPerUser,
	SUM(a.FailedCountingSubmissionsPerUser)	TotalFailedCountingSubmissions
FROM
	users u
LEFT JOIN
	(
		SELECT
			a.user_id,
			COUNT(DISTINCT(c.id))															ChallengesPerUser,
			COUNT(DISTINCT(IF(c.active AND c.open AND (c.type<>'public') AND ((c.enddate IS NULL) OR (c.enddate > NOW())), c.id, NULL)))		CountingChallengesPerUser,
			COUNT(a.id)																SubmissionsPeruser,
			COUNT(IF(c.active AND c.open AND (c.type<>'public') AND ((c.enddate IS NULL) OR (c.enddate > NOW())), a.id, NULL))			CountingSubmissionsPerUser,
			SUM(a.passed<>0)															PassedSubmisionsPerUser,
			SUM(IF(c.active AND c.open AND (c.type<>'public') AND ((c.enddate IS NULL) OR (c.enddate > NOW())), a.passed<>0, NULL))			PassedCountingSubmissionsPerUser,
			SUM(NOT a.passed)															FailedSubmissionsPerUser,
			SUM(IF(c.active AND c.open AND (c.type<>'public') AND ((c.enddate IS NULL) OR (c.enddate > NOW())), NOT a.passed, NULL))		FailedCountingSubmissionsPerUser
		FROM
			attempts a
		INNER JOIN
			challenges c
		ON
			(c.id=a.challenge_id)
		GROUP BY
			1
	) a
ON
	(a.user_id=u.id)
GROUP BY
	1
ORDER BY
	2 DESC";
	$info = $PDO->query($sql)->fetchAll();
	return $info;
}

?>
