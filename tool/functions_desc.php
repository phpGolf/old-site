<?php
if(!defined('INDEX')) {
    header('location: /');
}

if(!access('debug')) {
    error(403);
}
/**************

  -- HOW TO --
This file is filling the array that describe all the functions on the site
This is the setup:

//Name
$Desc['lowercasefunctionname']['Name'] = 'UppercaseFunctionName'; //If the is an different between lowercase and uppercase
//Description
$Desc['lowercasefunctionname']['Desc'] = 'This is an description of the function';
//Arguments
$Desc['lowercasefunctionname']['Args']['$variabel1']['Desc'] = 'Description of argument number 1';
$Desc['lowercasefunctionname']['Args']['$variabel1']['Type'] = 'Array'; //Use only if the type is spesial, like an array or an class.
$Desc['lowercasefunctionname']['Args']['$variabel2']['Desc'] = 'Description of argument number 2';
$Desc['lowercasefunctionname']['Args']['$variabel2']['Default'] = 'false'; //If the is an default value, put it here
//Return
$Desc['lowercasefunctionname']['Return']['type'] = 'Description of returned value'; //Type is array, integer, string or multi



*/

//Start
$Desc['start']['Desc'] = 'Include and fix everything that is needed to show the page. Can only be runned one time.';

//Show page
$Desc['show_page']['Desc'] = 'Builds an page with menu and all the other stuff. Shows sidebar if wanted with show_right()';
$Desc['show_page']['Args']['$title']['Desc'] = 'The title that will be shown in title tag';

//Show footer
$Desc['show_footer']['Desc'] = 'Internal function that shows the footer';

//Show api
$Desc['show_api']['Desc'] = 'Have no use yet';

//Include site
$Desc['include_site']['Desc'] = 'Include an php file. Makes an 404 site if file don\'t exists';
$Desc['include_site']['Args']['$site']['Desc'] = 'Filename to include, without .php';
$Desc['include_site']['Return']['bool'] = 'True on success, false on failure';

//Include func
$Desc['include_func']['Desc'] = 'Include an phpfunction file';
$Desc['include_func']['Args']['$func']['Desc'] = 'Filename to include, without .php';
$Desc['include_func']['Return']['bool'] = 'True on success, false on failure';

//Include class
$Desc['include_class']['Desc'] = 'Include an phpclass file';
$Desc['include_class']['Args']['$class']['Desc'] = 'Filename to include, without .php';
$Desc['include_class']['Return']['bool'] = 'True on success, false on failure';

//Show right
$Desc['show_right']['Desc'] = 'Sets if the sidebar is wanted to be showned';
$Desc['show_right']['Args']['$return']['Desc'] = 'If true, return if the sidebar is wanted to be showned';
$Desc['show_right']['Args']['$return']['Default'] = 'false';
$Desc['show_right']['Return']['bool'] = 'True/false if $return is true. If $return is false, NULL is returned';

//Add css
$Desc['addcss']['Name'] = 'addCss';
$Desc['addcss']['Desc'] = 'Set stylesheet that will be linked';
$Desc['addcss']['Args']['$css']['Desc'] = 'Css file in '.DESIGN.' folder. Without .css.';
$Desc['addcss']['Args']['$css']['Default'] = 'false';
$Desc['addcss']['Return']['multi'] = 'True on success, false on failure. If $css is false, return an array with the files that is registrated';

//Add script
$Desc['addscript']['Name'] = 'addScript';
$Desc['addscript']['Desc'] = 'Set javascript files to be included';
$Desc['addscript']['Args']['$script']['Desc'] = 'Javascript file in '.SCRIPT.' folder. Without .js.';
$Desc['addscript']['Args']['$script']['Default'] = 'false';
$Desc['addscript']['Return']['multi'] = 'True on success, false on failure. If $script is false, return an array with the files that is registrated';

//Error
$Desc['error']['Desc'] = 'Shows an error page with either preset message, og custom message ($code needs to be false/0 for the custom message to show)';
$Desc['error']['Args']['$code']['Desc'] = 'If you want an preset message, place the error code here (404, 403, etc)';
$Desc['error']['Args']['$title']['Desc'] = 'Custom title';
$Desc['error']['Args']['$title']['Default'] = 'false';
$Desc['error']['Args']['$msg']['Desc'] = 'Custom message';
$Desc['error']['Args']['$msg']['Default'] = 'false';
$Desc['error']['Return']['NULL'];

//SQL error
$Desc['sqlerror']['Name'] = 'sqlError';
$Desc['sqlerror']['Desc'] = 'Take an errorInfo() array from PDO and makes an nice debug message of it';
$Desc['sqlerror']['Args']['$errorInfo']['Desc'] = 'Array from PDO::errorInfo() or PDOStatement::errorInfo()';
$Desc['sqlerror']['Args']['$errorInfo']['Type'] = 'Array';
$Desc['sqlerror']['Return']['NULL'];

//Msg
$Desc['msg']['Desc'] = 'Prints out an nice return message';
$Desc['msg']['Args']['$msg']['Desc'] = 'The message to be printed';
$Desc['msg']['Args']['$msg']['Default'] = 'false';
$Desc['msg']['Args']['$type']['Desc'] = 'Type of message (0=error, 1=ok, 2=debug)';
$Desc['msg']['Args']['$type']['Default'] = '1';
$Desc['msg']['Return']['multi'] = 'True on success, false on failure. If $msg is false, return an array with the messages that is registrated';

//Online
$Desc['online']['Desc'] = 'Returns number of users that is online';
$Desc['online']['Return']['integer'] = 'Number of users online';

//Randstr
$Desc['randstr']['Name'] = 'randStr';
$Desc['randstr']['Desc'] = 'Makes an random string';
$Desc['randstr']['Args']['$length']['Desc'] = 'Length of string';
$Desc['randstr']['Return']['string'] = 'Return an random string';

//time2str
$Desc['time2str']['Desc'] = 'Convert unix timestamp to text (Ex: 1 hour ago)';
$Desc['time2str']['Args']['$timestamp']['Desc'] = 'Unix timestamp';
$Desc['time2str']['Return']['string'] = 'Return humanreadable text like 1 hour ago';

//Count bytes
$Desc['countbytes']['Name'] = 'countBytes';
$Desc['countbytes']['Desc'] = 'Count the bytes in an array';
$Desc['countbytes']['Args']['$Array']['Desc'] = 'Array to be calculated';
$Desc['countbytes']['Args']['$Array']['Type'] = 'Array';
$Desc['countbytes']['Args']['$continue']['Desc'] = 'Internal argument (the function is going in loop)';
$Desc['countbytes']['Args']['$continue']['Default'] = 'false';
$Desc['countbytes']['Return']['integer'] = 'Size of the array';

//Row class
$Desc['rowclass']['Name'] = 'rowClass';
$Desc['rowclass']['Desc'] = 'Returns an css class name. Used to make different colurs on tables';
$Desc['rowclass']['Args']['$rows']['Desc'] = 'How many different classes (on false, reset counter)';
$Desc['rowclass']['Args']['$rows']['Default'] = '2';
$Desc['rowclass']['Return']['multi'] = 'Class name, or true on reset ($rows is false)';

//Show date
$Desc['showdate']['Name'] = 'showDate';
$Desc['showdate']['Desc'] = 'Returns an formated date according to the DATE_FORMAT constant (currently "'.DATE_FORMAT.'")';
$Desc['showdate']['Args']['$date']['Desc'] = 'Any date in any common format';
$Desc['showdate']['Return']['string'] = 'Returns formated date';

//Show time
$Desc['showtime']['Name'] = 'showTime';
$Desc['showtime']['Desc'] = 'Returns an formated time according to the TIME_FORMAT constant (currently "'.TIME_FORMAT.'")';
$Desc['showtime']['Args']['$time']['Desc'] = 'Any time in any common format';
$Desc['showtime']['Return']['string'] = 'Returns formated time';

//Show date and time
$Desc['showdatetime']['Name'] = 'showDateTime';
$Desc['showdatetime']['Desc'] = 'Returns an formated date and time according to the DATE_FORMAT and TIME_FORMAT constant (currently "'.DATE_FORMAT.', '.TIME_FORMAT.'")';
$Desc['showdatetime']['Args']['$date']['Desc'] = 'Any date and time in any common format';
$Desc['showdatetime']['Return']['string'] = 'Returns formated date and time';

//Login
$Desc['login']['Desc'] = 'Complete the users login with sessions and cookies. It also set an updated IP adress and time in the user table';
$Desc['login']['Args']['$id']['Desc'] = 'User id';
$Desc['login']['Args']['$username']['Desc'] = 'Username';
$Desc['login']['Args']['$hash']['Desc'] = 'User unique hash string';
$Desc['login']['Args']['$level']['Desc'] = 'Userlevel';
$Desc['login']['Args']['$permissions']['Desc'] = 'The user permissions';
$Desc['login']['Args']['$permissions']['Default'] = 'false';
$Desc['login']['Args']['$remember']['Desc'] = 'If true, the remember cookie is set with the userhash';
$Desc['login']['Args']['$remember']['Default'] = 'false';
$Desc['login']['Return']['bool'] = 'Always return true, for now';

//Login valid
$Desc['login_valid']['Desc'] = 'Checks username and password, if it is correct the user is logged in';
$Desc['login_valid']['Args']['$username']['Desc'] = 'Username to the user that is trying to log in';
$Desc['login_valid']['Args']['$password']['Desc'] = 'Plaintext password to the user that is trying to log in';
$Desc['login_valid']['Args']['$remember']['Desc'] = 'True to set remember cookie';
$Desc['login_valid']['Args']['$remember']['Default'] = 'false';
$Desc['login_valid']['Return']['bool'] = 'True if user is logged in, false if user is not';

//Auto login
$Desc['autologin']['Desc'] = 'Log the user in if the user have an valid remember cookie';
$Desc['autologin']['Return']['bool'] = 'True if the user is logged in (direct return from login()), false if the user is not found or invalid cookie';

//Register
$Desc['register']['Desc'] = 'Registrate an user';
$Desc['register']['Args']['$username']['Desc'] = 'Username that is wanted';
$Desc['register']['Args']['$password']['Desc'] = 'Plaintext password that is wanted, if empty and password is generated and sent on email';
$Desc['register']['Args']['$email']['Desc'] = 'Valid and unique emailaddress';
$Desc['register']['Args']['$sendpass']['Desc'] = 'True to send the password to the user. Currently it always send the password';
$Desc['register']['Args']['$sendpass']['Default'] = 'false';
$Desc['register']['Return']['bool'] = 'True if user have been registrated, false if not';

//Logout
$Desc['logout']['Desc'] = 'The user is logged off, and cookies is deleted';
$Desc['logout']['Return']['bool'] = 'True if the user have been logged off, false if the user already is logged off';

//Challenges
$Desc['challenges']['Desc'] = 'An array of challenges is returned';
$Desc['challenges']['Args']['$all']['Desc'] = 'True to return all challenges. If false only the challenges the user is allowed to see is returned';
$Desc['challenges']['Args']['$all']['Default'] = 'false';
$Desc['challenges']['Args']['$reset']['Desc'] = 'To reset memcache and get the list of challenges from the DB';
$Desc['challenges']['Args']['$reset']['Default'] = 'false';
$Desc['challenges']['Return']['array'] = 'Returns an array of challenges';

//Get toplist
$Desc['gettoplist']['Name'] = 'getToplist';
$Desc['gettoplist']['Desc'] = 'Return the total toplist for all challenges (not public challenges)';
$Desc['gettoplist']['Args']['$limit']['Desc'] = 'Number of entries to be returned';
$Desc['gettoplist']['Args']['$limit']['Default'] = 'false';
$Desc['gettoplist']['Args']['$extended']['Desc'] = 'True to also return number of challenges the users have completed';
$Desc['gettoplist']['Args']['$extended']['Default'] = 'false';
$Desc['gettoplist']['Return']['array'] = 'Return toplist';

//Get challenge toplist
$Desc['getchalltoplist']['Name'] = 'getChallToplist';
$Desc['getchalltoplist']['Desc'] = 'Returns toplists for seperated challenges spesified in $challenge_id';
$Desc['getchalltoplist']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['getchalltoplist']['Args']['$limit']['Desc'] = 'Number of entries to be returned, if false, all is returned';
$Desc['getchalltoplist']['Return']['array'] = 'Return toplist for the challenge';

//Get user challenge rank
$Desc['getuserchallrank']['Name'] = 'getUserChallRank';
$Desc['getuserchallrank']['Desc'] = 'Returns the rank the user is on the challenge toplist';
$Desc['getuserchallrank']['Args']['$userid']['Desc'] = 'Valid user id ';
$Desc['getuserchallrank']['Args']['$cid']['Desc'] = 'Valid challenge id';
$Desc['getuserchallrank']['Return']['integer'] = 'Return the rank the user have on the challenge';

//Get user rank
$Desc['getuserrank']['Name'] = 'getUserRank';
$Desc['getuserrank']['Desc'] = 'Returns the rank the user is on the toplist';
$Desc['getuserrank']['Args']['$userid']['Desc'] = 'Valid user id ';
$Desc['getuserrank']['Return']['integer'] = 'Return the rank the user is on the total toplist';

//Get recent
$Desc['getrecent']['Name'] = 'getRecent';
$Desc['getrecent']['Desc'] = 'Returns the latest attempts uploaded, failed and passed';
$Desc['getrecent']['Args']['$limit']['Desc'] = 'Number of entries to be returned, if false, all is returned (maximum 100 attempts)';
$Desc['getrecent']['Return']['array'] = 'Return the list';

//Get challenge recent
$Desc['getchallrecent']['Name'] = 'getChallRecent';
$Desc['getchallrecent']['Desc'] = 'Returns the latest attempts uploaded for that challenge, failed and passed';
$Desc['getchallrecent']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['getchallrecent']['Args']['$limit']['Desc'] = 'Number of entries to be returned, if false, all is returned (maximum 100 attempts)';
$Desc['getchallrecent']['Return']['array'] = 'Return the list';

//Get top submissions
$Desc['gettopsubmissions']['Name'] = 'getTopSubmissions';
$Desc['gettopsubmissions']['Desc'] = 'Returns the list over the users that is on the top of the toplists for all challenges';
$Desc['gettopsubmissions']['Return']['array'] = 'Return the list';

//Get challenge name
$Desc['getchallengename']['Name'] = 'getChallengeName';
$Desc['getchallengename']['Desc'] = 'Get challenge name from challenge id';
$Desc['getchallengename']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['getchallengename']['Return']['string'] = 'Return challenge name';

//Get challenge id
$Desc['getchallengeid']['Name'] = 'getChallengeId';
$Desc['getchallengeid']['Desc'] = 'Get challenge id from challenge name';
$Desc['getchallengeid']['Args']['$challenge_name']['Desc'] = 'Valid challenge name';
$Desc['getchallengeid']['Return']['integer'] = 'Return challenge id';

//Get challenge id from safename
$Desc['getchallengeidfromsafename']['Name'] = 'getChallengeIdFromSafename';
$Desc['getchallengeidfromsafename']['Desc'] = 'Get challenge id from challenge safename';
$Desc['getchallengeidfromsafename']['Args']['$safename']['Desc'] = 'Valid challenge safename';
$Desc['getchallengeidfromsafename']['Return']['integer'] = 'Return challenge id';

//Get safename from challenge id
$Desc['getsafenamefromid']['Name'] = 'getSafenameFromId';
$Desc['getsafenamefromid']['Desc'] = 'Get challenge safename from challenge id';
$Desc['getsafenamefromid']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['getsafenamefromid']['Return']['string'] = 'Return challenge safename';

//Is challenge name
$Desc['ischallengename']['Name'] = 'isChallengeName';
$Desc['ischallengename']['Desc'] = 'Check if the challenge name exists';
$Desc['ischallengename']['Args']['$challenge_name']['Desc'] = 'Challenge name to check';
$Desc['ischallengename']['Return']['bool'] = 'True if challenge name exists, false if not';

//Is challenge
$Desc['ischallenge']['Name'] = 'isChallenge';
$Desc['ischallenge']['Desc'] = 'Check if the challenge exists';
$Desc['ischallenge']['Args']['$challenge_id']['Desc'] = 'Challenge id to check';
$Desc['ischallenge']['Return']['bool'] = 'True if challenge exists, false if not';

//Attempt
$Desc['attempt']['Desc'] = 'Upload an attempt';
$Desc['attempt']['Args']['$file']['Desc'] = '$_FILES array of the file that is uplaoded';
$Desc['attempt']['Args']['$file']['Type'] = 'Array';
$Desc['attempt']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['attempt']['Args']['$user_id']['Desc'] = 'Valid user id';
$Desc['attempt']['Return']['bool'] = 'True on success, false on failure';

//Get challenge stats
$Desc['getchallengestats']['Name'] = 'getChallengeStats';
$Desc['getchallengestats']['Desc'] = 'Get stats for an challenge. Number og attempts, number of passed/failed etc';
$Desc['getchallengestats']['Args']['$challenge_id']['Desc'] = 'Valid challenge id';
$Desc['getchallengestats']['Return']['array'] = 'Array of stats';

//Get all challenge stats
$Desc['getallchallengesstats']['Name'] = 'getAllChallengesStats';
$Desc['getallchallengesstats']['Desc'] = 'Get stats for all challenges. Number og attempts, number of passed/failed etc';
$Desc['getallchallengesstats']['Return']['array'] = 'Array of stats';

//Get total of submissions
$Desc['gettotalsubmissions']['Name'] = 'getTotalSubmissions';
$Desc['gettotalsubmissions']['Desc'] = 'Get the total number of submissions on all challenges';
$Desc['gettotalsubmissions']['Return']['integer'] = 'Total submissions';

//Users
$Desc['users']['Desc'] = 'Get all active users';
$Desc['users']['Args']['$reset']['Desc'] = 'Reset memcache on true';
$Desc['users']['Args']['$reset']['Default'] = 'false';
$Desc['users']['Return']['array'] = 'Array of all users';

//Groups
$Desc['groups']['Desc'] = 'Get all user groups';
$Desc['groups']['Args']['$reset']['Desc'] = 'Reset memcache on true';
$Desc['groups']['Args']['$reset']['Default'] = 'false';
$Desc['groups']['Return']['array'] = 'Array of all user groups';

//Permissions
$Desc['permissions']['Desc'] = 'Get all permissions';
$Desc['permissions']['Args']['$reset']['Desc'] = 'Reset memcache on true';
$Desc['permissions']['Args']['$reset']['Default'] = 'false';
$Desc['permissions']['Return']['array'] = 'Array of all permissions';

//Is username
$Desc['isusername']['Name'] = 'isUsername';
$Desc['isusername']['Desc'] = 'Check if username exists';
$Desc['isusername']['Args']['$username']['Desc'] = 'Username to check';
$Desc['isusername']['Return']['bool'] = 'True if username exists, false if not';

//Get username from id
$Desc['getusernamefromid']['Name'] = 'getUsernameFromId';
$Desc['getusernamefromid']['Desc'] = 'Get username from user id';
$Desc['getusernamefromid']['Args']['$id']['Desc'] = 'Valid userid';
$Desc['getusernamefromid']['Return']['string'] = 'Username';

//Get id from username
$Desc['getidfromusername']['Name'] = 'getIdFromUsername';
$Desc['getidfromusername']['Desc'] = 'Get user id from username';
$Desc['getidfromusername']['Args']['$username']['Desc'] = 'Valid username';
$Desc['getidfromusername']['Return']['integer'] = 'User id';

//Get easy permissions
$Desc['geteasypermissions']['Name'] = 'getEasyPermissions';
$Desc['geteasypermissions']['Desc'] = 'Get easy list of all permissions';
$Desc['geteasypermissions']['Args']['$reset']['Desc'] = 'Reset memcache on true';
$Desc['geteasypermissions']['Args']['$reset']['Default'] = 'false';
$Desc['geteasypermissions']['Return']['array'] = 'Easy array of all permissions';

//Access
$Desc['access']['Desc'] = 'Check if the user have access to key';
$Desc['access']['Args']['$key']['Desc'] = 'Permission key';
$Desc['access']['Return']['bool'] = 'True if user have permission, false if not';

//User level
$Desc['userlevel']['Name'] = 'userLevel';
$Desc['userlevel']['Desc'] = 'Check if the userlevel is lower then the userlevel to the current user';
$Desc['userlevel']['Args']['$level']['Desc'] = 'Userlevel to check against';
$Desc['userlevel']['Return']['bool'] = 'True if users level is above $level, false if not';

//Get user level
$Desc['getuserlevel']['Name'] = 'getUserLevel';
$Desc['getuserlevel']['Desc'] = 'Get userlevel for an user group';
$Desc['getuserlevel']['Args']['$group']['Desc'] = 'Valid group id';
$Desc['getuserlevel']['Return']['integer'] = 'Userlevel to the usergroup';

//Is group
$Desc['isgroup']['Name'] = 'isGroup';
$Desc['isgroup']['Desc'] = 'Check if group exists';
$Desc['isgroup']['Args']['$id']['Desc'] = 'Group id to check';
$Desc['isgroup']['Return']['bool'] = 'True if group exists, false if not';

//Get group name
$Desc['getgroupname']['Name'] = 'getGroupName';
$Desc['getgroupname']['Desc'] = 'Get group name from group id';
$Desc['getgroupname']['Args']['$id']['Desc'] = 'Valid group id';
$Desc['getgroupname']['Return']['string'] = 'Group name';

//Get group permissions
$Desc['getgrouppermissions']['Name'] = 'getGroupPermissions';
$Desc['getgrouppermissions']['Desc'] = 'Get permissions for the group';
$Desc['getgrouppermissions']['Args']['$id']['Desc'] = 'Valid group id';
$Desc['getgrouppermissions']['Return']['array'] = 'Group permissions';

//Add permission
$Desc['addpermission']['Name'] = 'addPermission';
$Desc['addpermission']['Desc'] = 'Add permission key';
$Desc['addpermission']['Args']['$name']['Desc'] = 'Permission name';
$Desc['addpermission']['Args']['$key']['Desc'] = 'Permission key';
$Desc['addpermission']['Return']['bool'] = 'True on success, false on failure';

//Edit permission
$Desc['editpermission']['Name'] = 'editPermission';
$Desc['editpermission']['Desc'] = 'Edit permission key';
$Desc['editpermission']['Args']['$id']['Desc'] = 'Valid permission id';
$Desc['editpermission']['Args']['$name']['Desc'] = 'Permission name';
$Desc['editpermission']['Args']['$key']['Desc'] = 'Permission key';
$Desc['editpermission']['Return']['bool'] = 'True on success, false on failure';

//Delete permission
$Desc['deletepermission']['Name'] = 'deletePermission';
$Desc['deletepermission']['Desc'] = 'Delete permission key';
$Desc['deletepermission']['Args']['$id']['Desc'] = 'Valid permission id';
$Desc['deletepermission']['Return']['bool'] = 'True on success, false on failure';

//Add group
$Desc['addgroup']['Name'] = 'addGroup';
$Desc['addgroup']['Desc'] = 'Add user group';
$Desc['addgroup']['Args']['$name']['Desc'] = 'Unique group name';
$Desc['addgroup']['Args']['$permissions']['Desc'] = 'Semicolon separated list of permissions id';
$Desc['addgroup']['Args']['$order']['Desc'] = 'Userlevel';
$Desc['addgroup']['Return']['bool'] = 'True on success, false on failure';

//Edit group
$Desc['editgroup']['Name'] = 'editGroup';
$Desc['editgroup']['Desc'] = 'Edit user group';
$Desc['editgroup']['Args']['$id']['Desc'] = 'Valid group id';
$Desc['editgroup']['Args']['$name']['Desc'] = 'Group name';
$Desc['editgroup']['Args']['$permissions']['Desc'] = 'Semicolon separated list of permissions id';
$Desc['editgroup']['Args']['$order']['Desc'] = 'Userlevel';
$Desc['editgroup']['Return']['bool'] = 'True on success, false on failure';

//Delete group
$Desc['deletegroup']['Name'] = 'deleteGroup';
$Desc['deletegroup']['Desc'] = 'Delete user group';
$Desc['deletegroup']['Args']['$id']['Desc'] = 'Valid group id';
$Desc['deletegroup']['Return']['bool'] = 'True on success, false on failure';

//Get permission id from key
$Desc['getpermissionidfromkey']['Name'] = 'getPermissionIdFromKey';
$Desc['getpermissionidfromkey']['Desc'] = 'Get permission id from permission key';
$Desc['getpermissionidfromkey']['Args']['$key']['Desc'] = 'Valid permission key';
$Desc['getpermissionidfromkey']['Return']['integer'] = 'Permission id';

//Convert permissions
$Desc['convertpermissions']['Name'] = 'convertPermissions';
$Desc['convertpermissions']['Desc'] = 'Converts a string with semicolon separated permissions id list to an permissions array. And the other way around';
$Desc['convertpermissions']['Args']['$permissions']['Desc'] = 'String with permissions id or array with permisisons';
$Desc['convertpermissions']['Return']['multi'] = 'If $permissions was an array the function return an string with semicolon separated permissions id, if string the function returns an array with permissions';

//Get total user submissions
$Desc['gettotalusersubmissions']['Name'] = 'getTotalUserSubmissions';
$Desc['gettotalusersubmissions']['Desc'] = 'Get the total number of submissions the user have made';
$Desc['gettotalusersubmissions']['Args']['$userid']['Desc'] = 'Valid userid';
$Desc['gettotalusersubmissions']['Return']['integer'] = 'Total number og submissions';

//Get user challs
$Desc['getuserchalls']['Name'] = 'getUserChalls';
$Desc['getuserchalls']['Desc'] = 'Get challenges the user have passed on';
$Desc['getuserchalls']['Args']['$userid']['Desc'] = 'Valid userid';
$Desc['getuserchalls']['Return']['array'] = 'Array with challenges';

//Bbcode
$Desc['bbcode']['Desc'] = 'Converts BBCode into HTML';
$Desc['bbcode']['Args']['$string']['Desc'] = 'String with BBCode';
$Desc['bbcode']['Return']['string'] = 'String with HTML';

//Highlightcode
$Desc['highlightcode']['Name'] = 'highlightCode';
$Desc['highlightcode']['Desc'] = 'Highlight php code that is between [code] bbcode tags';
$Desc['highlightcode']['Args']['$string']['Desc'] = 'String with [code] tag';
$Desc['highlightcode']['Return']['string'] = 'String with highlighted code';

//News
$Desc['news']['Desc'] = 'An array of news post is returned';
$Desc['news']['Args']['$reset']['Desc'] = 'Reset memcache';
$Desc['news']['Args']['$reset']['Default'] = 'false';
$Desc['news']['Return']['array'] = 'Returns an array of news post';

//Get total members
$Desc['gettotalmembers']['Name'] = 'getTotalMembers';
$Desc['gettotalmembers']['Desc'] = 'Get number of active members';
$Desc['gettotalmembers']['Return']['integer'] = 'Number og members';

//Get users online peak
$Desc['getusersonlinepeak']['Name'] = 'getUsersOnlinePeak';
$Desc['getusersonlinepeak']['Desc'] = 'Returns the number on users online at the same time';
$Desc['getusersonlinepeak']['Return']['integer'] = 'Users online peak';

//Delete a forum topic
$Desc['forumdeletetopic']['Name'] = 'forumDeleteTopic';
$Desc['forumdeletetopic']['Args']['$topic_id']['Desc'] = 'The id of the topic to be deleted';
$Desc['forumdeletetopic']['Desc'] = 'Deletes a forum topic';

//Delete a forum reply
$Desc['forumdeletereply']['Name'] = 'forumDeleteReply';
$Desc['forumdeletereply']['Args']['$reply_id']['Desc'] = 'The id of the reply to be deleted';
$Desc['forumdeletereply']['Desc'] = 'Deletes a forum reply';

//Edit a forum topic
$Desc['forumedittopic']['Name'] = 'forumEditTopic';
$Desc['forumedittopic']['Args']['$topic_id']['Desc'] = 'The id of the topic to be deleted';
$Desc['forumedittopic']['Args']['$text']['Desc'] = 'The updated text';
$Desc['forumedittopic']['Desc'] = 'Edit a topic';

//Edit a forum reply
$Desc['forumeditreply']['Name'] = 'forumEditReply';
$Desc['forumeditreply']['Args']['$reply_id']['Desc'] = 'The id of the reply to be deleted';
$Desc['forumeditreply']['Args']['$text']['Desc'] = 'The updated text';
$Desc['forumeditreply']['Desc'] = 'Edit a reply';

//Get forum categories
$Desc['getforumcategories']['Name'] = 'getForumCategories';
$Desc['getforumcategories']['Desc'] = 'Get all forum categories';
$Desc['getforumcategories']['Return']['array'] = 'An array of all forum categories';

//Get forum topics
$Desc['getforumtopics']['Name'] = 'getForumTopics';
$Desc['getforumtopics']['Desc'] = 'Get all topics for a category or a specific topic';
$Desc['getforumtopics']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['getforumtopics']['Args']['$topic_id=0']['Desc'] = 'If 0, then return all topics else return data of a specific topic';
$Desc['getforumtopics']['Return']['array'] = 'An array of topic data';

//Get forum replies
$Desc['getforumreplies']['Name'] = 'getForumReplies';
$Desc['getforumreplies']['Desc'] = 'Get all forum replies';
$Desc['getforumreplies']['Args']['$topic_id']['Desc'] = 'The topic id';
$Desc['getforumreplies']['Return']['array'] = 'An array with replies to the particular topic';

//Get count topic replies
$Desc['getcounttopicreplies']['Name'] = 'getCountTopicReplies';
$Desc['getcounttopicreplies']['Desc'] = 'Get the number of replies on a topic';
$Desc['getcounttopicreplies']['Args']['$topic_id']['Desc'] = 'The topic id';
$Desc['getcounttopicreplies']['Return']['integer'] = 'The number of replies';

//Get count category topics
$Desc['getcountcategorytopics']['Name'] = 'getCountCategoryTopics';
$Desc['getcountcategorytopics']['Desc'] = 'Get the number of topics on the whole category';
$Desc['getcountcategorytopics']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['getcountcategorytopics']['Return']['integer'] = 'The number of replies';

//Get count category replies
$Desc['getcountcategoryreplies']['Name'] = 'getCountCategoryReplies';
$Desc['getcountcategoryreplies']['Desc'] = 'Get the number of replies of the whole category';
$Desc['getcountcategoryreplies']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['getcountcategoryreplies']['Return']['integer'] = 'The number of replies';

//Post forum reply
$Desc['postforumreply']['Name'] = 'postForumReply';
$Desc['postforumreply']['Desc'] = 'Post a reply to a forum thread';
$Desc['postforumreply']['Args']['$user_id']['Desc'] = 'The user id';
$Desc['postforumreply']['Args']['$topic_id']['Desc'] = 'The topic id';
$Desc['postforumreply']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['postforumreply']['Args']['$message']['Desc'] = 'The content of the reply';

//Post forum topic
$Desc['postforumtopic']['Name'] = 'postForumTopic';
$Desc['postforumtopic']['Desc'] = 'Post a new topic';
$Desc['postforumtopic']['Args']['$user_id']['Desc'] = 'The user id';
$Desc['postforumtopic']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['postforumtopic']['Args']['$topic']['Desc'] = 'The name of the topic';
$Desc['postforumtopic']['Args']['$message']['Desc'] = 'The content of the topic';

//Get category from id
$Desc['getcategoryfromid']['Name'] = 'getCategoryFromId';
$Desc['getcategoryfromid']['Desc'] = 'Get the category name from category id';
$Desc['getcategoryfromid']['Args']['$category_id']['Desc'] = 'The category id';
$Desc['getcategoryfromid']['Return']['string'] = 'The category name';

//Get topic from id
$Desc['gettopicfromid']['Name'] = 'getTopicFromId';
$Desc['gettopicfromid']['Desc'] = 'Get the name of a topic from topic id';
$Desc['gettopicfromid']['Args']['$topic_id'] = 'The topic id';
$Desc['gettopicfromid']['Return']['string'] = 'The topic name';

//Get category from topic
$Desc['getcategoryfromtopic']['Name'] = 'getCategoryFromTopic';
$Desc['getcategoryfromtopic']['Desc'] = 'Get the category from topic id';
$Desc['getcategoryfromtopic']['Args']['$topic_id']['Desc'] = 'The topic id';
$Desc['getcategoryfromtopic']['Return']['array'] = 'An array with category id and name';
