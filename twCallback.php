<?php

/*
 * This file is called immeidately after the user grants our app their permission on Twitter.
 *
 * 0) Twitter calls this file.
 *    The URL of this file can be changed at dev.twitter.com
 * 1) Gets and sets the access token passed by Twitter
 * 2) Adds the user to the database in the users and tw_users tables
 * 3) Returns a confirmation message
 *
 */

session_start();

include 'lib/twitter/EpiCurl.php';
include 'lib/twitter/EpiOAuth.php';
include 'lib/twitter/EpiTwitter.php';
include 'config.php';
include 'dbconnect.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);

// get the auth_token from the URL (passed by twitter.com)
$twitterObj -> setToken($_GET['oauth_token']);
// get/generate the secret
$token = $twitterObj -> getAccessToken();
// authenticate the current twitter object to the specified user
$twitterObj -> setToken($token -> oauth_token, $token -> oauth_token_secret);
$_SESSION['ot'] = $token -> oauth_token;
$_SESSION['ots'] = $token -> oauth_token_secret;
// use twitter's REST api to get the account information, save it to $twitterInfo
// more info here: https://dev.twitter.com/docs/api/1/get/account/verify_credentials
$twitterInfo = $twitterObj -> get_accountVerify_credentials();
$twitterInfo -> response;

// save the info to local variables
$user_id = $twitterInfo -> id;
$screen_name = $twitterInfo -> screen_name;
$profile_image_url = $twitterInfo -> profile_image_url;
$oauth_token = $token -> oauth_token;
$oauth_token_secret = $token -> oauth_token_secret;

// debugging
// echo "$user_id $oauth_token $oauth_token_secret $screen_name $profile_image_url ";

// insert into the twitter users table
$query = "INSERT INTO tw_users (
								user_id,
								oauth_token,
								oauth_token_secret,
								screen_name,
								profile_image_url)
						VALUES (
								'$user_id',
								'$oauth_token',
								'$oauth_token_secret',
								'$screen_name',
								'$profile_image_url')";
$result = mysql_query($query);

// insert into the all users table
$rowInNetwork = mysql_insert_id();
$query = "INSERT INTO users (
							row,
							network)
						VALUES (
							'$rowInNetwork',
							'tw')";
$result = mysql_query($query);

?>

<html>
		<head>
		<title>Thank You | postFlo</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
		<link href="css/default.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="container">
		<h1>Just another thank you and confirmation message</h1>
		<h2>Thank you @<?php echo $screen_name ?></h2>
		</div>
	</body>
</html>
