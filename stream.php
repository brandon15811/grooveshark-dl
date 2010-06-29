<?php
#session_start();
include "newexec.php";
header('Content-Disposition: audio/mpeg; filename=' . $songInfo["result"]["song"]["artistName"]. " - " . $songInfo["result"]["song"]["songName"] . '.mp3');
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
$streamData = json_decode(getStreamURL($_GET["songid"]), true);
$urlg = $streamData["result"]["url"];
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
	    "icy-metadata: 1",
	    "transferMode.dlna.org: Streaming",
	    "User-Agent: GStreamer souphttpsrc libsoup/2.25.4"
	
        );
        $gh = curl_init();
        curl_setopt($gh, CURLOPT_URL,$urlg);
        curl_setopt($gh, CURLOPT_TIMEOUT, 60);
        curl_setopt($gh, CURLOPT_HTTPHEADER, $headersg);
        #curl_setopt($gh, CURLOPT_POST, 1);
	if (strpos($urlg, "akm") !== false) {
     	  curl_setopt($gh, CURLOPT_HEADER, 1);
	  curl_setopt($gh, CURLOPT_RETURNTRANSFER, 1);
	}
	/*$header = http_parse_headers($myvar);
  	$location = $header['Location'];*/
if (strpos($urlg, "akm") !== false) {
  #echo "hello";
  $myvar = curl_exec($gh);
  curl_close($gh);
  $header = http_parse_headers($myvar);
  $location = $header['Location'];
  header('Location: '.$location);
} else {
  #echo "Before curl_exec:".memory_get_usage()."\\\n";
  echo curl_exec($gh);
  curl_close($gh);
}
        
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST']."/timer.php?session=".$sessionid."&key=".$streamKey."&serverid=".$serverID);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$status = curl_exec($ch);
curl_close($ch);
?>
