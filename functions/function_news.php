<?php
if(!defined('INDEX')) {
    header('location: /');
}
//All news posts
// $reset = Reset list
function news($reset = false) {
    static $news;
    if($reset) {
        $news = null;
        $mem = new cache;
        $mem->delete('News');
        $mem->close();
    }
    if(!$news) {
        $mem = new cache;
        $mem->key = 'News';
        if(!$data = $mem->get()) {
            $PDO =&DB::$PDO;
            $result = $PDO->query('SELECT n.id, n.author, u.username as authorname, n.date, n.title, n.post, n.challenge, n.twitter, n.bitly FROM news as n, users as u WHERE u.id = n.author ORDER BY n.date DESC');
            while(list($id,$author_id,$author_name,$date,$title,$text,$challenge_id,$twitter,$bitly)=$result->fetch()) {
                $aTitle = explode(' ',$title);
                $aTitle = implode(' ',array_pad($aTitle,(count($aTitle)>3 ? 3 : count($aTitle)),0));
                $safetitle = preg_replace('/[^0-9a-zA-Z\-]/','',str_replace(' ','-',$aTitle));
                $news[$id]['newsid'] = $id;
                $news[$id]['author'] = $author_id;
                $news[$id]['author_name'] = $author_name;
                $news[$id]['date'] = $date;
                $news[$id]['title'] = $title;
                $news[$id]['safetitle'] = $safetitle;
                $news[$id]['text'] = $text;
                $news[$id]['challenge_id'] = $challenge_id;
                $news[$id]['twitter'] = $twitter;
                $news[$id]['bitly'] = $bitly;
            }
            $mem->set(0,$news,MEMCACHE_COMPRESSED,(3600*24));
        } else {
            $news = $data;
        }
    }
    return $news;
}
