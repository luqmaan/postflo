<?php
require 'config.php';

include 'lib/twitter/EpiCurl.php';
include 'lib/twitter/EpiOAuth.php';
include 'lib/twitter/EpiTwitter.php';

require 'lib/facebook/facebook.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
$twAuthUrl = $twitterObj -> getAuthorizationUrl();

$facebook = new Facebook( array(
		'appId' => $fb_app_id,
		'secret' => $fb_secret
));

$user = $facebook -> getUser();

// redirect to facebook page
if (isset($_GET['code'])) {
	header("Location: " . $fb_app_url);
	exit ;
}

// create authorising url
$fbAuthUrl = $facebook -> getLoginUrl(array(
		'canvas' => 0,
		'fbconnect' => 0,
		'scope' => 'offline_access,publish_stream',
		'redirect_uri' => $redirect_uri
));
?>

<html>
	<body>
		<a href="<?php echo $fbAuthUrl;?>">Authorize My Facebook</a>
		<a href="<?php echo $twAuthUrl;?>">Authorize My Twitter</a>
	</body>
</html>
