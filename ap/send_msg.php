<?php
require_once __DIR__.'/../classes/Config.php';
require_once __DIR__.'/../vendor/autoload.php';
include __DIR__.'/../classes/Dialogue.php';
declare(ticks = 1);

$config = new Config();

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($config->Channel_Token());
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $config->Channel_Secret()]);
//$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');

$redis = new Redis();
$redis->connect($config->Redis_Server(), $config->Redis_Port(), $config->Redis_Timeout());
while (true){
	$msg = $redis->lPop('msgq');
	$img = $redis->lPop('imgq');
	if ($msg){
	        $json_array = json_decode($msg,true);
		$dialogue = new Dialogue($json_array["events"][0]["message"]);
	        $response = $bot->pushMessage($json_array["events"][0]["source"]["userId"], $dialogue->echo());
	}
	else if ($img){
		$response = $bot->getMessageContent($img);
	//	var_dump($response);
		if ($response->isSucceeded()) {
			$tempfile = tmpfile();
//			fwrite($tempfile, $response->getRawBody());
			$imagefile = fopen("/mnt/linebotupload/images/".$img.".jpg","w+");
	                fwrite($imagefile, $response->getRawBody());
        	        fclose($imagefile);
		} 
	}
	else{
                usleep($config->Replytime());
        }
}
$redis->close();




//抓住強制終止訊號
function sig_handler($signo)
{
     switch ($signo) {
         case SIGTERM:
             // gestion de l'extinction
                echo "what?\n";
                $redis->close();
             exit;
             break;
         case SIGHUP:
                echo "wtf\n";
                $redis->close();
             // gestion du redémarrage
             break;
         case SIGUSR1:
                $redis->close();
             echo "wtf man\n";
             break;
         default:
                $redis->close();
                break;
             // gestion des autres signaux
     }

}

pcntl_signal(SIGUSR1, "sig_handler");
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");

?>
