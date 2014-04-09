<?php

/*
 * This file generates the admin page, basd on the values in the database
 * 
 * /

require_once ('lib/login/include/adminFunctions.php');
check_valid_user();
?>
<html>
	<head>
		<title>Administration</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
		<link href="css/default.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
			#users {
				display: block;
				clear: both;
				margin: 10px 0 10px 10px;
			}
			#users img {
				margin: 0px 4px
			}
		</style>
	</head>
	<body>
		<div id="container">
			<h1>postFlo</h1>
			<form name="elMessengero" method="POST" action="notify.php">
				<label> Twitter Message </label>
				<input type="text" class="full text" name="twMessage" minlength="1" maxlength="140">
				<label> Facebook Message </label>
				<input type="text" class="full text" name="fbMessage" minlength="1">
				<div id="users">
					<h2>Users</h2>
					<input type="button" class="check" value="check all" />
					<?php

					/*
					 * Create the checkboxes
					 *
					 * Query gets executed selecting all users from main users table
					 * We get full results back with users of main table
					 * Loop through each row and find the value of the stuff in the table.
					 *
					 */

					require 'config.php';
					require 'dbconnect.php';
					require 'lib/twitter/EpiOAuth.php';
					require 'lib/twitter/EpiTwitter.php';
					require 'lib/twitter/EpiCurl.php';
					require 'lib/klout/klout.php';

					$output = '';

					$query = "SELECT * FROM users";
					$results = mysql_query($query);

					// $row is the main loop for all users
					// $Row is the secondary loop for the current user
					while ($row = mysql_fetch_array($results)) {

						$id = $row['id'];

						if ($row['network'] == "tw") {
							$query = "SELECT DISTINCT user_id, profile_image_url, screen_name FROM tw_users WHERE id =" . $row['row'];
							$Results = mysql_query($query);

							/*print the tw user's info*/
							while ($Row = mysql_fetch_array($Results)) {
								$userKlout = getKloutScore($Row['screen_name'], $klout_key);
								if ($userKlout > 0) {
									$userKlout = " <em class='klout'>Klout " . $userKlout . "</em>";
								}
								$output .= "<span class='checkbox'>";
								$output .= "<input type='checkbox' name='users[]' value='$id' id='checkbox$id' />";
								$output .= "<label for='checkbox$id'>T <img src='" . $Row['profile_image_url'] . "' alt='@" . $Row['screen_name'] . "' width='30' height='30' />@" . $Row['screen_name'] . $userKlout . "</label>";
								$output .= "</span>";
							}
						}
						elseif ($row['network'] == "fb") {
							$query = "SELECT DISTINCT user_id, name FROM fb_users WHERE id = " . $row['row'];
							$Results = mysql_query($query);

							/*print the fb user's info*/
							while ($Row = mysql_fetch_array($Results)) {
								$output .= "<span class='checkbox'>";
								$output .= "<input type='checkbox' name='users[]' value='$id' id='checkbox$id' />";
								$output .= "<label for='checkbox$id'>F <img src='http://graph.facebook.com/" . $Row['user_id'] . "/picture' alt='" . $Row['name'] . "' width='30' height='30' />" . $Row['name'] . "</label>";
								$output .= "</span>";
							}

						}
					}

					echo $output;
					?>
				</div>
				<input type="submit" class="full submit"/>
			</form>
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
			<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
			<script type="text/javascript">
				$(document).ready(function() {
					$('.check:button').toggle(function() {
						$('input:checkbox').attr('checked', 'checked');
						$(this).val('uncheck all')
					}, function() {
						$('input:checkbox').removeAttr('checked');
						$(this).val('check all');
					})
				})
			</script>
		</div>
	</body>
</html>