<?php
if(!defined('INDEX')) {
    header('location: /');
}

class Twitter {
    private $token = '';
    private $token_secret = '';
    private $consumer_key = '';
    private $consumer_secret = '';
    private $baseurl = 'https://api.twitter.com/1/statuses/';
    private $post = array();
    private $url = '';
    
    public function __construct() {
        
    }
    
    //Returns signature
    // $auth = array of auth parameters
    // return = signature (is also added to $auth automaticly);
    private function makeSignature(array &$auth) {
        if(empty($this->url)) {
            return false;
        }
        $sign_arr = array();
        foreach($auth as $parm => $value) {
            $sign_arr[] = $parm.'='.rawurlencode($value);
        }
        if(count($this->post) > 0) {
            foreach($this->post as $parm => $value) {
                $sign_arr[] = $parm.'='.rawurlencode($value);
            }
        }
        sort($sign_arr);
        $sign_str = 'POST&'.rawurlencode($this->url).'&';
        $sign_str .= rawurlencode(implode('&',$sign_arr));
        
        $sign_key = $this->consumer_secret.'&'.$this->token_secret;
        $sign = base64_encode(hash_hmac('sha1', $sign_str, $sign_key, true));
        $auth['oauth_signature'] = $sign;
        return $sign;
    }
    
    private function makeRequest($method) {
        switch($method) {
            case 'update':
                $this->url = $this->baseurl.'update.json';
                $http_method = 'POST';
                break;
            case 'delete':
                if(empty($this->post['id'])) {
                    return false;
                }
                $this->url = $this->baseurl.'destroy/'.$this->post['id'].'.json';
                $http_method = 'POST';
                break;
            default:
                return false;
                break;
        }
        //Make auth array
        $auth['oauth_consumer_key'] = $this->consumer_key;
        $auth['oauth_nonce'] = substr(sha1(time().rand(0,100)),0,10);
        $auth['oauth_signature_method'] = 'HMAC-SHA1';
        $auth['oauth_timestamp'] = time();
        $auth['oauth_token'] = $this->token;

        //Make siganture
        $this->makeSignature($auth);

        //Make auth string
        $auth_str = 'OAuth ';
        $first = true;
        foreach($auth as $parm => $value) {
            if(!$first) {
                $auth_str .= ', ';
            }
            $auth_str .= $parm.'="'.rawurlencode($value).'"';
            $first = false;
        }
        
        //Make post string
        if(count($this->post) >0) {
            $post_str = '';
            $first=true;
            foreach($this->post as $parm => $value) {
                if(!$first) {
                    $post_str .= '&';
                }
                $post_str .= $parm.'='.rawurlencode($value).'';
                $first = false;
            }
        }
        
        //Make curl object
        $curl = curl_init($this->url);
        //Return transfer
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        //HTTP Headers
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: '.$auth_str,'Expect:'));
        //POST method
        if($http_method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
        }
        //GET method
        if($http_method == 'GET') {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }
        //Add postfields
        if(count($this->post) >0) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_str);
        }
        $respons = json_decode(curl_exec($curl));
        $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        
        $return['code'] = $http_code;
        
        if($method == 'update' && $http_code == 200) {
            $return['postid'] = $respons->id_str;
        }
        if($http_code != 200) {
            $return['error'] = $respons->error;
        }
        return $return;
    }
    
    //Post tweet
    // $post = string with the post, max 140 chars;
    public function post($post) {
        if(strlen($post)>140) { 
            return false;
        }
        $this->post['status'] = $post;
        return $this->makeRequest('update');
    }
    
    //Delete tweet
    // $id = id for the post to delete
    public function delete($id) {
        if(!is_numeric($id)) { 
            return false;
        }
        $this->post['id'] = $id;
        return $this->makeRequest('delete');
    }
}
