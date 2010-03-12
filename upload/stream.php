<?php
#echo "Before Include:".memory_get_usage()."\\\n";
include "exec.php";
#echo "After Include:".memory_get_usage()."\\\n";
header('Content-Type: audio/mpeg');
/*function http_parse_header( $header )
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
    }*/
#$streamjsonurl = streamkey($_GET["songid"]);
$streamdata = json_decode(streamkey($_GET["songid"]), true);
#$buttonurl = $streamdata[result][result][streamServer];
#$buttonkey = $streamdata[result][result][streamKey];	
$urlg = "http://".$streamdata[result][result][streamServer]."/stream.php";
#$pageg = "stream.php";
$postjsong = "streamKey=".$streamdata[result][result][streamKey];;
        $headersg = array(
            "POST stream.php HTTP/1.1",
            "Content-length: ".strlen($postjsong),
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
	
        );
        $gh = curl_init();
	#echo "$urlg"."?".$postjsong;
        curl_setopt($gh, CURLOPT_URL,$urlg);
        #curl_setopt($gh, CURLOPT_RETURNTRANSFER, 1);
	#curl_setopt($gh, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($gh, CURLOPT_TIMEOUT, 60);
        curl_setopt($gh, CURLOPT_HTTPHEADER, $headersg);
        #curl_setopt($gh, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        // Apply the XML to our curl call
        curl_setopt($gh, CURLOPT_POST, 1);
        curl_setopt($gh, CURLOPT_POSTFIELDS, $postjsong);
	if (strpos($urlg, "akm") !== false) {
     	  curl_setopt($gh, CURLOPT_HEADER, 1);
	  curl_setopt($gh, CURLOPT_RETURNTRANSFER, 1);
	}

	#echo "Before curl_exec:".memory_get_usage()."\\\n";
        #curl_exec($gh);
	#echo "After curl_exec:".memory_get_usage()."\\\n";
	#curl_close($gh);
	#echo $myvar;
	#$myvardelete = substr($myvar, 0, (stripos($myvar, "\r\n\r\n")));
	#$myvarr = str_replace($myvardelete, '', $myvar);
	$header = http_parse_headers($myvar);
  	$location = $header['Location'];
  #echo "Before Delete:".memory_get_usage()."\\\n";
  #myvardelete = substr($myvar, 0, (stripos($myvar, "\r\n\r\n")));
  #echo "After Delete and Before Replace:".memory_get_usage()."\\\n";
  #$myvar = str_replace($myvardelete, '', $myvar);
  #echo "After Replace:".memory_get_usage()."\\\n";
if (strpos($urlg, "akm") !== false) {
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
	#echo "<br><br>";
	#var_dump($location);
        

?>
