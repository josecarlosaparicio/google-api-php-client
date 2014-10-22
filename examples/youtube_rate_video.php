<?php
	
// SCRIPT FOR RATING A YOUTUBE VIDEO
// Trying to rate for this video: https://www.youtube.com/watch?v=ZE8ODPL2VPI
// Api youtube v3 for rate video: https://developers.google.com/youtube/v3/docs/videos/rate?hl=en

//////////////////////////////////////////////////////////////
//  FILE: youtube_rate_video.php  ////////////////////////////
//////////////////////////////////////////////////////////////	

//require_once 'google-api-php-client/src/Google/Client.php';
//require_once 'google-api-php-client/src/Google/Service/YouTube.php';
require_once realpath(dirname(__FILE__) . '/../autoload.php');

// API GOOGLE CLIENT PARAMS
$client_id	= 'SET_CLIENT_ID_GOOGLE_API';
$client_secret	= 'SET_CLIENT_SECRET_GOOGLE_API';
$redirect_uri	= 'SET_REDIRECT_URI_GOOGLE_API'; // url/path/to/file/rating_video.php
// ID VIDEO FOR RATING
$id_video	= 'ZE8ODPL2VPI';
$rating 	= 'like'; // Acceptable values: dislike, like, none
// LOCAL SCRIPT PARAMS
$key 		= '7R6H8364HS';
$is_auth 	= false;
// REQUEST (POST|GET) PARAMS
$idvideo 	= null; 
$rating 	= null;
$code		= null;

if(isset($_POST["idvideo"])) $idvideo = $_POST["idvideo"];
if(isset($_POST["rating"])) $rating = $_POST["rating"];
if(isset($_GET["code"])) $code = $_GET["code"];

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes("https://www.googleapis.com/auth/youtube"); 

if(isset($_SESSION['youtube_data']) && !empty($_SESSION['youtube_data'])) $is_auth = true;	
if($is_auth){

	$token = $_SESSION['youtube_data'];
	$client->setAccessToken($token);
	
	if($idvideo != null && $rating != null){

		$youtube = new Google_Service_YouTube($client);
		$result = $youtube->videos->rate($idvideo,$rating);
	
		echo $result;
		
	}else{
		echo '
		<form action="rating_video.php" method="POST">
			<input type="hidden" name="rating" value="'.$rating.'" /> 
			<input type="hidden" name="idvideo" value="'.$id_video.'" />
			<button type="submit">I like the video with id: '.$id_video.'</button>
		</form>';
	}

}else{	
		
	if($code != null){		

		$client->authenticate($code);
		$_SESSION['youtube_data'] = $client->getAccessToken();
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));

	}else{
		$authUrl = $client->createAuthUrl();			
		echo "<a href='$authUrl'>Sign in with Google </a>";		
	}
}

?>
