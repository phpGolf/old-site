<?php
if(!defined('INDEX')) {
    header('Location: /');
}
class Challenge {
    private $cid;
    private $name;
    private $open;
    private $active;
    private $type;
    private $output_type;
    private $disabled_func;
    private $constant;
    private $enddate;
    
    //Construct
    public function __construct($cid) {
        if(!isChallenge($cid)) {
            msg('Did not find challenge');
            return false;
        }
        $challenges = challenges(true);
        $challenge = $challenges[$cid];
        $this->cid = $cid;
        $this->name = $challenge['name'];
        $this->open = ($challenge['open']) ? true : false;
        $this->active = ($challenge['active']) ? true : false;
        $this->type = $challenge['type'];
        $this->output_type = $challenge['output_type'];
        $this->disabled_func = $challenge['disabled_func'];
        $this->constant = $challenge['constant'];
        $this->enddate = $challenge['enddate'];
        return true;
    }
    //Get Challenge id
    public function getId() {
        return $this->cid;
    }
    
    //Get Challenge name
    public function getName() {
        return $this->name;
    }
    
    //Set Challenge name
    // $name = Unused name
    public function setName($name) {
        if($name == $this->name) {
            return true;
        }
        if(isChallengeName($name)) {
            msg('Challenge name exists');
            return false;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET `name`=:name WHERE id=:cid');
        $pre->execute(array(':name' => $name, ':cid' => $this->cid));
        $this->name = $name;
        return true;
    }
    
    
    //Get status open
    public function getStatusOpen() {
        return $this->open;
    }
    
    //Set status open
    // $open = true/false
    public function setStatusOpen($open) {
        if($open == $this->open) {
            return true;
        }
        $this->open = ($open) ? true : false;
        $open = ($open) ? '1' : '0';
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET `open`=:open WHERE id=:cid');
        $pre->execute(array(':open' => $open, ':cid' => $this->cid));
        return true;
    }
    
    //Get status active
    public function getStatusActive() {
        return $this->active;
    }
    
    //Set status active
    // $active = true/false
    public function setStatusActive($active) {
        if($active == $this->active) {
            return true;
        }
        $this->active = ($active) ? true : false;
        $active = ($active) ? '1' : '0';
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET `active`=:active WHERE id=:cid');
        $pre->execute(array(':active' => $active, ':cid' => $this->cid));
        return true;
    }
    
    //Get type
    public function getType() {
        return $this->type;
    }
    
    //Set type
    // $type = Valid type
    public function setType($type) {
        if($type == $this->type) {
            return true;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET type=:type WHERE id=:cid');
        var_dump($pre->execute(array(':type' => $type, ':cid' => $this->cid)));
        $this->type = $type;
        return true;
    }
    
    //Get output type
    public function getOutputType() {
        return $this->output_type;
    }
    
    //Set output type
    // $type = Valid output type
    public function setOutputType($type) {
        if($type == $this->output_type) {
            return true;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET output_type=:output_type WHERE id=:cid');
        $pre->execute(array(':output_type' => $type, ':cid' => $this->cid));
        $this->output_type = $type;
        return true;
    }
    
    //Get disabled_func
    public function getDisabledFunc() {
        return $this->disabled_func;
    }
    
    //Set disabled_func
    public function setDisabledFunc($funcs) {
        if($funcs == $this->disabled_func) {
            return true;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET disabled_func=:disabled_func WHERE id=:cid');
        $pre->execute(array(':disabled_func' => $funcs, ':cid' => $this->cid));
        $this->disabled_func = $funcs;
        return true;
    }
    
    //Get constant
    public function getConstant() {
        return $this->constant;
    }
    
    //Set constant
    public function setConstant($constant) {
        if($constant == $this->constant) {
            return true;
        }
        if(preg_match('/^[a-zA-Z0-9]*$/',$constant) == 0) {
            msg('Constant contained others then letters and numbers!',0);
            return false;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET constant=:constant WHERE id=:cid');
        $pre->execute(array(':constant' => $constant, ':cid' => $this->cid));
        $this->constant = $constant;
        return true;
    }
    
    //Get enddate
    // $parsed = Get an array of parsed date
    public function getEnddate($parsed=false) {
        if($this->enddate) {
            if($parsed) {
                return date_parse($this->enddate);
            } else {
                return $this->enddate;
            }
        } else {
            return false;
        }
    }
    
    //Set enddate
    // $date = Valid DATE
    public function setEnddate($date) {
        $date = date_parse($date);
        $date = $date['year'].'-'.$date['month'].'-'.$date['day'];
        if($this->enddate == $date) {
            return true;
        }
        $this->enddate = $date;
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET enddate=:enddate WHERE id=:cid');
        $pre->execute(array(':enddate' => $date, ':cid' => $this->cid));
        return true;
    }
    
    //Remove enddate
    public function delEnddate() {
        if(!$this->enddate) {
            return true;
        }
        //Save
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('UPDATE challenges SET enddate=NULL WHERE id=:cid');
        $pre->execute(array(':cid' => $this->cid));
        $this->enddate = false;
        return true;
    }
}
