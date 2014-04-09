<?php

/*
 * This file is the user login form. 
 * The login system right now is very backwards and hacked together; a better system needs to be built.
 * 
 */

if (isset($_POST['submit'])) {
	require_once ('config.php');
	require_once ('lib/login/include/adminFunctions.php');
	$error = array();
	$username = $_POST['username'];
	$passwd = $_POST['passwd'];
	if (empty($username)) {
		$error[] = 'Error - You forgot to enter username.';
	}
	if (empty($passwd)) {
		$error[] = 'Error - You forgot to enter password.';
	}

	if (empty($error)) {
		list($check, $data) = login($username, $passwd);
		if ($check) {
			session_start();
			$_SESSION['userid'] = $data['userid'];
			$_SESSION['username'] = $data['username'];

			// Store the HTTP user agent
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

			// replace this welcome.php to antoher if you want to redirect to anotherlink
			$url = urlPath('admin.php');
			header("Location: $url");
			exit();
		}
		else {
			$error = $data;
		}
	} // end of empty($error)
}
?>

<html>
	<head>
		<title>Log In | postFlo</title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
		<link href="css/default.css" rel="stylesheet" type="text/css" />
	</head>
	<div id="container">
		<form id="form" name="form" method="POST" action="login.php">
			<h1>Log In | postFlo</h1>
			<?php

			if (!empty($error)) {
				echo "<div class=\"msg\">";
				foreach ($error as $e) {
					echo "* $e <br>";
				}
				echo "</div><br/><br/>";

			}
			?>

			<label>User Name</label>
			<input type="text" name="username" id="username" class="full"/>
			<label>Password</label>
			<input type="password" name="passwd" id="password" class="full" />
			<input type="submit" name="submit" class="submit full" id="submit" value="Log In">
			<div class="spacer"></div>
		</form>
	</div>
</html>