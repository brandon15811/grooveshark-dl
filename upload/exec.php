<?php
	$urle = "http://listen.grooveshark.com/";
	$useragente = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.7) Gecko/20100106 Ubuntu/9.10 (karmic) Firefox/3.5.7";
        $eh = curl_init();
        curl_setopt($eh, CURLOPT_URL,$urle);
        curl_setopt($eh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($eh, CURLOPT_TIMEOUT, 60);
        curl_setopt($eh, CURLOPT_USERAGENT, $useragente);
        $pagedata = curl_exec($eh);
// Functions
function csv_explode($delim=',', $str, $enclose='"', $preserve=false){
  $resArr = array();
  $n = 0;
  $expEncArr = explode($enclose, $str);
  foreach($expEncArr as $EncItem){
    if($n++%2){
      array_push($resArr, array_pop($resArr) . ($preserve?$enclose:'') . $EncItem.($preserve?$enclose:''));
    }else{
      $expDelArr = explode($delim, $EncItem);
      array_push($resArr, array_pop($resArr) . array_shift($expDelArr));
      $resArr = array_merge($resArr, $expDelArr);
    }
  }
  return $resArr;
} 
$csvfull = stristr($pagedata, "sessionID: ");
$csv = csv_explode(",", $csvfull);
$newcsv = preg_grep("/session/", $csv);
$sessionold = $newcsv[0];
$reparray = array("sessionID: '", "'");
$session = str_replace($reparray, "", $sessionold);

function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        mt_rand( 0, 0xffff ),

        mt_rand( 0, 0x0fff ) | 0x4000,

        mt_rand( 0, 0x3fff ) | 0x8000,

        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
$uuid = uuid();
function randomstring() {
    $length = 6;
    $characters = '1234567890abcdef';
    unset($string);
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

$jsons = '{"header": {"session": "ff97930c030cfec038da67ffefef5bfd", "client": "gslite", "clientRevision": "20100211.13", "uuid": "10fe28a7-a932-41cc-a4df-eef0083af788"}, "method": "getCommunicationToken", "parameters": {"secretKey": "f06dbce142851e05a4e59c11cebee9ba"}}';
$json = json_decode($jsons, true);
$secretkey = md5($session);
$jsona = array ( 'header' => array ( 'session' => $session, 'client' => 'gslite', 'clientRevision' => '20100211.16', 'uuid' => $uuid, ), 'method' => 'getCommunicationToken', 'parameters' => array ( 'secretKey' => $secretkey, ), );
#$jsonstreama = array ( 'header' => array ( 'session' => '0e58504cd32877e33a577b64a34a591a', 'token' => '8c986376701aa16165ba3e67eaf2e947fdf9a9142384d7', 'client' => 'gslite', 'clientRevision' => '20100211.13', 'uuid' => 'cf1b7fcd-c34c-480a-89b3-9ff495324e22', ), 'method' => 'getStreamKeyFromSongID', 'parameters' => array ( 'songID' => '22992605', 'prefetch' => false, ), );
$jsonnew = json_encode($jsona);
#echo $jsonnew;
	$url = "https://cowbell.grooveshark.com/service.php";
        $page = "/service.php";
        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Accept-Encoding: identity",
            "Content-length: ".strlen($jsonnew),
            "Host: cowbell.grooveshark.com",
            "Content-type: application/json",
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonnew);
        $ctokendata = curl_exec($ch);
	#echo $ctokendata;
	$ctokenj = json_decode($ctokendata, true);
	$ctoken = $ctokenj[result];
$randomchars = randomstring();
// Token Gen
$streamtoken = "getStreamKeyFromSongID".":".$ctoken.":theColorIsRed:".$randomchars;
$streamtokensha1 = $randomchars.sha1($streamtoken);
$listtoken = "playlistGetSongs".":".$ctoken.":theColorIsRed:".$randomchars;
$listtokensha1 = $randomchars.sha1($listtoken);
$searchtoken = "getSearchResults".":".$ctoken.":theColorIsRed:".$randomchars;
$searchtokensha1 = $randomchars.sha1($searchtoken);
$populartoken = "popularGetSongs".":".$ctoken.":theColorIsRed:".$randomchars;
$populartokensha1 = $randomchars.sha1($populartoken);
#echo "StreamRequest: $getStreamKeyFromSongID";
#echo "<br>";
#echo "ListRequest: $playlistGetSongs";
/*$jsonstreamreq = json_decode($getStreamKeyFromSongID, true);
$streamtoken = $jsonstreamreq[header][token];
$streamsession = $jsonstreamreq[header][session];
$songID = "23013459";
echo $streamtoken;
echo "<br><br>";
echo $streamsession;
echo "<br><br>";*/
function streamkey($songID)
{
	global $session, $streamtokensha1, $uuid;
	$jsonstreama = array ( 'header' => array ( 'session' => $session, 'token' => $streamtokensha1, 'client' => 'gslite', 'clientRevision' => '20100211.16', 'uuid' => $uuid, ), 'method' => 'getStreamKeyFromSongID', 'parameters' => array ( 'songID' => $songID, 'prefetch' => false, ), );
$jsonstreampost = json_encode($jsonstreama);
	$urld = "http://cowbell.grooveshark.com/more.php?getStreamKeyFromSongID";
        $paged = "/more.php?getStreamKeyFromSongID";
        $headersd = array(
            "POST ".$paged." HTTP/1.0",
            "Accept-Encoding: identity",
            "Content-length: ".strlen($jsonstreampost),
            "Host: cowbell.grooveshark.com",
            "Content-type: application/json",
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
        );
        $dh = curl_init();
        curl_setopt($dh, CURLOPT_URL,$urld);
        curl_setopt($dh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($dh, CURLOPT_TIMEOUT, 60);
        curl_setopt($dh, CURLOPT_HTTPHEADER, $headersd);
        #curl_setopt($dh, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($dh, CURLOPT_POST, 1);
        curl_setopt($dh, CURLOPT_POSTFIELDS, $jsonstreampost);
        $keydata = curl_exec($dh);
	return $keydata;
}
function playlist($playlistID)
{
	global $session, $listtokensha1, $uuid;
	#Get Playlist
	#$playlistID = "25353532";
	#$playlistID = $_GET['playlist'];
	$listjsona = array ( 'header' => array ( 'session' => $session, 'token' => $listtokensha1, 'client' => 'gslite', 'clientRevision' => '20100211.16', 'uuid' => $uuid, ), 'method' => 'playlistGetSongs', 'parameters' => array ( 'playlistID' => $playlistID, ), );
	$listjsonpost = json_encode($listjsona);
	$url = "http://cowbell.grooveshark.com/more.php?playlistGetSongs";
        $page = "/more.php?playlistGetSongs";
        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Accept-Encoding: identity",
            "Content-length: ".strlen($listjsonpost),
            "Host: cowbell.grooveshark.com",
            "Content-type: application/json",
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $listjsonpost);
        $listdata = curl_exec($ch);
        return $listdata;
}
function search($query)
{	
	global $session, $searchtokensha1, $uuid;
	$searchjsona = array ( 'header' => array ( 'clientRevision' => '20100211.16', 'client' => 'gslite', 'token' => $searchtokensha1, 'session' => $session, 'uuid' => $uuid, ), 'parameters' => array ( 'type' => 'Songs', 'query' => $query, ), 'method' => 'getSearchResults', );
	$searchjson = json_encode($searchjsona);
	$url = "http://cowbell.grooveshark.com/more.php?getSearchResults";
        $page = "/more.php?getSearchResults";
        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Accept-Encoding: identity",
            "Content-length: ".strlen($searchjson),
            "Host: cowbell.grooveshark.com",
            "Content-type: application/json",
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
        );
        $sh = curl_init();
        curl_setopt($sh, CURLOPT_URL,$url);
        curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sh, CURLOPT_TIMEOUT, 60);
        curl_setopt($sh, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($sh, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        // Apply the XML to our curl call
        curl_setopt($sh, CURLOPT_POST, 1);
        curl_setopt($sh, CURLOPT_POSTFIELDS, $searchjson);
        $searchdata = curl_exec($sh);
	return $searchdata;
}

function popular()
{
global $session, $populartokensha1, $uuid;
$popjsona = array ( 'header' => array ( 'client' => 'gslite', 'token' => $token, 'clientRevision' => '20100211.16', 'uuid' => $uuid, 'session' => $session, ), 'parameters' => array ( ), 'method' => 'popularGetSongs', );
$popjson = json_encode($popjsona);
	$url = "http://cowbell.grooveshark.com/more.php?getPopularSongs";
        $page = "/more.php?getPopularSongs";
        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Accept-Encoding: identity",
            "Content-length: ".strlen($popjson),
            "Host: cowbell.grooveshark.com",
            "Content-type: application/json",
            "Connection: close",
            "User-Agent: Python-urllib/2.6"
        );
        $ph = curl_init();
        curl_setopt($ph, CURLOPT_URL,$url);
        curl_setopt($ph, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ph, CURLOPT_TIMEOUT, 60);
        curl_setopt($ph, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ph, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ph, CURLOPT_POST, 1);
        curl_setopt($ph, CURLOPT_POSTFIELDS, $popjson);
        $keydata = curl_exec($ph);
	return $keydata;
}
?>
