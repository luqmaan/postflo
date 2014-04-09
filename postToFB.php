<?php

/*
 * Posts to the specified users wall
 *
 */

include_once 'config.php';
include_once 'lib/facebook/facebook.php';

/*
 // DELETE ME
 $hai = "time: " . time() . " lala";
 echo postToFB("oijewofiwjefoi efjief", "23451", "David", "12345");

 $hai = "time: " . time() . " lala";
 echo postToFB($hai, "362821", "Hebron", "lolcatmoew");
 */

function postToFB($message, $user_id, $name, $access_token) {

	$facebook = new Facebook( array(
			'appId' => $fb_app_id,
			'secret' => $fb_secret
	));

	$output = '';

	$msg = array(
			'message' => $message,
			'access_token' => $access_token
	);

	$to = "/$user_id/feed";

	try {
		$facebook -> api('/me/feed', 'POST', $msg);
		$output .= "Posting message on '$name' wall success";
	}
	catch (FacebookApiException $e) {
		$output .= "Posting message on '$name' wall failed";
	}

	return $output;
}
