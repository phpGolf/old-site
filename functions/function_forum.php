<?php
if(!defined('INDEX')) {
    header('location: /');
}

function forumDeleteTopic($topic_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('DELETE FROM forum_topics WHERE id = :topic LIMIT 1');
    $stmt->execute(array(':topic' => $topic_id));
}

function forumDeleteReply($reply_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('DELETE FROM forum_replies WHERE id = :reply LIMIT 1');
    $stmt->execute(array(':reply' => $reply_id));
}

function forumEditTopic($topic_id, $text) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE forum_topics SET text = :text WHERE id = :topic_id');
    $stmt->execute(array(':topic_id' => $topic_id, ':text' => $text));
}

function forumEditReply($reply_id, $text) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('UPDATE forum_replies SET text = :text WHERE id = :reply_id');
    $stmt->execute(array(':reply_id' => $reply_id, ':text' => $text));
}

function getForumCategories() {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #$mem->key = 'ForumCategories';
    #if (!$data = $mem->get()) {
        $row = $PDO->query('SELECT * FROM forum_categories ORDER BY id DESC')->fetchAll();
    #    $mem->set(0,$row,0,60*60);
    #}
    #else {
    #    $row = $data;
    #}
    return $row;
}

function getForumTopics($category_id, $topic_id=0) {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #if ($topic_id == 0) {
    #    $mem->key = 'ForumTopics_' . $category_id;
    #}
    #else {
    #    $mem->key = 'ForumTopics_' . $topic_id;
    #}
    #if (!$data = $mem->get()) {
        if ($topic_id == 0) {
            $stmt = $PDO->prepare('SELECT * FROM forum_topics WHERE category_id = :cat ORDER BY timestamp DESC');
            $stmt->execute(array(':cat' => $category_id));
        }
        else {
            $stmt = $PDO->prepare('SELECT * FROM forum_topics WHERE id = :topic');
            $stmt->execute(array(':topic' => $topic_id));
        }
        $row = $stmt->fetchAll();
    #    #$mem->set(0,$row,0,60*60);
    #}
    #else {
    #    $row = $data;
    #}
    return $row;
}

// Suddenly decided to NOT RETURN A FUCKING THING! NEED TO BE FIXED!!!111
function getCategoryFromTopic($topic_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT t.category_id, c.category FROM forum_topics t, forum_categories c WHERE t.id = :topic_id AND t.category_id = c.id');
    $stmt->execute(array(':topic_id' => $topic_id));
    $row = $stmt->fetch();
    //print_r(array('id' => $row['category_id'], 'name' => $row['category']));
    return array('id' => $row['category_id'], 'name' => $row['category']);
}

function getForumReplies($topic_id) {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #$mem->key = 'ForumReplies_' . $topic_id;
    #if (!$data = $mem->get()) {
        $stmt = $PDO->prepare('SELECT * FROM forum_replies WHERE topic_id = :topic ORDER BY timestamp ASC');
        $stmt->execute(array(':topic' => $topic_id));
        $row = $stmt->fetchAll();
    #    $mem->set(0,$row,0,60*60);
    #}
    #else {
    #    $row = $data;
    #}
    return $row;
}

function getCountTopicReplies($topic_id) {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #$mem->key = 'TopicReplies_' . $topic_id;
    #if (!$data = $mem->get()) {
        $stmt = $PDO->prepare('SELECT id FROM forum_replies WHERE topic_id = :topic');
        $stmt->execute(array(':topic' => $topic_id));
        $count = count($stmt->fetchAll());
    #    $mem->set(0,$count,0,60*60);
    #}
    #else {
     #   $count = $data;
    #}
    return $count;
}

function getCountCategoryTopics($category_id) {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #$mem->key = 'CategoryTopics_' . $category_id;
    #if (!$data = $mem->get()) {
        $stmt = $PDO->prepare('SELECT id FROM forum_topics WHERE category_id = :cat');
        $stmt->execute(array(':cat' => $category_id));
        $count = count($stmt->fetchAll());
    #    $mem->set(0,$count,0,60*60);
    #}
    #else {
    #    $count = $data;
    #}
    return $count;
}

function getCountCategoryReplies($category_id) {
    $PDO =&DB::$PDO;
    #$mem = new cache;
    #$mem->key = 'CategoryReplies_' . $category_id;
    #if (!$data = $mem->get()) {
        $stmt = $PDO->prepare('SELECT id FROM forum_replies WHERE category_id = :cat');
        $stmt->execute(array(':cat' => $category_id));
        $count = count($stmt->fetchAll());
    #    $mem->set(0,$count,0,60*60);
    #}
    #else {
    #    $count = $data;
    #}
    return $count;
}

function postForumReply($user_id, $topic_id, $category_id, $message) {
    $PDO =&DB::$PDO;
    $sql = 'INSERT INTO forum_replies
                (user_id, category_id, topic_id, text, timestamp)
            VALUES
                (:user_id, :cat, :topic, :msg, now())';
    $stmt = $PDO->prepare($sql);
/*    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':cat', $category_id);
    $stmt->bindParam(':topic', $topic_id);
    $stmt->bindParam(':msg', $message);*/
    $stmt->execute(array(':user_id' => $user_id, ':cat' => $category_id, ':topic' => $topic_id, ':msg' => $message));
}

// Returns false if it is possible to add new topics in the category
function isForumCatLocked($category_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT locked FROM forum_categories WHERE locked = 1 AND id = :cat_id');
    $stmt->execute(array(':cat_id' => $category_id));
    if ($stmt->fetchAll()) {
        return true;
    } else {
        return false;
    }
}

function postForumTopic($user_id, $category_id, $topic, $message) {
    $PDO =&DB::$PDO;
    $sql = 'INSERT INTO forum_topics
                (user_id, category_id, topic, text, timestamp)
            VALUES
                (:user_id, :cat, :topic, :msg, now())';
    $stmt = $PDO->prepare($sql);
/*    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':cat', $category_id);
    $stmt->bindParam(':topic', $topic);
    $stmt->bindParam(':msg', $message);*/
    $stmt->execute(array(':user_id' => $user_id, ':cat' => $category_id, ':topic' => $topic, ':msg' => $message));
}

function getCategoryFromId($category_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT category FROM forum_categories WHERE id = :cat LIMIT 1');
    $stmt->execute(array(':cat' => $category_id));
    $row = $stmt->fetchAll();
    return $row[0]['category'];
}

function getTopicFromId($topic_id) {
    $PDO =&DB::$PDO;
    $stmt = $PDO->prepare('SELECT topic FROM forum_topics WHERE id = :topic LIMIT 1');
    $stmt->execute(array(':topic' => $forum_id));
    $row = $stmt->fetchAll();
    return $row[0]['topic'];
}

?>
