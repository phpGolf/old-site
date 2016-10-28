<?php
if(!defined('INDEX')) {
    header('location: /');
}
class LogPDO extends PDO {
    static private $logs = array();
	public function log($Query,$Time,$Type) {
		LogPDO::$logs[] = array('Type' => $Type,'Query' => $Query,'Time' => $Time);
	}
	public function query($Query) {
		$Start = microtime(true);
		$Result = parent::query($Query);
		$Time = microtime(true) - $Start;
		$this->log($Query,$Time * 1000,'Query');
		if(parent::errorCode() != 00000 && access('debug')) {
		    sqlError(parent::errorInfo());
		    return parent::errorInfo();
		}
		return $Result;
	}
	
	public function exec($Query) {
		$Start = microtime(true);
		$Result = parent::exec($Query);
		$Time = microtime(true) - $Start;
		$this->log($Query,$Time * 1000,'Exec');
		if(parent::errorCode() != 00000 && access('debug')) {
		    sqlError(parent::errorInfo());
		    return parent::errorInfo();
		}
		return $Result;
	}
	
	public function prepare($Query) {
		$Prepare = parent::prepare($Query);
		if(parent::errorCode() != 00000 && access('debug')) {
		    sqlError(parent::errorInfo());
		    return parent::errorInfo();
		}
		if($Prepare) {
			return new LogPDOStatement($Prepare);
		} else {
			return false;
		}
	}
	
	static public function getLog() {
	    return LogPDO::$logs;
	}
}

class LogPDOStatement {
	private $Statement;
	public function __construct(PDOStatement $Statement) {
		$this->Statement = $Statement;
		unset($this->Parameters);
	}
	
	public function execute(array $Parameters = array()) {
		$Key = key($Parameters);
		$Numeric = is_numeric($Key);
		$Query = $this->Statement->queryString;
		$Start = microtime(true);
		if(count($Parameters) != 0) {
			$Result = $this->Statement->execute($Parameters);
		} else {
			$Result = $this->Statement->execute();
		}
		$Time = microtime(true) - $Start;
		if($this->Statement->errorCode() != 00000 && access('debug')) {
		    sqlError($this->Statement->errorInfo());
		}
		if(!$Numeric && $Result) {
			LogPDO::log(str_replace(array_keys($Parameters),$Parameters,$Query),round($Time * 1000, 3),'Prepared');
		} elseif($Result) {
			$this->i = 0;
			$this->Parameters = $newParameters;
			$Query = preg_replace_callback('/[^A-Za-z0-9\?]\?[^A-Za-z0-9\?]/',array(&$this,'changeKeys'),$Query);
			unset($this->Parameters);
			unset($this->i);
			LogPDO::log($Query,round($Time * 1000, 3),'Prepared');
		}
		return $Result;
	}
	
	public function __call($Function_name, $Parameters) {
		return call_user_func_array(array($this->Statement, $Function_name), $Parameters);
	}
	
	public function changeKeys($Match) {
		return '='.$this->Parameters[$this->i++].' ';
	}
}
?>
