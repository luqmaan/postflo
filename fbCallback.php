<?php

/*
 * This file saves the user to the database and displays a confirmation message to the user.
 *
 * 0) Called by Facebook, which in turn is called by the authorization buttons
 * 	  You can change which file is called in your Facebook app settings
 * 1) Facebook passes some tokens to this file, which uses them to get the user's data
 * 2) This file saves the users access tokens and saves it to a table called 'fb_users'
 *     a row in fb_users looks like
 *      id = 7,
 *      user_id = 12345,
 *      name = John Smith,
 *      access_token = iejfoiwejfoiwejfoiwjef6
 * 3) This file also inserts id as 'row' into into a table called 'users' along with their network (fb)
 *
 */

require 'config.php';
require 'dbconnect.php';
require 'lib/facebook/facebook.php';

$facebook = new Facebook( array(
		'appId' => $fb_app_id,
		'secret' => $fb_secret
));

// get user- if present, insert/update access_token for this user
$user = $facebook -> getUser();

if ($user) {
	// get user data and access token, if it doesn't work, die
	try {
		// $userData contains all the information we need from the user, e.g. name
		$userData = $facebook -> api('/me');
	}
	catch (FacebookApiException $e) {
		die("API call failed");
	}

	$accessToken = $facebook -> getAccessToken();

	// check that user is not already inserted? If it is check it's access token and update if needed
	// also make sure that there is only one access_token for each user
	$row = null;
	$result = mysql_query("
		SELECT
			*
		FROM
			fb_users
		WHERE
			user_id = '" . mysql_real_escape_string($userData['id']) . "'
	");

	if ($result) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if (mysql_num_rows($result) > 1) {
			mysql_query("
				DELETE FROM
					fb_users
				WHERE
					user_id='" . mysql_real_escape_string($userData['id']) . "' AND
					id != '" . $row['id'] . "'
			");
		}
	}

	if (!$row) {
		mysql_query("INSERT INTO
				fb_users
			SET
				`user_id` = '" . mysql_real_escape_string($userData['id']) . "',
				`name` = '" . mysql_real_escape_string($userData['name']) . "',
				`access_token` = '" . mysql_real_escape_string($accessToken) . "'
		");
	}
	else {
		mysql_query("UPDATE
				fb_users
			SET
				`access_token` = '" . mysql_real_escape_string($accessToken) . "'
			WHERE
				`id` = " . $row['id'] . "
		");
	}

	// insert into the all users table
	$rowInNetwork = mysql_insert_id();
	$query = "INSERT INTO users (row, network) VALUES ('$rowInNetwork', 'fb')";
	$result = mysql_query($query);

}

else {

	$loginUrl = $facebook -> getLoginUrl(array(
			'canvas' => 1,
			'fbconnect' => 0,
			'scope' => 'offline_access,publish_stream'
	));

	echo "<h1>Sorry, but we didn't get the permissions we need to continue. <a href='$loginUrl'>Please try again.</h1>";
}
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
			<h2>Thank you <?php echo $userData['name']?></h2>
		</div>
	</body>
</html>
