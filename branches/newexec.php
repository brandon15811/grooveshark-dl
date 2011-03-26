<?php
session_start();
include 'config.php';
require 'lastfmapi/lastfmapi.php';
/*if (!isset($_SESSION['sessionid'])) {
	/*$sessionch = curl_init(); 
	curl_setopt($sessionch, CURLOPT_URL, "http://getthatmp3.com/b/startsession.php"); 
	curl_setopt($sessionch, CURLOPT_RETURNTRANSFER, 1); 
	$sessionjson = curl_exec($sessionch);
	#echo "aaaaa";
	#$sessionjson = "aa";
	curl_close($sessionch);
	//echo callRemote("session.Start", array("apiKey" => "1100e42a014847408ff940b233a39930"));
	$_SESSION['sessionid'] = '{"header":{},"result":{"sessionID":"4a2a382dd65d313c2a2a589041832d76","expireSeconds":604800}}';
}*/
// Last.fm
$authVars = array(
	'apiKey' => "3a6ed2f9c1505f8a30b8c1e3a83d8b28"
);
$auth = new lastfmApiAuth('setsession', $authVars);
$apiClass = new lastfmApi();
#$sessionjson = '{"header":{"sessionID":"e4f4086dbfe63b1489ad6abd912206f9","hostname":"RHL032","serverTime":1275089211},"result":{"sessionID":"e4f4086dbfe63b1489ad6abd912206f9","expireSeconds":604800}}';
$sessionjson = $_SESSION['sessionid'];
$sessionjsona = json_decode($sessionjson, true);
$sessionid = $sessionjsona["result"]["sessionID"];
if ($cache) {
	$con = mysql_connect($mysql_host, $mysql_user, $mysql_pass);
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	$db = mysql_select_db($mysql_db, $con);
	if (!$db)
	{
		die('Could not select database: ' . mysql_error());
	}
}


/*function callRemote($method, $parameters = array())
{
	global $sessionid;
	$jsonarray = array ( 'header' => array ( 'sessionID' => $sessionid, ), 'method' => $method, 'parameters' => $parameters, );
	$postjson = stripslashes(str_replace("]", "}", str_replace("[", "{", json_encode($jsonarray))));
	#echo $postjson;
	$url = "http://api.grooveshark.com/ws/1.0/?json";
        $headers = array(
        "POST /ws/1.0/?json HTTP/1.0",
	    "Host: api.grooveshark.com",
        "User-Agent: Twisted PageGetter",
	    "Content-Length: ".strlen($postjson),
    	"Content-type: text/json",
	    "connection: close"
        );
        $ph = curl_init();
        curl_setopt($ph, CURLOPT_URL,$url);
        curl_setopt($ph, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ph, CURLOPT_TIMEOUT, 60);
        curl_setopt($ph, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ph, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ph, CURLOPT_POST, 1);
        curl_setopt($ph, CURLOPT_POSTFIELDS, $postjson);
        $jsondata = curl_exec($ph);
	return $jsondata;
}*/

function build_query($data, $prefix='', $sep='', $key='') { 
	$ret = array(); 
	foreach ((array)$data as $k => $v) { 
		if (is_int($k) && $prefix != null) { 
			$k = urlencode($prefix . $k); 
		} 
		if ((!empty($key)) || ($key === 0))  $k = $key.'['.urlencode($k).']'; 
		if (is_array($v) || is_object($v)) { 
			array_push($ret, build_query($v, '', $sep, $k)); 
		} else { 
			array_push($ret, params."[".$k."]".'='.urlencode($v)); 
		} 
	} 
	if (empty($sep)) $sep = ini_get('arg_separator.output'); 
	return str_replace("&amp;params", "&params", implode($sep, $ret)); 
}
function callRemote($method, $params = array(), &$url = null)
{
	define('HOST', 'brandontest.net46.net');
	define('ENDPOINT', 'startsession.php');
    $url = sprintf('http://%s/%s?method=%s&%s', HOST, ENDPOINT, $method,
        build_query($params, "params"));
		//echo $url;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
    $result = curl_exec($curl);
    curl_close($curl);
	$result = substr($result, 0, stripos($result, "\r\n") );
    return $result;
}



/*function getStreamURL($songID)
{
	$url = callRemote("song.getStreamUrlEx", array('songID' => "$songID"));
	return $url;
}*/
// User Functions
/*function createUserAuthToken($username, $password)
{
	$hashpass = $password;
	$hashpass = $username.$hashpass;
	$hashpass = md5($hashpass);
	$authToken = callRemote("session.createUserAuthToken", array('username' => $username, 'hashpass' => $hashpass));
	$authtoken = json_decode($authToken, true);
	if (isset($authtoken['fault'])) {
		return $authToken;
	} else {
		$authToken = $authtoken['result']['token'];
		return $authToken;
	}
}

function destroyUserAuthToken($token)
{
	$destroy = callRemote("session.destroyAuthToken", array('token' => $token));
	return $destroy;
}

function loginViaAuthToken($token)
{
	$login = callRemote("session.loginViaAuthToken", array('token' => $token));
	destroyUserAuthToken($token);
	return $login;
}*/

/*
Finish This
*/
function authenticateUser($username, $password)
{
	#global $loggedin;
	//$_SESSION['loggedin'] = false;
	if($_SESSION['loggedin']) {
		return $_SESSION['userID'];
	} else {
		$token = md5(strtolower($username).$password);
		$userID = callRemote("authenticateUser", array('sessionID' => getSession(), 'username' => $username, 'token' => $token));
		return $userID;
	}
}

function getSession()
{
	if (!isset($_SESSION['sessionid'])) 
	{
		$sessionjson = callRemote("startSession");
		$sessionjsona = json_decode($sessionjson, true);
		$sessionid = $sessionjsona["result"]["sessionID"];
		$_SESSION['sessionid'] = $sessionid;
		return $_SESSION['sessionid'];
	} else {
		return $_SESSION['sessionid'];
	}
}
	
	

function loggedInStatus()
{
	#global $loggedin;
	return $_SESSION['loggedin'];
}


function getSongInfo($songID)
{
	$songInfo = callRemote("getSongInfo", array('songID' => $songID));
	return $songInfo;
}

function getUserFavoriteSongs()
{
	$favs = callRemote("getUserFavoriteSongs", array('sessionID' => getSession(), 'limit' => 100));
	return $favs;


}

// Playlist Functions
function getUserPlaylists()
{
	if($_SESSION['loggedin'])
	{
		$playlists = callRemote("getUserPlaylists", array('sessionID' => getSession(), 'limit' => 100));
		return $playlists;
	}

}

function getPlaylistSongs($playlistID)
{
	if($_SESSION['loggedin'])
	{
		$playlist = callRemote("getPlaylistSongs", array('playlistID' => $playlistID, 'limit' => 100));
		return $playlist;
	}
}
// Search Functions
function getSongSearchResultsEx($query)
{
	if ($GLOBALS['cache'])
	{
		$equery = mysql_real_escape_string($query);
		$sql = "SELECT time FROM `search` WHERE type = 'songs' and query = '".$equery."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$songs = callRemote("getSongSearchResultsEx", array('query' => $query, 'limit' => 100));
			$esongs = mysql_real_escape_string($songs);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `search` (`query`, `type`, `json`, `time`) VALUES ('".$equery."', 'songs', '".$esongs."', ".time().");";
			} else {
				$sql = "UPDATE `search` SET `query` = '".$equery."', `json` = '".$esongs."', `time` = '".time()."' WHERE `type` = 'songs' and `query` = '".$equery."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $songs;
		} else {
			$sql = "SELECT json FROM `search` WHERE type = 'songs' and query = '".$equery."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$songs = mysql_fetch_array($result);
			return $songs['0'];
		}
	} else {
		$search = callRemote("getSongSearchResultsEx", array('query' => $query, 'limit' => 100));
		return $search;
	}
}

function getArtistSearchResults($query)
{
	if ($GLOBALS['cache'])
	{
		$equery = mysql_real_escape_string($query);
		$sql = "SELECT time FROM `search` WHERE type = 'artists' and query = '".$equery."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			 }
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$artists = callRemote("getArtistSearchResults", array('query' => $query, 'limit' => 100));
			$eartists = mysql_real_escape_string($artists);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `search` (`query`, `type`, `json`, `time`) VALUES ('".$equery."', 'artists', '".$eartists."', ".time().");";
			} else {
				$sql = "UPDATE `search` SET `query` = '".$equery."', `json` = '".$artists."', `time` = '".time()."' WHERE `type` = 'artists' and `query` = '".$equery."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $artists;
		} else {
			$sql = "SELECT json FROM `search` WHERE type = 'artists' and query = '".$equery."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$artists = mysql_fetch_array($result);
			return $artists['0'];
		}
	} else {
		$search = callRemote("getArtistSearchResults", array('query' => $query, 'limit' => 100));
		return $search;
	}
}

function getAlbumSearchResults($query)
{
	if ($GLOBALS['cache'])
	{
		$equery = mysql_real_escape_string($query);
		$sql = "SELECT time FROM `search` WHERE type = 'albums' and query = '".$equery."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$albums = callRemote("getAlbumSearchResults", array('query' => $query, 'limit' => 100));
			$ealbums = mysql_real_escape_string($albums);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `search` (`query`, `type`, `json`, `time`) VALUES ('".$equery."', 'albums', '".$ealbums."', ".time().");";
			} else {
				$sql = "UPDATE `search` SET `query` = '".$equery."', `json` = '".$ealbums."', `time` = '".time()."' WHERE `type` = 'albums' and `query` = '".$equery."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $albums;
		} else {
			$sql = "SELECT json FROM `search` WHERE type = 'albums' and query = '".$equery."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$albums = mysql_fetch_array($result);
			return $albums['0'];
		}
	} else {
		$search = callRemote("getAlbumSearchResults", array('query' => $query, 'limit' => 100));
		return $search;
	}
}
// Popular Functions
function getPopularSongsToday()
{
	if ($GLOBALS['cache'])
	{
		$sql = "SELECT time FROM `popular` WHERE type = 'songs'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if (time() - $time['0'] > $GLOBALS['ctime'] and $crtype = "PHP")
		{
			$songs = callRemote("getPopularSongsToday", array('limit' => 100));
			$esongs = mysql_real_escape_string($songs);
			$sql = "UPDATE `popular` SET `json` = '".$esongs."', `time` = '".time()."' WHERE `type` = 'songs';";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $songs;
		} else {
			$sql = "SELECT json FROM `popular` WHERE type = 'songs'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$songs = mysql_fetch_array($result);
			return $songs['0'];
		}
	} else {
		$songs = callRemote("getPopularSongsToday", array('limit' => 100));
		return $songs;
	}
}

/*function popularGetArtists()
{
	if ($GLOBALS['cache'])
	{
		$sql = "SELECT time FROM `popular` WHERE type = 'artists'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if (time() - $time['0'] > $ctime and $crtype = "PHP")
		{
			$artists = callRemote("popular.getArtists", array('limit' => 100));
			$eartists = mysql_real_escape_string($songs);
			$sql = "UPDATE `popular` SET `json` = '".$eartists."', `time` = '".time()."' WHERE `type` = 'artists';";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $artists;
		} else {
			$sql = "SELECT json FROM `popular` WHERE type = 'artists'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$artists = mysql_fetch_array($result);
			return $artists['0'];
		}
	} else {
		$songs = callRemote("popular.getArtists", array('limit' => 100));
		return $artists;
	}
}

function popularGetAlbums()
{
	
	if ($GLOBALS['cache'])
	{
		$sql = "SELECT time FROM `popular` WHERE type = 'albums'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if (time() - $time['0'] > $ctime and $crtype = "PHP")
		{

			$albums = callRemote("popular.getAlbums", array('limit' => 100));
			$ealbums = mysql_real_escape_string($songs);
			$sql = "UPDATE `popular` SET `json` = '".$esongs."', `time` = '".time()."' WHERE `type` = 'albums';";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $albums;
		} else {
			$sql = "SELECT json FROM `popular` WHERE type = 'albums'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$albums = mysql_fetch_array($result);
			return $albums['0'];
		}
	} else {
		$songs = callRemote("popular.getAlbums", array('limit' => 100));
		return $albums;
	}
	
}*/
// Artist Functions
function getArtistAlbums($artistID)
{
	if ($GLOBALS['cache'])
	{
		$eartistID = mysql_real_escape_string($artistID);
		$sql = "SELECT time FROM `artist` WHERE type = 'albums' and artistID = '".$eartistID."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$albums = callRemote("getArtistAlbums", array('artistID' => $artistID, 'limit' => 100));
			$ealbums = mysql_real_escape_string($albums);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `artist` (`artistID`, `type`, `json`, `time`) VALUES ('".$eartistID."', 'albums', '".$ealbums."', ".time().");";
			} else {
				$sql = "UPDATE `artist` SET `artistID` = '".$eartistID."', `json` = '".$ealbums."', `time` = '".time()."' WHERE `type` = 'albums' and `artistID` = '".$eartistID."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $albums;
		} else {
			$sql = "SELECT json FROM `artist` WHERE type = 'albums' and artistID = '".$eartistID."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$albums = mysql_fetch_array($result);
			return $albums['0'];
		}
	} else {
		$albums = callRemote("getArtistAlbums", array('artistID' => $artistID, 'limit' => 100));
		return $albums;
	}
	
}

function getArtistSongs($artistID)
{
	if ($GLOBALS['cache'])
	{
		$eartistID = mysql_real_escape_string($artistID);
		$sql = "SELECT time FROM `artist` WHERE type = 'songs' and artistID = '".$eartistID."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$songs = callRemote("getArtistSongs", array('artistID' => $artistID, 'limit' => 100));
			$esongs = mysql_real_escape_string($songs);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `artist` (`artistID`, `type`, `json`, `time`) VALUES ('".$eartistID."', 'songs', '".$esongs."', ".time().");";
			} else {
				$sql = "UPDATE `artist` SET `artistID` = '".$eartistID."', `json` = '".$esongs."', `time` = '".time()."' WHERE `type` = 'songs' and `artistID` = '".$eartistID."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $songs;
		} else {
			$sql = "SELECT json FROM `artist` WHERE type = 'songs' and artistID = '".$eartistID."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$songs = mysql_fetch_array($result);
			return $songs['0'];
		}
	} else {
		$songs = callRemote("getArtistSongs", array('artistID' => $artistID, 'limit' => 100));
		return $songs;
	}
}

// Album Functions
function getAlbumSongsEx($albumID)
{
	if ($GLOBALS['cache'])
	{
		$ealbumID = mysql_real_escape_string($albumID);
		$sql = "SELECT time FROM `album` WHERE albumID = '".$ealbumID."'";
		$result = mysql_query($sql);
		if (!$result)
			{
				die('Could not execute query: ' . mysql_error());
			}
		$time = mysql_fetch_array($result);
		if ((time() - $time['0'] > $GLOBALS['ctime'] and $GLOBALS['crtype'] = "PHP") or (mysql_num_rows($result) == 0))
		{
			$songs = callRemote("getAlbumSongsEx", array('albumID' => $albumID, 'limit' => 100));
			$esongs = mysql_real_escape_string($songs);
			if (mysql_num_rows($result) == 0)
			{
				$sql = "INSERT INTO `album` (`albumID`, `json`, `time`) VALUES ('".$ealbumID."', '".$esongs."', ".time().");";
			} else {
				$sql = "UPDATE `album` SET `albumID` = '".$ealbumID."', `json` = '".$esongs."', `time` = '".time()."' WHERE `albumID` = '".$ealbumID."';";
				}
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			return $songs;
		} else {
			$sql = "SELECT json FROM `album` WHERE albumID = '".$ealbumID."'";
			$result = mysql_query($sql);
			if (!$result)
				{
					die('Could not execute query: ' . mysql_error());
				}
			$songs = mysql_fetch_array($result);
			return $songs['0'];
		}
	} else {
		$songs = callRemote("getAlbumSongsEx", array('albumID' => $albumID, 'limit' => 100));
		return $songs;
	}
}


?>