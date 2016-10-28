<?php
if(!defined('INDEX')) {
    header('location: /');
}

class xForum_category {
    private $cat_id;
    private $cat_title;
    private $cat_desc;
    private $cat_domid;
    private $cat_userLevel;
    private $categories = array();
    private $topics = array();
    
    public function __construct($cat_id) {
        return $this->setCategory($cat_id);
    }
    
    public function update() {
        $this->setCategory($this->cat_id);
        return true;
    }
    
    public function setCategory($id) {
        $PDO =&DB::$PDO;
        //Check if cat id exists
        if($id != 0) {
            $pre = $PDO->prepare('SELECT title,description,dom_id,user_level FROM xforum_categories WHERE id=:id LIMIT 1');
            $pre->execute(array(':id' => $id));
            $cat = $pre->fetch();
            unset($pre);
            if(count($cat) == 0) {
                msg('Did not find category',0);
                return false;
            }
            $this->cat_id = $id;
            $this->cat_title = $cat['title'];
            $this->cat_desc = $cat['description'];
            $this->cat_domid = $cat['dom_id'];
            $this->cat_userLevel = $cat['user_level'];
        } else {
            $this->cat_id = NULL;
            $this->cat_title = 'Forum';
            $this->cat_desc = '';
            $this->cat_domid = NULL;
            $this->cat_userLevel = 0;
        }
        
        //Get categories in category
        $this->categories = array();
        $pre = $PDO->prepare('SELECT id AS category_id, title, description, user_level FROM xforum_categories WHERE dom_id=:id');
        $pre->execute(array(':id' => $this->cat_id));
        while(list($cat_id,$title,$desc,$userLevel)=$pre->fetch()) {
            $categories[$cat_id]['id'] = $cat_id;
            $categories[$cat_id]['title'] = $title;
            $categories[$cat_id]['desc'] = $desc;
            $categories[$cat_id]['userLevel'] = $userLevel;
        }
        $this->categories = $categories;
        
        //Get topics in category
        $this->topics = array();
        $pre = $PDO->prepare('SELECT t.id AS topic_id, u.username, t.posted, t.title FROM xforum_replies AS t, users AS u WHERE t.cat_id=:id AND t.topic_id=t.id AND u.id=t.user_id');
        $pre->execute(array(':id' => $this->cat_id));
        while(list($topic_id,$username,$posted,$title)=$pre->fetch()) {
            $topics[$topic_id]['id'] = $topic_id;
            $topics[$topic_id]['title'] = $title;
            $topics[$topic_id]['username'] = $username;
            $topics[$topic_id]['posted'] = $posted;
        }
        $this->topics = $topics;
    }
    
    public function getId() {
        return $this->cat_id;
    }
    
    public function getTitle() {
        return $this->cat_title;
    }
    
    public function getDescription() {
        return $this->cat_desc;
    }
    
    public function getDomId() {
        return $this->cat_domid;
    }
    
    public function getUserLevel() {
        return $this->cat_userLevel;
    }
    
    public function getCategories() {
        return $this->categories;
    }
    
    public function getTopics() {
        return $this->topics;
    }
    
    //Append category to current category
    // $cat_id, ID to category that is appended
    public function appendCategory($cat_id) {
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE xforum_categories SET dom_id=:dom_id WHERE id=:id');
        $pre->execute(array(':id' => $cat_id, ':dom_id' => $this->cat_id));
        if($pre->rowCount() != 1) {
            msg('Category was moved',1);
            return false;
        }
        msg('Category is moved',1);
        return true;
    }
    
    //Create
    // $title = title of category
    // $desc = description of category
    // $dom_id = category this is under, or false for root
    // $userLevel = minimum userlevel to see and access category
    //
    //If success, returning xForum_category object
    static public function createCategory($title,$desc,$dom_id=false,$userLevel=0) {
        $PDO =&DB::$PDO;    
        $pre = $PDO->prepare('SELECT id FROM xforum_categories WHERE title LIKE :title');
        $pre->execute(array(':title' => $title));
        if(count($pre->fetchAll()) != 0) {
            msg('Category title already exists',0);
            return false;
        }
        if($dom_id) {
            $pre = $PDO->prepare('SELECT id FROM xforum_categories WHERE id=:dom_id');
            $pre->execute(array(':dom_id' => $dom_id));
            if(count($pre->fetchAll()) != 1) {
                msg('The category you want to place it under don\'t exist',0);
                return false;
            }
        }
        $pre = $PDO->prepare('INSERT INTO xforum_categories SET title=:title, description=:desc, user_level=:userLevel, dom_id=:dom_id');
        $pre->execute(array(
                            ':title' => $title,
                            ':desc' => $desc,
                            ':userLevel' => $userLevel,
                            ':dom_id' => $dom_id));
        $category_id = $PDO->lastInsertId();
        if($pre->rowCount() != 1) {
            msg('An error happend under creating the category',0);
            return false;
        }
        return new xForum_category($category_id);
    }
    
    //Delete
    // $cat_id = category id to delete
    static public function deleteCategory($cat_id) {
        $PDO =&DB::$PDO;
        //Move all topics and categories to Default
        $preCat = $PDO->prepare('UPDATE xforum_categories SET dom_id=1 WHERE dom_id=:id');
        $preTopic = $PDO->prepare('UPDATE xforum_replies SET cat_id=1 WHERE cat_id=:id');
        $preCat->execute(array(':id'=>$cat_id));
        $preTopic->execute(array(':id'=>$cat_id));
        //Delete category
        $preDel = $PDO->prepare('DELETE FROM xforum_categories WHERE id=:id');
        $preDel->execute(array(':id' => $cat_id));
        if($preDel->rowCount() == 0) {
            msg('Did not delete category',0);
            return false;
        } else {
            return true;
        }
    }
    
    //Edit
    public function edit($title=false,$desc=false,$dom_id=false,$userLevel=false) {
        $return = self::editChallenge($this->cat_id,$title,$desc,$dom_id,$userLevel);
        $this->update();
        return $return;
    }
    
    static public function editChallenge($cat_id,$title=false,$desc=false,$dom_id=false,$userLevel=false) {
        //Get old info
        
    }
}


//Get categories
// $dom_id = category id, or false for root
function getCategories($id=false) {
    static $categories = array();
        if(empty($categories[$id])) {
        $PDO =&DB::$PDO;
        $sql = 'SELECT id AS category_id, title, description, dom_id,user_level FROM xforum_categories';
        if($id) {
            $sql .= ' WHERE dom_id=:id';
        }
        $pre = $PDO->prepare($sql);
        if($id) {
            $pre->execute(array(':id' => $id));
        } else {
            $pre->execute();
        }
        while(list($category_id,$title,$desc,$dom_id,$userLevel)=$pre->fetch()) {
            $categories[$id][$category_id]['id'] = $category_id;
            $categories[$id][$category_id]['title'] = $title;
            $categories[$id][$category_id]['description'] = $desc;
            $categories[$id][$category_id]['userLevel'] = $userLevel;
        }
    }
    return $categories[$id];
}

//Create categories
// $title = title of category
// $desc = description of category
// $userLevel = minimum userlevel to see and access category
// $dom_id = category this is under, or false for root
function createCategory($title,$desc,$userLevel=0,$dom_id=false) {
    $PDO =&DB::$PDO;
    $pre = $PDO->prepare('SELECT id FROM xforum_categories WHERE title LIKE :title');
    $pre->execute(array(':title' => $title));
    if(count($pre->fetchAll()) != 0) {
        msg('Category title already exists',0);
        return false;
    }
    if($dom_id) {
        $pre = $PDO->prepare('SELECT id FROM xforum_categories WHERE id=:dom_id');
        $pre->execute(array(':dom_id' => $dom_id));
        if(count($pre->fetchAll()) != 1) {
            msg('The category you want to place it under don\'t exist',0);
            return false;
        }
    }
    $pre = $PDO->prepare('INSERT INTO xforum_categories SET title=:title, description=:desc, user_level=:userLevel, dom_id=:dom_id');
    $pre->execute(array(
                        ':title' => $title,
                        ':desc' => $desc,
                        ':userLevel' => $userLevel,
                        ':dom_id' => $dom_id));
    $category_id = $PDO->lastInsertId();
    if($pre->rowCount() != 1) {
        msg('An error happend under creating the category',0);
        return false;
    }
    return $category_id;
}


