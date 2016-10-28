<?php
if(!defined('INDEX')) {
    header('location: /');
}
/** Memcache class **/
/* Made by AlecTBM fro phpgolf.org */
class cache {
    private $mem;
    private static $log = array();
    public $key;
    public function __construct() {
        $this->mem = new Memcache;
        $this->mem->connect(MEM_HOST,MEM_PORT);
    }
    
    public function get($key=false) {
        if(!$key) {
            $key = $this->key;
        }
        $this->keyHandling($key);
        
        $data = $this->mem->get($key);
        cache::log($key,$data);
        return ($data == NULL) ? false : $data;
    }
    
    public function set($key,$data,$args=0,$timeout=0) {
        if(!$key) {
            $key = $this->key;
        }
        $this->keyHandling($key);
        return $this->mem->set($key,$data,$args,$timeout);
    }
    
    public function replace($key,$data,$args=0,$timeout=0) {
        if(!$key) {
            $key = $this->key;
        }
        $this->keyHandling($key);
        return $this->mem->replace($key,$data,$args,$timeout);
    }
    
    public function flush() {
        return $this->mem->flush();
    }
    
    public function delete($key=false,$timeout=0) {
        if(!$key) {
            $key = $this->key;
        }
        $this->keyHandling($key);
        $this->mem->delete($key,$timeout);
    }
    
    public function close() {
        $this->mem->close();
    }
    
    public function __destruct() {
        $this->mem->close();
    }
    
    //Key handling
    private function keyHandling(&$key) {
        if($key == 'Keys') {
            return true;
        }
        $key2 = $key;
        //Couse of different timezones, one cache with time is not working.
        $key = date_default_timezone_get().'-'.$key;
        $key = 'TEST_'.$key; // EXTRA WHEN TESTING //
        return true;
    }
    // Logging
    static private function log($key,$data) {
        cache::$log[$key] = $data;
    }
    
    public function getLog() {
        return cache::$log;
    }
}
?>
