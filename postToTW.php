<?php


include 'config.php';
include 'lib/twitter/EpiCurl.php';
include 'lib/twitter/EpiOAuth.php';
include 'lib/twitter/EpiTwitter.php';

/*
//Sample tests for debugging
$time = time();
echo postToTW("Hello World! a $time", "meow", "12382ewfef82", "23552352asdfasdf");
echo postToTW("Hello World! b $time ", "meow", "283sdfdsf8382", "asdfasdf2323");
 */


function postToTW($msg, $name, $oauth_token, $oauth_token_secret) {

	$output = '';

	$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);

	$twitterObj -> setToken($oauth_token, $oauth_token_secret);

	$twitterInfo = $twitterObj -> get_accountVerify_credentials();
	$twitterInfo -> response;

	$update_status = $twitterObj -> post_statusesUpdate(array('status' => $msg));
	$response = $update_status -> response;

	// useful for debugging
	// print_r($response);

	if ($response[error]) {
		$output = "Posting message on '$name' failed. Error: " . $response[error];
	}
	else {
		$output = "Posting message on '$name' success";
	}
	return $output;

}
?>
