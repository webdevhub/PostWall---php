<?php
$appId = "175679282469826";
$appSecret = "04e2609874317ac00f00bb5a4044e8d8";
$redirectUri = "http://demo.webdevhub.net/facebook/postWall-php/";
$encRedirectUri = urlencode($redirectUri);
$access_token = "";
$friends = array();
$status = "";

//https://www.facebook.com/dialog/oauth?client_id=YOUR_APP_ID&redirect_uri=YOUR_URL&scope=PERMISSION1[,PERMISSION2...]
$loginUrl = "https://www.facebook.com/dialog/oauth?client_id=$appId&redirect_uri=$encRedirectUri&scope=publish_stream";

if(isset($_GET['code'])) {
	$code = $_GET['code'];
	/*https://graph.facebook.com/oauth/access_token?
	    client_id=YOUR_APP_ID&redirect_uri=YOUR_URL&
	    client_secret=YOUR_APP_SECRET&code=THE_CODE_FROM_ABOVE*/
	$accessTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=$appId&redirect_uri=$encRedirectUri&client_secret=$appSecret&code=$code";
	$result = file_get_contents($accessTokenUrl);

	//Regular expression for separating access_token from the response we get from facebook
	/*the following Regular expression says "Get me two groups of text such that first one is
	after a string called 'access_token=' & before a string called '&expires='(string 2) and
	the other group whatever is left after string 2" **/
	$regex = "/access_token=(.*)&expires=(.*)/";
	preg_match($regex,$result,$matches);
	$access_token = $matches[1]; //access_token
	$status = "You are now logged in. Please load friends now";
}

if(isset($_GET['access_token'])&&$_GET['access_token']!='') {
	$access_token = $_GET['access_token'];

	if(isset($_GET['action'])&&$_GET['action']=='friend') {
		//https://graph.facebook.com/USER_ID/friends?access_token=ACCESS_TOKEN
		$friends_url = "https://graph.facebook.com/me/friends?access_token=".$_GET['access_token'];
		$friends = json_decode(file_get_contents($friends_url),true);
		$friends = $friends["data"];
		$status = "All your friends loaded. Please select a friend and post message";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Facebook Post Wall Application (in PHP)</title>
<!-- Include the normal stylesheet-->
<link href="style.css" rel="stylesheet" />
</head>
<body>
      <div id="wrapper">
		<a class="btn" href="<?php echo $loginUrl; ?>">Get Permissions from Facebook</a>
		<a class="btn" href="http://demo.webdevhub.net/facebook/postWall-php/?action=friend&access_token=<?php echo $access_token;?>">Load Friends</a>
		
		<!-- the following div will show the status messages during the workflow of application-->
		<div id="status"><?php echo $status; ?></div>
		
		<!-- This is the form which when submitted takes the data to Facebook Server-->
		<form id="postSomething" action="submit.php?access_token=<?php echo $access_token;?>" method="post">

			Select the friend on whose wall you want to post 
			<select id="friendList" name="friend">
				<?php
					$i = count($friends);
					if($i!=0) {
						echo '<option value="me">me</option>';
						for($v=0;$v<$i;$v++) {
							// <option value="FRIEND_ID">FRIEND_NAME</option>
							echo '<option value="'.$friends[$v]['id'].'">'.$friends[$v]["name"].'</option>';
						}
					}
				?>
			</select>
			<br/>
			Write the Message  <textarea cols="35" rows="4" name="message"></textarea><br/>
			<input type="submit" value="Post on Wall"/>
		</form>

		<p>
		Instructions
		<ul>
			<li>Click on "Get Permissions from Facebook" button</li>
			<li>Click on "Load Friends" button</li>
			<li>Select the friend on whose wall you want to post message. Enter the message</li>
			<li>Click on "Post on Wall" button. You will be redirected to some other page</li>
		</p>
      </div>
</body>
</html>