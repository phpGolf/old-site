<?php
if(!defined('INDEX')) {
    header('location: /');
}

//Get list of users
// $reset = Reset list
function users($reset = false) {
    static $users;
    if($reset) {
        $users=null;
        $mem = new cache;
        $mem->delete('users');
        $mem->close();
    }
    if(!$users) {
        $mem = new cache;
        $mem->key = 'users';
        if(!$data = $mem->get()) {
            $PDO =&DB::$PDO;
            $result = $PDO->query('SELECT id,username,`group`,permissions,avatar,gravatar,email FROM users');
            while(list($userid,$username,$group,$permissions,$avatar,$gravatar,$email)=$result->fetch()) {
                $users_save[$userid]['userid'] = $userid;
                $users_save[$userid]['username'] = $username;
                $users_save[$userid]['group'] = $group;
                $users_save[$userid]['permissions'] = $permissions;
                $users_save[$userid]['avatar'] = $avatar;
                $users_save[$userid]['email'] = $email;
                $users_save[$userid]['gravatar'] = (bool)$gravatar;
            }
            $mem->set(0,$users_save,MEMCACHE_COMPRESSED,(3600*24*7));
        } else {
            $users_save = $data;
        }
        foreach($users_save as $userid => $user) {
            $users[$userid] = $user;
            unset($users[$userid]['permissions']);
            $users[$userid]['permissions'] = array_merge(getGroupPermissions($user['group']), convertPermissions($user['permissions']));
        }
    }
    return $users;
}
//Get list of usergroups
// $reset = Reset list
function groups($reset = false) {
    static $groups;
    static $groups_save;
    if($reset) {
        $groups=null;
        $groups_save=null;
        $mem = new cache;
        $mem->delete('groups');
        $mem->close();
    }
    if(!$groups) {
        $mem = new cache;
        $mem->key = 'groups';
        if(!$data = $mem->get()) {
            $PDO =&DB::$PDO;
            $result = $PDO->query('SELECT id,name,permissions,`order` FROM groups ORDER BY `order` DESC');
            $permissions = permissions();
            while(list($id,$name,$permissionsId,$order)=$result->fetch()) {
                $groups[$id]['name'] = $name;
                $groups[$id]['order'] = $order;
                $groups_save[$id]['name'] = $name;
                $groups_save[$id]['order'] = $order;
                if(!empty($permissionsId)) {
                    $groups_save[$id]['permissions'] = explode(';',$permissionsId);
                    $groups[$id]['permissions'] = convertPermissions($permissionsId);
                }
            }
            $mem->set(0,$groups,MEMCACHE_COMPRESSED,3600);
        } else {
            $groups = $data;
        }
    }
    return $groups;
}

//Get list of permissions
// $reset = Reset list
function permissions($reset = false) {
    static $permissions;
    if($reset) {
        $permissions = null;
        $mem = new cache;
        $mem->delete('permissions');
        $mem->close();
    }
    if(!$permissions) {
        $mem = new cache;
        $mem->key = 'permissions';
        if(!$data = $mem->get()) {
            $PDO =&DB::$PDO;
            $result = $PDO->query('SELECT id,`key`,name FROM permissions ORDER BY `key`');
            while(list($id,$key,$name) = $result->fetch()) {
                if(!empty($key)) {
                    $permissions[$id]['key'] = $key;
                    $permissions[$id]['name'] = $name;
                }
            }
            $mem->set(0,$permissions,MEMCACHE_COMPRESSED,3600);
        } else {
            $permissions = $data;
        }
    }
    return $permissions;
}

//Check if $username is an user
// $username = username
function isUsername($username) {
    static $Users;
    if(!$Users) {
        foreach(users() as $uid => $user) {
            $Users[$user['username']] = $uid;
        }
    }
    return isset($Users[$username]);
}

function getUsernameFromId($id) {
    $users = users();
    return ($users[$id]['username']) ? $users[$id]['username'] : false;
}


function getCountryFromUserid($user_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT country FROM users WHERE id = :user_id');
    $stmt->execute(array(':user_id' => $user_id));
    $result = $stmt->fetch();
    return $result['country'];
}

function getIdFromUsername($username) {
    static $users;
    if(!$users) {
        $users_raw = users();
        foreach($users_raw as $uid => $user) {
            $users[$user['username']] = $user;
        }
    }
    return ($users[$username]['userid']) ? $users[$username]['userid'] : false;
}

//Get easy list of permissions
function getEasyPermissions($reset = false) {
    static $permissions;
    if($reset) {
        $permissions = null;
        $mem = new cache;
        $mem->delete('permissions');
        $mem->close();
    }
   if(count($permissions) == 0) {
        foreach(permissions() as $id => $perm) {
            $permissions[$perm['key']] = $id;
        }
    }
    return $permissions;
}

//Check if user have $key right
// $key = permissionkey to check
function access($key) {
    return isset($_SESSION['permissions'][$key]);
}

//Check if user is above userlevel
// $level = Userlevel to check
function userLevel($level) {
    return ($_SESSION['userLevel'] >= $level);
}
//Get userlevel of group
// $group = Group id
function getUserLevel($group) {
    if(isGroup($group)) {
        $groups = groups();
        return $groups[$group]['order'];
    } else {
        return false;
    }
}

//Check is group exists
// $id = Groupid to check
function isGroup($id) {
    $groups = groups();
    return isset($groups[$id]);
}

//Get group name from id
// $id = valid group id
function getGroupName($id) {
    $groups = groups();
    if(!isGroup($id)) {
        return false;
    }
    return $groups[$id]['name'];
}

//Get permissions to the group
// $id = valid group id
function getGroupPermissions($id) {
    $groups = groups();
    if(!isGroup($id)) {
        return array();
    }
    return $groups[$id]['permissions'];
}

//Add permission to list and DB
// $name = Name of the permission
// $key = Permission key
function addPermission($name,$key) {
    $permissions = permissions();
    foreach($permissions as $pid => $perm) {
        if($perm['name'] == $name || $perm['key'] == $key ) {
            return false;
        }
    }
    //Save
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('INSERT INTO permissions SET name=:name, `key`=:key');
    $pre->execute(array(
                ':name' => $name,
                ':key' => $key));
    permissions(true);
    return true;
}

//Edit permission
// $id = Valid permission id
// $name = Name of the permission
// $key = Permission key
function editPermission($id,$name,$key) {
    $permissions = permissions();
    if(!$permissions[$id]) {
        return false;
    }
    foreach($permissions as $pid => $perm) {
        if(($perm['name'] == $name || $perm['key'] == $key ) && $pid != $id) {
            return false;
        }
    }
    //Save
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('UPDATE permissions SET name=:name, `key`=:key WHERE id=:id');
    $pre->execute(array(
                ':name' => $name,
                ':key' => $key,
                ':id' => $id));
    permissions(true);
    return true;
}

//Delete permission
// $id = Valid permission id
function deletePermission($id) {
    $permissions = permissions();
    if(!$permissions[$id]) {
        return false;
    }
    //Remove
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('DELETE FROM permissions WHERE id=:id');
    $pre->execute(array(':id' => $id));
    permissions(true);
    return true;
}

//Create group
// $name = Group name
// $permissions = List of permission id, seperated by ;
// $order = Numeric value to represent the order (normaly 0-1000)
function addGroup($name,$permissions,$order){
    $groups = groups();
    foreach($groups as $gid => $group) {
        if($group['name'] == $name) {
            return false;
        }
    }
    if(empty($name)) {
        return false;
    }
    if(empty($order) && $order !== 0) {
        $order = 500;
    }
    //Save
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('INSERT INTO groups SET name=:name, `permissions`=:permissions, `order`=:order');
    $pre->execute(array(
                ':name' => $name,
                ':permissions' => $permissions,
                ':order'=>$order));
    groups(true);
    return true;
}

//Edit group
// $id = Valid group id
// $name = Group name
// $permissions = List of permission id, seperated by ;
// $order = Numeric value to represent the order (normaly 0-1000)
function editGroup($id,$name,$permissions,$order){
    $groups = groups();
    foreach($groups as $gid => $group) {
        if($group['name'] == $name && $gid != $id) {
            return false;
        }
    }
    if(empty($name)) {
        return false;
    }
    if(empty($order) && $order !== 0) {
        $order = 500;
    }
    //Save
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('UPDATE groups SET name=:name, `permissions`=:permissions, `order`=:order WHERE id=:id');
    $pre->execute(array(
                ':name' => $name,
                ':permissions' => $permissions,
                ':order'=>$order,
                ':id' => $id));
    groups(true);
    return true;
}

//Delete group
// $id = Valid group ID
function deleteGroup($id){
    $groups = groups();
    if(!$groups[$id] || $id == 2) {
        return false;
    }
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('DELETE FROM groups WHERE id=:id');
    $pre->execute(array(':id' => $id));
    $pre = $PDO->prepare('UPDATE users SET `group`=2 WHERE `group`=:group');
    $pre->execute(array(':group' => $id));
    groups(true);
    return true;
    
}

//Get permissions id from key
// $key = Permissions key
function getPermissionIdFromKey($key) {
    $permissions = getEasyPermissions();
    return (isset($permissions[$key])) ? $permissions[$key] : false;
}

//Convert permissions
// $permissions = String with permissions ID's, seperated by ; / array with permissions
function convertPermissions($permissions) {
    $allPermissions = permissions();
    $perms = array();
    if(empty($permissions)) {
        return $perms;
    }
    if(!is_array($permissions)) {
        foreach(explode(';',$permissions) as $permid) {
            if(!empty($allPermissions[$permid]['key'])) {
                $perms[$allPermissions[$permid]['key']] = true;
            }
        }
        return $perms;
    } else {
        foreach($permissions as $permission => $value) {
            if($id = getPermissionIdFromKey($permission)) {
                $perms[] = $id;
            }
        }
        sort($perms);
        return implode(';',$perms);
    }
    
}

//Get total of submissions an user have made
// $userid = valid userid
function getTotalUserSubmissions($userid) {
    static $total = array();
    if(!isset($total[$userid])) {
        $mem = new cache;
        $mem->key = 'TotalUserSub_'.$userid;
        if(($data = $mem->get()) === false) {
            $PDO =&DB::$PDO;
            $stmt = $PDO->prepare('SELECT id FROM attempts WHERE user_id = :user');
            $stmt->execute(array(':user' => $userid));
            $total[$userid] = count($stmt->fetchAll());
            $mem->set(0,$total[$userid],0,3600);
        } else {
            $total[$userid] = $data;
        }
    }
    return $total[$userid];
}

//Get challenges the user have done/tried
// $userid = valid userid
function getUserChalls($userid, $only_open_active_priv=false) {
    static $challenges = array();
    if(!isset($challenges[$userid])) {
        $mem = new cache;
        $mem->key = 'UserChallenges_'.$userid;
        if(!$data = $mem->get()) {
            $PDO = &DB::$PDO;
            if ($only_open_active_priv) {
                $pre = $PDO->prepare("SELECT  min(a.size) size, a.passed, c.id, c.name
                                    FROM attempts a, challenges c 
                                   WHERE c.id = a.challenge_id AND c.open = 1 AND c.type = 'private' AND c.active = 1 AND a.user_id=:userid AND a.executed='1' AND a.passed='1' GROUP BY c.id ORDER BY min(a.size) ASC");
            } else {
                $pre = $PDO->prepare("SELECT  min(a.size) size, a.passed, c.id, c.name
                                     FROM attempts a, challenges c 
                                     WHERE c.id = a.challenge_id AND a.user_id=:userid AND a.executed='1' AND a.passed='1' GROUP BY c.id ORDER BY min(a.size) ASC");
            }
            $pre->execute(array(':userid' => $userid));
            if($pre->rowCount() == 0) {
                $challenges[$userid] = array();
            } else {
                while(list($stat['size'],$stat['passed'],$stat['challenge_id'],$stat['challenge_name'])=$pre->fetch()) {
                    $challenges[$userid][] = $stat;
                    unset($stat);
                }
            }
            $mem->set(0,$challenges[$userid],0,3600);
        } else {
            $challenges[$userid] = $data;
        }
    }
    if(!$all && count($challenges[$userid]) != 0) {
        foreach($challenges[$userid] as $challenge) {
            if($challenge['passed'] == true) {
                $return[] = $challenge;
            }
        }
    } else {
        $return = $challenges[$userid];
    }
    return $return;
}


//Avatar
function getUserAvatar($uid,$size=false) {
    static $urls;
    if(!$urls[$uid][(int)$size]) {
        $users = users();
        $user = $users[$uid];
        if($user['gravatar']) {
            if(!function_exists('createGravatarUrl')) {
                return false;
            }
            $url = createGravatarUrl($user['email'],true);
            if($url == GRAVATAR_IMAGE_NOTFOUND) {
                return false;
            }
            if($size && is_numeric($size)) {
                $url .= '?s='.$size;
            }
            $urls[$uid][$size] = $url;
            return $url;
        } elseif($user['avatar']) {
            $PDO =&DB::$PDO;
            $pre = $PDO->prepare('SELECT format FROM images WHERE id=:id');
            $pre->execute(array(':id' => $user['avatar']));
            $res = $pre->fetch();
            if(!$res) {
                return false;
            }
            $url = 'http://'.DOMAIN.'/image/show/'.$user['avatar'].'.'.$res['format'];
            if($size && is_numeric($size)) {
                $url .= '?size='.$size;
            }
            $urls[$uid][$size] = $url;
            return $url;
        } else {
            return false;
        }
    }
    return $urls[$uid][(int)$size];
}

function medals($rank,$cup=false) {
    if ($cup) {
        $cup = 'cup_';
        $style = 'style="width:19px; height:13px;"';
    } else {
        $style = 'style="width:11px; height:11px;"';
        $cup = '';
    }
    if ($rank == 1) {
        $medal = '<img src="'.GFX.'icons/'.$cup.'gold.png" '.$style.' alt="1" title="Gold" />';
    } elseif ($rank == 2) {
        $medal = '<img src="'.GFX.'icons/'.$cup.'silver.png" '.$style.' alt="2" title="Silver" />';
    } elseif ($rank == 3) {
        $medal = '<img src="'.GFX.'icons/'.$cup.'bronze.png" '.$style.' alt="3" title="Bronze" />';
    } else {
        $medal = $rank;
    }
    return $medal;
}




