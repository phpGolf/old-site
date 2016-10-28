<?php
if(!defined('INDEX')) {
    header('location: /');
}

class News {
    private $twitter;
    private $title;
    private $safetitle;
    private $text;
    private $author;
    private $author_name;
    private $bitly;
    private $date;
    private $post_id;
    
    //Construct
    // $id = If false, new post
    function __construct($id=false,$error=true) {
        $PDO =&DB::$PDO;
        if($id) {
            $pre = $PDO->prepare('SELECT n.id, n.title, n.post, n.author, u.username as authorname, n.date, n.challenge, n.twitter, n.bitly
                                    FROM news as n, users as u
                                    WHERE n.id=:id AND n.author=u.id');
            $pre->execute(array(':id' => $id));
            list($this->post_id,$this->title,$this->text,$this->author,$this->author_name,$this->date,$this->challenge,$this->twitter,$this->bitly)=$pre->fetch();
            if(empty($this->post_id)) {
                if($error) {
                    msg('Did not find post',0);
                }
                return false;
            }
        }
        return true;
    }
    
    //Add
    // $title = Title on post
    // $text = Text in post
    // $author = Author on post
    // $date = Post date
    // $challenge = Challenge that is connected (default: false)
    // $link = make link. Options: false=no link, 1=post, 2=challenge (default: false)
    // $twitter = make twitter post (default: false)
    // $bitly = make Bit.ly link (default: false)
    function add($title,$text,$author,$date,$challenge=false,$link=false,$twitter=false,$bitly=false) {
        $PDO =&DB::$PDO;

        //Check title length
        if(strlen($title) < 5) {
            msg('Title too short',0);
            $E = true;
        }
        //Check text length
        if(strlen($text) < 10) {
            msg('Text too short',0);
            $E = true;
        }
        //Check author
        $users = users();
        if(!$users[$author]['permissions']['news_add']) {
            msg('Not valid author',0);
            $E = true;
        }
        //Check time and make propper format
        $timestamp = strtotime($date);
        if($timestamp == 0 || $timestamp > time()) {
            msg('Invalid date',0);
            $E = true;
        }
        $date = date('Y-m-d H:i:s',$timestamp);
        //If any errors, return false
        if($E) {
            return false;
        }
        //Safe title
        $aTitle = explode(' ',$title);
        $aTitle = implode(' ',array_pad($aTitle,(count($aTitle)>3 ? 3 : count($aTitle)),0));
        $safetitle = preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$aTitle));
        
        //Twitter
        $tweet_id = '';
        if($twitter) {
            if(!in_array('Twitter',get_declared_classes())) {
                msg('Not posted on twitter, Twitter class not loaded.',0);
            } else {
                $tweet_post = $title;
                if($challenge) {
                    if(!in_array(substr($tweet_post,-1),array('.',',','!','?'))) {
                        $tweet_post .= '. ';
                    }
                    if(substr($tweet_post,-1) != ' ') {
                        $tweet_post .= ' ';
                    }
                    $challenges = challenges();
                    $cname = $challenges[$challenge]['name'];
                    $safename = $challenges[$challenge]['safename'];
                    $tweet_post .= $cname.'! ';
                }
                //Get post URL
                if($link == 1) {
                    $result = $PDO->query("SELECT `AUTO_INCREMENT` FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME='news'");
                    $newId = $result->fetch();
                    $newId = $newId[0];
                    $url = 'http://'.DOMAIN.'/news/'.$newId.'-'.$safetitle;
                }
                
                //Get challenge URL
                if($link == 2) {
                    $url = 'http://'.DOMAIN.'/challenge/'.$safename.'/';
                }
                
                if($link) {
		    if(!in_array(substr($tweet_post,-1),array('.',',','!','?'))) {
                        $tweet_post .= '. ';
                    }
                    if(substr($tweet_post,-1) != ' ') {
                        $tweet_post .= ' ';
                    }
                    if($bitly) {
                        $Bitly = new Bitly;
                        $bitly_url = $Bitly->shorten($url);
                        if($bitly_url) {
                            $tweet_post .= 'Url: '.$bitly_url;
                        }
                    } else {
                        $tweet_post .= 'Url: '.$url;
                    }
                }
                $Tweet = new Twitter;
                $Result = $Tweet->post($tweet_post);
                if($Result['code'] != 200) {
                    msg('Twitter error: '.$Result['error'],0);
                } else {
                    $tweet_id = $Result['postid'];
                }
            }
        }
        //Make post
        $pre = $PDO->prepare('INSERT INTO news (
                                    author,
                                    date,
                                    title,
                                    post,
                                    challenge,
                                    twitter,
                                    bitly
                                    ) VALUES (
                                    :author,
                                    :date,
                                    :title,
                                    :post,
                                    :challenge,
                                    :twitter,
                                    :bitly)');
        $pre->execute(array(
                            ':author' => $author,
                            ':date' => $date,
                            ':title' => $title,
                            ':post' => $text,
                            ':twitter' => $tweet_id,
                            ':challenge' => ($challenge) ? $challenge : '',
                            ':bitly' => ($bitly_url) ? $bitly_url : ''));
        if($pre->errorCode() != 00000) {
            msg('Failed to save post!',0);
            return false;
        }
        $post_id = $PDO->lastinsertid();
        news(true);
        $this->post_id = $post_id;
        $this->title = $title;
        $this->safetitle = $safetitle;
        $this->text = $text;
        $this->date = $date;
        $this->challenge = $challenge;
        $this->bitly = $bitly;
        $this->author = $author;
        $this->author_name = getUsernameFromId($author);
    }
    
    //Get id
    public function getId() {
        return ($this->post_id) ? $this->post_id : false;
    }
    
    //Get title
    public function getTitle() {
        if($this->post_id) {
            return $this->title;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Set title
    public function setTitle($title) {
        if($this->post_id) {
            if(strlen($title) < 5) {
                msg('Title too short',0);
                return false;
            }
            if($title == $this->title) {
             return true;
            }
            $PDO = &DB::$PDO;
            $pre = $PDO->prepare('UPDATE news SET title=:title WHERE id=:id');
            $pre->execute(array(':title' => $title,':id' => $this->post_id));
            $this->title = $title;
            return true;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get text
    public function getText() {
        if($this->post_id) {
            return $this->text;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Set text
    public function setText($text) {
        if($this->post_id) {
            if(strlen($text) < 10) {
                msg('Text too short',0);
                return false;
            }
            if($text == $this->text) {
             return true;
            }
            $PDO = &DB::$PDO;
            $pre = $PDO->prepare('UPDATE news SET post=:post WHERE id=:id');
            $pre->execute(array(':post' => $text,':id' => $this->post_id));
            $this->text = $text;
            return true;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    //Get date
    public function getDate() {
        if($this->post_id) {
            return $this->date;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get challenge
    public function getChallenge() {
        if($this->post_id) {
            return $this->challenge;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get twitter
    public function getTwitter() {
        if($this->post_id) {
            return $this->twitter;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get bitly
    public function getBitly() {
        if($this->post_id) {
            return $this->bitly;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get author
    public function getAuthor() {
        if($this->post_id) {
            return $this->author;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Get author name
    public function getAuthorName() {
        if($this->post_id) {
            return $this->author_name;
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
    
    //Delete post
    public function delete($force=false) {
        if($this->post_id) {
            $PDO =&DB::$PDO;
            //Delete twitter post
            if($this->twitter) {
                $twitter = new Twitter;
                $Result = $twitter->delete(number_format($this->twitter,0,'',''));
                if($Result['code'] != 200) {
                    msg('Twitter error: '.$Result['error'],0);
                    if(!$force) {
                        msg('Post was not deleted from DB, <a href="?action=delete&id='.$this->post_id.'&force=true">remove anyway?</a>',0);
                        return false;
                    } else {
                        msg('Post was deleted from DB, but not from twitter!');
                    }
                }
            }
            //Delete post
            if($PDO->exec('DELETE FROM news WHERE id='.$this->post_id.'')) {
                return true;
            } else {
                msg('Post was not deleted from database',0);
                return false;
            }
        } else {
            msg('No news post is active',0);
            return false;
        }
    }
}
