<?php
if(!defined('INDEX')) {
    header('location: /');
}

//Class for user modification
class User {
    private $username;
    private $userid;
    private $email;
    private $group;
    private $website;
    private $userLevel;
    private $permissions;
    private $country;
    private $groupPermissions;
    private $password;
    private $avatar;
    public $publicEmail;
    public $timezone;
    public $gravatar;
    public $hash;
    public $regdate;
    public $last_ip;
    public $last_time;
    private $PDO;
    
    //Construct
    // $userid = userid or username (depends on $is_username)
    // $is_username = true if $userid is an username
    function __construct($userid,$is_username=false) {
        $this->PDO = &DB::$PDO;
        $PDO = &$this->PDO;
        
        //Get information
        $sql = 'SELECT id,username,email,hash,permissions,country,regdate,last_ip,last_time,`group`,timezone,password,public_email,website,avatar,gravatar FROM users WHERE ';
        if($is_username) {
            $sql .= 'username LIKE :userid';
        } else {
            $sql .= 'id=:userid';
        }
        $pre = $PDO->prepare($sql);
        $pre->execute(array(':userid' => $userid));
        if($pre->rowCount() != 1) {
            error(0,'Unknown user','The user was not found!');
            return false;
        }
        list($this->userid,$this->username,$this->email,$this->hash,$permissions,$this->country,$this->regdate,$this->last_ip,$this->last_time,$this->group,$this->timezone,$this->password,$this->publicEmail,$this->website,$this->avatar,$this->gravatar)=$pre->fetch();
        
        $this->permissions = convertPermissions($permissions);
        $this->groupPermissions = getGroupPermissions($this->group);
        $groups = groups();
        $this->userLevel = getUserLevel($this->group);
    }
    
    //Get username
    public function getUsername() {
        return $this->username;
    }
    
    //Get last time
    public function getLastTime() {
        return $this->last_time;
    }
    
    //Set username
    public function setUsername($username) {
        if(isUsername($username)) {
            msg('Username is taken',0);
            return false;
        }
        if(!preg_match('/^[a-zA-Z0-9]{3,32}$/',$username)) {
            msg('Username is invalid',0);
            return false;
        }
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET username=:username WHERE id=:id');
        $pre->execute(array(':username' => $username,
                            ':id' => $this->userid));
        $this->username = $username;
        $this->updateSession();
        users(true);
        return true;
    }
    
    //Get userid
    public function getUserid() {
        return $this->userid;
    }
    
    //Get email
    public function getEmail() {
        return $this->email;
    }
    
    //Get country
    public function getCountry() {
        return $this->country;
    }

    //Get website
    public function getWebsite() {
        return $this->website;
    }
    //Set website
    public function setWebsite($url) {
        if(!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            msg('Website is invalid',0);
            return false;
        }
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET website=:website WHERE id=:id');
        $pre->execute(array(':website' => $url,
                            ':id' => $this->userid));
        $this->website = $url;
        users(true);
        return true;
    }


    //Set country
    public function setCountry($country) {
        if (!empty($country)) {
            $PDO = &$this->PDO;
            $pre = $PDO->prepare('UPDATE users SET country=:country WHERE id=:id');
            $pre->execute(array(':id' => $this->userid,
                                ':country' => $country));
            return true;
        }
    }

    //Set email public
    public function setEmailPublic($value) {
        $value = ($value) ? '1' : '0';
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET public_email=:public WHERE id=:id');
        $pre->execute(array(':id' => $this->userid,
                            ':public' => $value));
        $this->publicEmail = ($value) ? true : false;
        return true;
    }
    
    //Set email
    public function setEmail($email) {
        if(!preg_match('/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-+]+(\.[a-z0-9-+]+)*(\.[a-z+]{2,3})$/',$email)) {
            msg('Email is invalid',0);
            return false;
        }
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('SELECT id FROM users WHERE email=:email');
        $pre->execute(array(':email' => $email));
        if(count($pre->fetchAll()) != 0) {
            msg('Email is already in use',0);
            return false;
        }
        $this->email = $email;
        $pre = $PDO->prepare('UPDATE users SET email=:email WHERE id=:userid');
        $pre->execute(array(':userid' => $this->userid,':email' => $this->email));
        return true;
    }
    
    //Get permissions
    public function getPermissions() {
        return $this->permissions;
    }
    //Get group permissions
    public function getGroupPermissions() {
        return $this->groupPermissions;
    }
    
    //Get all permissions
    public function getAllPermissions() {
        return array_merge($this->permissions, $this->groupPermissions);
    }
    
    //Get permission
    public function getPermission($name) {
        return ($this->permissions[$name] === true) ? true : false;
    }
    
    //Set permission
    public function setPermission($name,$value) {
        if(!$value) {
            unset($this->permissions[$name]);
        } else {
            if($this->groupPermissions[$name]) {
                return true;
            }
            $this->permissions[$name] = true;
        }
        $this->savePermissions();
        return true;
    }
    
    //Set multiply permissions
    // $permissions = array/string with permissions
    public function setMultPermissions($permissions) {
        if(!is_array($permissions)) {
            $permissions = convertPermissions($permissions);
        }
        $perm = array();
        foreach($permissions as $key => $value) {
            if(!$this->groupPermissions[$key]) {
                $perm[$key] = true;
            }
        }
        $this->permissions = $perm;
        $this->savePermissions();
        return true;
    }
    
    //Save permissions
    private function savePermissions() {
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET permissions=:permissions WHERE id=:userid');
        $pre->execute(array(':userid' => $this->userid,':permissions' => convertPermissions($this->permissions)));
        $this->updateSession();
        users(true);
    }
    
    //Get userLevel
    public function getUserLevel() {
        return $this->userLevel;
    }
    //Get group
    public function getGroup() {
        return $this->group;
    }
    //Get groupname
    public function getGroupName() {
        $groups = groups();
        return $groups[$this->group]['name'];
    }
    
    //Set group
    public function setGroup($group) {
        if(!isGroup($group)) {
            msg('The group is not valid',0);
            return false;
        }
        $this->group = $group;
        $this->userLevel = getUserLevel($group);
        $this->groupPermissions = getGroupPermissions($this->group);
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET `group`=:group WHERE id=:userid');
        $pre->execute(array(':userid' => $this->userid,':group' => $group));
        $this->updateSession();
        users(true);
        return true;
    }
    
    //Check password
    public function checkPassword($password) {
        return (md5($password) == $this->password);
    }
    //Set password
    public function setPassword($password) {
        if(strlen($password) < 5) {
            msg('Password is too short',0);
            return false;
        }
        $password = md5($password);
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET password=:password WHERE id=:userid');
        $pre->execute(array(':userid' => $this->userid,':password' => $password));
        return true;
    }
    
    //Get regdate
    public function getRegdate() {
        return $this->regdate;
    }
    
    //Set regdate
    public function setRegdate($date) {
        if(!$time = strtotime($date)) {
            msg('Wrong date format',0);
            return false;
        }
        $date = date('Y-m-d',$time);
        $PDO = &$this->PDO;
        $pre = $PDO->prepare('UPDATE users SET regdate=:regdate WHERE id=:userid');
        $pre->execute(array(':userid' => $this->userid,':regdate' => $date));
        $this->regdate = $date;
        return true;
    }
   
    //Generate new hash
    public function genHash() {
        $hash = randStr(128);
        $PDO =&$this->PDO;
        $PDO->exec('UPDATE users SET hash="'.$hash.'" WHERE id='.$this->userid);
        $this->updateSession();
    }
    
    //Rem avatar
    public function remAvatar() {
        $PDO =&$this->PDO;
        $PDO->exec('UPDATE users SET avatar=0, gravatar=false WHERE id='.$this->userid);
        $this->avatar = 0;
        $this->gravatar = false;
        users(true);
    }
    //Set avatar
    public function setAvatar($id,$gravatar=false) {
        $PDO =&$this->PDO;
        if($gravatar) {
            //Check id gravatar exists
            if($url = createGravatarUrl($this->email,true) == GRAVATAR_IMAGE_NOTFOUND) {
                msg('Did not find your gravatar image',0);
                return false;
            }
            $PDO->exec('UPDATE users SET avatar=0, gravatar=true WHERE id='.$this->userid);
            $this->avatar = 0;
            $this->gravatar = true;
            users(true);
            return true;
        }
        //Check if image exists
        $pre = $PDO->prepare('SELECT id FROM images WHERE id=:id');
        $pre->execute(array(':id' => $id));
        if(!$pre->fetch()) {
            msg('Not an valid image',0);
            return false;
        }
        $pre = $PDO->prepare('UPDATE users SET avatar=:avatar, gravatar=false WHERE id='.$this->userid);
        $pre->execute(array(':avatar' => $id));
        $this->gravatar = false;
        $this->avatar = $id;
        users(true);
        return true;
    }
    //Get avatar
    public function getAvatar($size=false) {
        return getUserAvatar($this->userid,$size);
    }
    
    //Set timezone
    public function setTimezone($timezone) {
        $PDO =&$this->PDO;
        if(!in_array($timezone,timezone_identifiers_list())) {
            msg('Invalid timezone',0);
            return false;
        }
        $PDO->exec('UPDATE users SET timezone="'.$timezone.'" WHERE id='.$this->userid);
        $this->timezone = $timezone;
        date_default_timezone_set($timezone);
        $this->updateSession();
        return true;
    }
    
    //Update session
    private function updateSession() {
        if($_SESSION['id'] == $this->userid) {
            $Allperm = $this->getAllPermissions();
            login($this->userid,$this->username,$this->hash,$this->userLevel,$this->timezone,$Allperm,(isset($_COOKIE['autologin'])));
            clearHttpCache();
        }
    }
}
?>
