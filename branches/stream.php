<?php
session_start();
//include "newexec.php";
/*if($_GET['dl'] == 1) {
	$songInfo = json_decode(getSongInfo($_GET["songid"]), true);
	header('Content-Disposition: audio/mpeg; filename=' . $songInfo["result"]["song"]["artistName"]. " - " . $songInfo["result"]["song"]["songName"] . '.mp3');
} else {*/
	//header('Content-Type: audio/mpeg');
//}
if (!function_exists('http_parse_headers')) {
function http_parse_headers( $header )
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }
}
function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        mt_rand( 0, 0xffff ),

        mt_rand( 0, 0x0fff ) | 0x4000,

        mt_rand( 0, 0x3fff ) | 0x8000,

        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
function randomstring() {
    $length = 6;
    $characters = '1234567890abcdef';
    $string = "";    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

	if ( strlen($string) == 6 ) {
		return $string;
	} else {
		return randomstring();
	}
}
//$songInfo = json_decode(getSongInfo($_GET["songid"]), true);
//echo $songInfo["result"]["song"]["artistName"]. " - " . $songInfo["result"]["song"]["songName"];
/*
Get Session
*/
$homesock = fsockopen("listen.grooveshark.com", 80, $errno, $errstr, 30);
if (!$homesock) {
    //echo "$errstr ($errno)<br />\n";
} else {
    /*$out = "POST /more.php HTTP/1.1\r\n";
	$out .= "Accept-Encoding: identity\r\n";
	$out .= "Content-Length: 372\r\n";
	$out .= "Host: cowbell.grooveshark.com\r\n";
	$out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12 (.NET CLR 3.5.30729)\r\n";
	$out .= "Connection: close\r\n";
	$out .= "Referer: http://listen.grooveshark.com/main.swf?cowbell=fe87233106a6cef919a1294fb2c3c05f\r\n";
	$out .= "Content-Type: application/json\r\n";*/
	$out = "GET / HTTP/1.1\r\n";
	$out .= "Accept-Encoding: identity\r\n";
	$out .= "Host: listen.grooveshark.com\r\n";
	$out .= "Connection: close\r\n";
	$out .= "User-Agent: Python-urllib/2.5\r\n\r\n";
    fwrite($homesock, $out);
    while (!feof($homesock)) {
        $gspage .= fgets($homesock, 4096);
    }
    fclose($homesock); 
}
//echo htmlentities($gspage);
/*$re1='.*?';	# Non-greedy match on filler
$re2='("sessionID")';	# Double Quote String 1
$re3='(:)';	# Any Single Character 1
$re4='(".*?")';	# Double Quote String 2
$sessionIDa = preg_match("/".$re1.$re2.$re3.$re4."/is", $gspage, $matches);*/
  $re1='.*?';	# Non-greedy match on filler
  $re2='\\{.*?\\}';	# Uninteresting: cbraces
  $re3='.*?';	# Non-greedy match on filler
  $re4='\\{.*?\\}';	# Uninteresting: cbraces
  $re5='.*?';	# Non-greedy match on filler
  $re6='\\{.*?\\}';	# Uninteresting: cbraces
  $re7='.*?';	# Non-greedy match on filler
  $re8='(\\{.*?\\})';	# Curly Braces 1
  $re9='(,)';	# Any Single Character 1
  $re10='("country")';	# Double Quote String 1
  $re11='(:)';	# Any Single Character 2
  $re12='(\\{.*?\\})';	# Curly Braces 2
  $re13='(,)';	# Any Single Character 3
  $re14='(".*?")';	# Double Quote String 2
  $re15='(:)';	# Any Single Character 4
  $re16='((?:[a-z][a-z]+))';	# Word 1
  $re17='(,)';	# Any Single Character 5
  $re18='(".*?")';	# Double Quote String 3
  $re19='(:)';	# Any Single Character 6
  $re20='((?:[a-z][a-z]+))';	# Word 2
  $re21='(,)';	# Any Single Character 7
  $re22='(".*?")';	# Double Quote String 4
  $re23='(:)';	# Any Single Character 8
  $re24='(".*?")';	# Double Quote String 5
  $re25='(\\})';	# Any Single Character 9
$session = preg_match("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7.$re8.$re9.$re10.$re11.$re12.$re13.$re14.$re15.$re16.$re17.$re18.$re19.$re20.$re21.$re22.$re23.$re24.$re25."/is", $gspage, $matches);
//echo "<pre>";
array_shift($matches);
$matches = implode($matches);
//echo $session;
//echo htmlentities(print_r($matches, true));
$jsona = json_decode($matches, true);
//var_dump($jsona);
$sessionID = $jsona['sessionID'];
//$sessionID = "45bf11d6c86e019aa967d36b3075139d";
//echo $sessionID;
/*
Get Token
*/
//exit;
$tokenjsona = array ( 'header' => array ( 'session' => $sessionID, 'client' => 'gslite', 'clientRevision' => '20101012.37', 'uuid' => uuid(), ), 'privacy' => 1, 'method' => 'getCommunicationToken', 'parameters' => array ( 'secretKey' => md5($sessionID), ), 'country' => array ( 'CC4' => '2147483648', 'CC1' => '0', 'CC3' => '0', 'CC2' => '0', 'IPR' => '1021', 'ID' => '223', ), );
$tokenjson = json_encode($tokenjsona);
//echo $tokenjson;
//$tokensock = fsockopen("cowbell.grooveshark.com", 80, $errno, $errstr, 30);
if (!$tokensock) {
    //echo "$errstr ($errno)<br />\n";
} else {
    $out = "POST /more.php HTTP/1.1\r\n";
	$out .= "Accept-Encoding: identity\r\n";
	$out .= "Content-Length: ".strlen($tokenjson)."\r\n";
	$out .= "Host: cowbell.grooveshark.com\r\n";
	$out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12 (.NET CLR 3.5.30729)\r\n";
	$out .= "Connection: close\r\n";
	$out .= "Referer: http://listen.grooveshark.com/main.swf?cowbell=fe87233106a6cef919a1294fb2c3c05f\r\n";
	$out .= "Content-Type: application/json\r\n\r\n";
	$out .= $tokenjson."\r\n\r\n";
    fwrite($tokensock, $out);
    while (!feof($tokensock)) {
        $tokendata .= fgets($tokensock, 4096);
    }
    fclose($tokensock);
}
		//echo $tokendata;
		$token = strstr($tokendata, "\r\n\r\n");
		$token = json_decode($token, true);
		$token = $token['result'];
		$token = preg_replace("/[^a-zA-Z0-9\s]/", "", $token);
		//$token = "4d73141417bc9";
		var_dump($token);
		echo "<br>";
/*
Stream Key
*/
$randomchars = randomstring();
//$randomchars = "58bba0";
$randomchars = preg_replace("/[^a-zA-Z0-9\s]/", "", randomstring());
$streamtoken = "getStreamKeyFromSongIDEx".":".$token.":quitStealinMahShit:".$randomchars;
$streamtokensha1 = $randomchars.sha1($streamtoken);
$streamtokenn = $randomchars."getStreamKeyFromSongIDEx".":".$token.":quitStealinMahShit:".$randomchars;
var_dump($streamtoken);
echo "<br>";
var_dump($streamtokenn);
echo "<br>";
var_dump($streamtokensha1);
echo "<br>";
//echo $streamtoken;
//exit;
$streamjsona = array ( 'header' => array ( 'session' => $sessionID, 'token' => $streamtokensha1, 'client' => 'gslite', 'clientRevision' => '20101012.37', 'uuid' => uuid(), ), 'privacy' => 1, 'method' => 'getStreamKeyFromSongIDEx', 'parameters' => array ( 'mobile' => false, 'country' => array ( 'CC4' => '2147483648', 'CC1' => '0', 'CC3' => '0', 'CC2' => '0', 'IPR' => '1021', 'ID' => '223', ), 'songID' => $_GET['songid'], 'prefetch' => false, ), 'country' => array ( 'CC4' => '2147483648', 'CC1' => '0', 'CC3' => '0', 'CC2' => '0', 'IPR' => '1021', 'ID' => '223', ), );
$streamjson = json_encode($streamjsona);
echo $streamjson."<br>";
exit;
$streamsock = fsockopen("cowbell.grooveshark.com", 80, $errno, $errstr, 30);
if (!$streamsock) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "POST /more.php?getStreamKeyFromSongIDEx HTTP/1.1\r\n";
	$out .= "Accept-Encoding: identity\r\n";
	$out .= "Content-Length: ".strlen($streamjson)."\r\n";
	$out .= "Host: cowbell.grooveshark.com\r\n";
	$out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12 (.NET CLR 3.5.30729)\r\n";
	$out .= "Connection: close\r\n";
	$out .= "Referer: http://listen.grooveshark.com/main.swf?cowbell=fe87233106a6cef919a1294fb2c3c05f\r\n";
	$out .= "Content-Type: application/json\r\n\r\n";
	$out .= $streamjson."\r\n\r\n";
    fwrite($streamsock, $out);
    while (!feof($streamsock)) {
        $streamdata .= fgets($streamsock, 4096);
    }
    fclose($streamsock);
	echo str_replace("\r\n", "<br>", $streamdata);
}
/*//$streamData = json_decode(getStreamURL($_GET["songid"]), true);
//$urlg = $streamData["result"]["url"];
#echo $urlg;
$streamHosta = parse_url($urlg);
$streamHost = $streamHosta["host"];
$urlParams = "/streamKey=".$streamData["result"]["streamKey"];
$streamKey = $streamData["result"]["streamKey"];
$serverID = $streamData["result"]["streamServerID"];
        $headersg = array(
            "GET ".$urlParams." HTTP/1.1",
	    "Host: ".$streamHost,
	    "Connection: close",
	
        );
        $gh = curl_init();
        curl_setopt($gh, CURLOPT_URL,$urlg);
        curl_setopt($gh, CURLOPT_HTTPHEADER, $headersg);
        #curl_setopt($gh, CURLOPT_POST, 1);
     	//curl_setopt($gh, CURLOPT_HEADER, 1);
		curl_setopt($gh, CURLOPT_RETURNTRANSFER, 1);
	
	/*$header = http_parse_headers($myvar);
  	$location = $header['Location'];*/
/*if (strpos($urlg, "akm") !== false) {
  #echo "hello";
  $myvar = curl_exec($gh);
  curl_close($gh);
  $header = http_parse_headers($myvar);
  $location = $header['Location'];
  $lg = curl_init();
  curl_setopt($lg, CURLOPT_URL, $location);
  //curl_setopt($lg, CURLOPT_TIMEOUT, 60);
  //curl_setopt($lg, CURLOPT_HEADER, 1);
  //curl_setopt($lg, CURLOPT_RETURNTRANSFER, 1);
  $stream = curl_exec($lg);
  //header('Content-Length: '.strlen($stream));
  echo $stream;
  curl_close($lg);

} else {
  #echo "Before curl_exec:".memory_get_usage()."\\\n";
  $stream = curl_exec($gh);
  strlen($stream);
  header('Content-Length: '.strlen($stream));
  echo $stream;
  curl_close($gh);
}
/*$file1 = str_replace("/stream.php", "", $_SERVER['PHP_SELF']);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST'].$file1."/timer.php?session=".$sessionid."&key=".$streamKey."&serverid=".$serverID);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$status = curl_exec($ch);
curl_close($ch);*/
?>