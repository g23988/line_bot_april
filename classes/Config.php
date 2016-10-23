<?php
//讀取設定檔
class Config{
	private $_Replytime;	
	private $_Config_string,$_Channel_ID,$_Channel_Secret,$_Channel_Token;
	private $_Redis_Sever,$_Redis_Port,$Redis_Timeout;
	private $_Config_path = __DIR__."/../conf/config.json";
	function __construct(){
		$this->_Config_string = json_decode(file_get_contents($this->_Config_path,FILE_USE_INCLUDE_PATH),true);
		$this->_Replytime = $this->_Config_string["system"]["Replytime"];
		$this->_Channel_ID = $this->_Config_string["line_bot_conf"]["Channel_ID"];
		$this->_Channel_Secret = $this->_Config_string["line_bot_conf"]["Channel_Secret"];
		$this->_Channel_Token = $this->_Config_string["line_bot_conf"]["Channel_Token"];
		$this->_Redis_Server = $this->_Config_string["redis"]["Server"];
		$this->_Redis_Port = $this->_Config_string["redis"]["Port"];
		$this->_Redis_Timeout = $this->_Config_string["redis"]["Timeout"];
	}
	//system setting
	function Replytime($Replytime=null){
		$this->_Replytime = $Replytime ?? $this->_Replytime;
		return $this->_Replytime;
	}
	//line bot setting
	function Channel_ID($channel_ID=null){
		$this->_Channel_ID = $channel_ID ?? $this->_Channel_ID;
		return $this->_Channel_ID;
	}
	function Channel_Secret($channel_Secret=null){
		$this->_Channel_Secret = $channel_Secret ?? $this->_Channel_Secret;
                return $this->_Channel_ID;	
	}
	function Channel_Token($channel_Token=null){
		$this->_Channel_Token = $channel_Token ?? $this->_Channel_Token;
		return $this->_Channel_Token;
	}
	//redis setting
	function Redis_Server($redis_Server=null){
		$this->_Redis_Server = $redis_Server ?? $this->_Redis_Server;
		return $this->_Redis_Server;
	}
	function Redis_Port($redis_Port=null){
		$this->_Redis_Port = $redis_Port ?? $this->_Redis_Port;
                return $this->_Redis_Port;
	}
	function Redis_Timeout($redis_Timeout=null){
		$this->_Redis_Timeout = $redis_Timeout ?? $this->_Redis_Timeout;
                return $this->_Redis_Timeout;
	}
	
	
}



?>
