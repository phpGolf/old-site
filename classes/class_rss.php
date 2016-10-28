<?php
class RSS {
    private $title;
    private $url;
    private $desc;
    private $entries = array();
    
    public function __construct($title,$url="http://www.phpgolf.org",$desc=false) {
        if(!$desc) {
            $this->desc = 'RSS feed for latest attempts on phpGolf.org';
        } else {
            $this->desc;
        }
        $this->title = $title;
        $this->url = $url;
    }
    
    
    public function addEntry($title,$desc,$url,$date,$guid=false) {
         $Entry['title'] = $title;
         $Entry['desc'] = $desc;
         $Entry['url'] = $url;
         $Entry['date'] = date(DateTime::RSS,strtotime($date));
         $Entry['guid'] = $guid;
         $this->entries[] = $Entry;
    }
    
    public function printRSS() {
        $DOM = new DOMdocument('1.0');
        $DOM->formatOutput=true;
        //Create RSS node
        $rss = $DOM->createElement('rss');
            $rss_version = $DOM->createAttribute('version');
            $rss_version->value="2.0";
            $rss->appendChild($rss_version);
        
        //Create channel node
        $channel = $DOM->createElement('channel');
        
        //Create title, desc and url nodes
        $channel->appendChild($DOM->createElement('title',$this->title));
        
        $desc = $DOM->createElement('description');
        $desc->appendChild($DOM->createTextNode($this->desc));
        $channel->appendChild($desc);
        
        $channel->appendChild($DOM->createElement('link',$this->url));

        //Date
        $channel->appendChild($DOM->createElement('pubDate',date(DateTime::RSS)));

        //Entries
        if(count($this->entries) != 0 ) {
            foreach($this->entries as $entry) {
                $item = $DOM->createElement('item');
                $item->appendChild($DOM->createElement('title',$entry['title']));
                $desc = $DOM->createElement('description');    
                $desc->appendChild($DOM->createTextNode($entry['desc']));
                $item->appendChild($desc);
                $item->appendChild($DOM->createElement('link',$entry['url']));
                if($entry['guid']) {
                    $item->appendChild($DOM->createElement('guid',$entry['guid']));
                }
                $item->appendChild($DOM->createElement('pubDate',$entry['date']));
                $channel->appendChild($item);
                unset($item);
            }

        }
        $rss->appendChild($channel);
        $DOM->appendChild($rss);
        
        echo $DOM->saveXML();
    }
    
    public function clear() {
        unset($this->entries);
        $this->entries = array();
    }
    
}
?>
