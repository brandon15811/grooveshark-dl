<?php
session_start();
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
#$sessionjson = '{"header":{"sessionID":"e4f4086dbfe63b1489ad6abd912206f9","hostname":"RHL032","serverTime":1275089211},"result":{"sessionID":"e4f4086dbfe63b1489ad6abd912206f9","expireSeconds":604800}}';
$sessionjson = $_SESSION['sessionid'];
$sessionjsona = json_decode($sessionjson, true);
$sessionid = $sessionjsona["result"]["sessionID"];


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
function createUserAuthToken($username, $password)
{
	$hashpass = $password;
	$hashpass = $username.$hashpass;
	$hashpass = md5($hashpass);
	$authToken = callRemote("session.createUserAuthToken", array('username' => $username, 'hashpass' => $hashpass));
	$authToken = json_decode($authToken, true);
	$authToken = $authToken['result']['token'];
	return $authToken;
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
}

function login($username, $password)
{
	#global $loggedin;
	$_SESSION['loggedin'] = false;
	if($_SESSION['loggedin']) {
		return $userID;
	} else {
		$token = createUserAuthToken($username, $password);
		$userID = loginViaAuthToken($token);
		$_SESSION['loggedin'] = true;
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
		$playlists = callRemote("remote.getPlaylists", array('userID' => $userID));
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
	$search = callRemote("search.songs", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
	return $search;
}

function searchArtists($query)
{
	$search = callRemote("search.artists", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
	return $search;
}

function searchAlbums($query)
{
	$search = callRemote("search.albums", array('query' => $query, 'limit' => 100, 'streamableOnly' => 1));
	return $search;
}
// Popular Functions
function popularGetSongs()
{
	$songs = callRemote("popular.getSongs", array('limit' => 100));
	return $songs;
}

function popularGetArtists()
{
	$songs = callRemote("popular.getArtists", array('limit' => 100));
	return $songs;
}

function popularGetAlbums()
{
	$songs = callRemote("popular.getAlbums", array('limit' => 100));
	return $songs;
}
// Artist Functions
function artistGetAlbums($artistID)
{
	$albums = callRemote("artist.getAlbums", array('artistID' => $artistID, 'limit' => 100));
	return $albums;
}

function artistGetSongs($artistID)
{
	$songs = callRemote("artist.getSongs", array('artistID' => $artistID, 'limit' => 100));
	return $songs;
}

// Album Functions
function albumGetSongs($artistID)
{
	$songs = callRemote("album.getSongs", array('artistID' => $artistID, 'limit' => 100));
	return $songs;
}


?>

