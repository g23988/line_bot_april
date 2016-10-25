<?php
class Brain{
	private $_config;
	private $_message;
	private $_return_message;
	private $_elk;
	function __construct($message){
		$this->_config = new Config();
		$this->_message = $message;
		$this->_elk = new Elk($this->_config->Elk_Server(),$this->_config->Elk_Port(),$this->_config->Elk_Index(),$this->_config->Elk_Type());
		$this->_process();
	}
	
	private function _process(){
		$this->_return_message = $this->_elk->question($this->_message);
	}

	
	function echo(){
		return $this->_return_message;
	}



}


class Elk{
	private $_search_url;
	function __construct($elk_Host,$elk_Port,$elk_Index,$elk_Type){
                $this->_search_url = "http://".$elk_Host.":".$elk_Port."/".$elk_Index."/".$elk_Type."/_search";
        }
	function question($message){
		$message = preg_replace('/^艾波|^波波/', '', $message);
		$match_text["query"]["match"]["question"] = $message;
		$result = $this->_sentQuery(json_encode($match_text,JSON_UNESCAPED_UNICODE));
		$back_array = $this->_getQuery($result);
		$back_message = $this->_think($back_array);
		return $back_message;
	}
	private function _sentQuery($json_string){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_search_url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $json_string ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	private function _getQuery($json_string){
		$all_result = json_decode($json_string,true);
		return $all_result;
	}
	private function _think($back_array){
		//$count = $back_array["_shards"]["total"];
		//var_dump($back_array["hits"]["hits"]);
		$wait_choice_messages = array();
		foreach($back_array["hits"]["hits"] as $message_item){
			if ($message_item["_score"]>=0.7){
				array_push($wait_choice_messages,$message_item["_source"]["answer"]);
			}
		}
		if(count($wait_choice_messages)!=0){
			return $wait_choice_messages[array_rand($wait_choice_messages)];
		}else{
			return "我不會這個問題，我去問問在回覆給您。";
		}
	}
}


?>
