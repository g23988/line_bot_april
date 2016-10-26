<?php
class Dialogue{
	private $_type;
	private $_message;
	private $_textMessageBuilder;
	
	//處理特殊事件 圖 or 影
	private $_config;
	private $_redis;	


	function __construct($object){
		$this->_config = new Config();
		$this->_message = $object["message"];
		$this->_redis = new Redis();
		$this->_redis->connect($this->_config->Redis_Server(), $this->_config->Redis_Port(), $this->_config->Redis_Timeout());
		$this->_process();
	}
	private function _process(){
		$type = $this->_message["type"];
		switch ($type){
			case "sticker":
				$this->textMessageBuilder = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(3,rand(180,250));
				break;
			case "image":
				//$this->textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('恩好，馬上幫您存起來');
				//拉圖任務轉交
				$this->_redis->rPush('imgq', $this->_message["id"]);
				break;
			case "text":
				if(preg_match("/^艾波|^波波/i",$this->_message["text"])){
					$april_brain = new Brain($this->_message["text"]);					
					$this->textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($april_brain->echo());
				}
				else{
					$this->textMessageBuilder = null;
				}
				break;
/*			default:
				$this->textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Hi');
				break;*/
		}
	}
	function echo(){
		return $this->textMessageBuilder;
	}
	function __destruct(){
		$this->_redis->close();
	}
}



?>
