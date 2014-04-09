<?php

/*
 * Gets the selected users data and posts to their accounts
 *
 */

require_once ('lib/login/include/adminFunctions.php');
check_valid_user();

require 'config.php';
require 'dbconnect.php';
require 'postToFB.php';
require 'postToTW.php';

// using the Twitter and Facebook library's together results in a session_start() error, which isn't a big problem (I think)
// it results in a E_WARNING level notificion
// workaround is to disable the error notificatoins
// see documentation for session_start() and see what happens when you turn this off
// Report simple running errors
error_reporting(0);
?>

<html>
	<head>
		<title>Notification Results | postFlo</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
		<link href="css/default.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="container">
			<h1>Notification Results | postFlo</h1>
			<?php

			// get the messages and users selected, prevent SQL injections
			$fbmsg = mysql_real_escape_string($_POST['fbMessage']);
			$twmsg = mysql_real_escape_string($_POST['twMessage']);
			// you can't use escape_string on an array! we do the escaping on the individual values later
			$users = $_POST['users'];

			// $output is used to send messages to users
			$output = "Message being sent to Facebook users: $fbmsg <br />";
			$output .= "Message being sent to Twitter users: $twmsg <br /><hr />";

			// Make sure everything is filled out correctly
			if (empty($users)) {
				die("Error: No users selected");
			}

			if (empty($fbmsg)) {
				die("Error: No Facebook message entered");
			}

			if (empty($twmsg)) {
				die("Error: No Twitter message entered");
			}

			// loop through every user passed by the checkboxes
			foreach ($users as $user) {

				$output .= "<p>User ID: $user ";

				// prevent SQL injection
				$currentUser = mysql_real_escape_string($user);

				// figure out which social network the user is in, and which row they are in for that table
				$query = "SELECT network, row FROM users WHERE id = $currentUser";
				$result = mysql_query($query);
				while ($row = mysql_fetch_array($result)) {
					$rowInNetwork = $row['row'];
					$network = $row['network'];
				}

				// if user is in FB, send them a fb message
				if ($network == 'fb') {

					$output .= "fb ";

					$query = "SELECT * FROM fb_users WHERE id = $rowInNetwork";
					$result = mysql_query($query);

					while ($user = mysql_fetch_array($result)) {

						$user_id = $user['user_id'];
						$name = $user['name'];
						$access_token = $user['access_token'];

						$output .= postToFB($fbmsg, $user_id, $name, $access_token);
					}
				}

				elseif ($network == 'tw') {

					$output .= "tw ";

					$query = "SELECT * FROM tw_users WHERE id = $rowInNetwork";

					$result = mysql_query($query);

					while ($user = mysql_fetch_array($result)) {

						$name = '@' . $user['screen_name'];
						$oauth_token = $user['oauth_token'];
						$oauth_token_secret = $user['oauth_token_secret'];

						$output .= postToTW($twmsg, $name, $oauth_token, $oauth_token_secret);

					}

				}

				$output .= "</p>";
			}

			echo $output;
			?>
		</div>
	</body>
</html>
