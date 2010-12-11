<?php
session_start();
include 'config.php';
require 'lastfmapi/lastfmapi.php';
if (!isset($_SESSION['sessionid'])) {
	$sessionch = curl_init(); 
	curl_setopt($sessionch, CURLOPT_URL, "http://www.moovida.com/services/grooveshark/session_start/"); 
	curl_setopt($sessionch, CURLOPT_RETURNTRANSFER, 1); 
	$sessionjson = curl_exec($sessionch);
	#echo "aaaaa";
	#$sessionjson = "aa";
	curl_close($sessionch);
	$_SESSION['sessionid'] = $sessionjson;
}
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


function callRemote($method, $parameters = array())
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
}

function getStreamURL($songID)
{
	$url = callRemote("song.getStreamUrlEx", array('songID' => "$songID"));
	return $url;
}
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

function login($username, $password)
{
	#global $loggedin;
	//$_SESSION['loggedin'] = false;
	if($_SESSION['loggedin']) {
		return $userID;
	} else {
		$token = md5(strtolower($username).$password);
		$userID = callRemote("session.loginExt", array('username' => $username, 'token' => $token));
		return $userID;
		}
}

function loggedInStatus()
{
	#global $loggedin;
	return $_SESSION['loggedin'];
}


function getSongInfo($songID)
{
	$songInfo = callRemote("song.about", array('songID' => $songID));
	return $songInfo;
}

function userGetFavoriteSongs($userID)
{
	$favs = callRemote("user.getFavoriteSongs", array('userID' => $userid));
	return $favs;


}

// Playlist Functions
function userGetPlaylists($userID)
{
	if($_SESSION['loggedin'])
	{
		$playlists = callRemote("user.getPlaylists", array('userID' => $userID));
		return $playlists;
	}

}

function playlistGetSongs($playlistID)
{
	if($_SESSION['loggedin'])
	{
		$playlist = callRemote("playlist.getSongs", array('playlistID' => $playlistID));
		return $playlist;
	}
}
// Search Functions
function searchSongs($query)
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
			$songs = callRemote("search.songs", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
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
		$search = callRemote("search.songs", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
		return $search;
	}
}

function searchArtists($query)
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
			$artists = callRemote("search.artists", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
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
		$search = callRemote("search.artists", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
		return $search;
	}
}

function searchAlbums($query)
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
			$albums = callRemote("search.albums", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
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
		$search = callRemote("search.albums", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
		return $search;
	}
}
// Popular Functions
function popularGetSongs()
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
			$songs = callRemote("popular.getSongs", array('limit' => 100));
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
		$songs = callRemote("popular.getSongs", array('limit' => 100));
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
function artistGetAlbums($artistID)
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
			$albums = callRemote("artist.getAlbums", array('artistID' => $artistID, 'limit' => 100));
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
		$albums = callRemote("artist.getAlbums", array('artistID' => $artistID, 'limit' => 100));
		return $albums;
	}
	
}

function artistGetSongs($artistID)
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
			$songs = callRemote("artist.getSongs", array('artistID' => $artistID, 'limit' => 100));
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
		$songs = callRemote("artist.getSongs", array('artistID' => $artistID, 'limit' => 100));
		return $songs;
	}
}

// Album Functions
function albumGetSongs($albumID)
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
			$songs = callRemote("album.getSongs", array('albumID' => $albumID, 'limit' => 100));
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
		$songs = callRemote("album.getSongs", array('albumID' => $albumID, 'limit' => 100));
		return $songs;
	}
}


?>