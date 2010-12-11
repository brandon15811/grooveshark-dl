<?php
	sleep(30);
	$sessionid = $_GET['session'];
	$key = $_GET['key'];
	$serverID = $_GET['serverid'];
#?session=".$sessionid."&key=".$streamKey."&serverid=".$serverID

	$jsonarray = array ( 'header' => array ( 'sessionID' => $sessionid, ), 'method' => 'song.setPlaybackReached30Seconds', 'parameters' => array ( 'streamKey' => $key, 'streamServerID' => $serverID, ), );
	$postjson = str_replace("]", "}", str_replace("[", "{", json_encode($jsonarray)));
	echo $postjson;
	$url = "http://api.grooveshark.com/ws/1.0/?json";
	//$url = "http://groovedl.pcriot.com/bla.php";
        $headers = array(
        "POST /ws/1.0/?json HTTP/1.0",
	"Host: api.grooveshark.com",
        "User-Agent: Twisted PageGetter",
	"Content-Length: ".strlen($postjson),
    	"Content-type: text/json",
	"connection: close"
        );

	/*$headers = array(
        "POST /bla2.php HTTP/1.0",
	"Host: groovedl.pcriot.com",
        "User-Agent: Twisted PageGetter",
	"Content-Length: ".strlen($postjson),
    	"Content-type: text/json",
	"connection: close"
        );*/

        $ph = curl_init();
        curl_setopt($ph, CURLOPT_URL,$url);
        curl_setopt($ph, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ph, CURLOPT_TIMEOUT, 60);
        curl_setopt($ph, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ph, CURLOPT_POST, 1);
        curl_setopt($ph, CURLOPT_POSTFIELDS, $postjson);
        $jsondata = curl_exec($ph);
	#echo $jsondata;

?>
