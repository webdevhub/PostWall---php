<?php
//This script processes the data in the form on index.php
	if(isset($_GET["access_token"])) {
		$friend = $_POST["friend"];
		$msg = $_POST["message"];
		$access_token = $_GET['access_token'];

		//https://graph.facebook.com/FRIEND_ID/feed
		$link = "https://graph.facebook.com/$friend/feed";
		$result = postViaCurl($link,$access_token,$msg);
		if($result!="") {
			echo "Your message has been posted to Facebook. Please check the Facebook Wall...";
		} else {
			echo "Oops...There was some error.";
		}
	}

//This function will actually post the data to Facebook Server using cURL
function postViaCurl($link,$token,$message) {
	$ch = curl_init();
	//set the URL
	curl_setopt($ch,CURLOPT_URL,$link);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POST,3);
	//feed in the data
	curl_setopt($ch, CURLOPT_POSTFIELDS, array("access_token"=>"$token","message"=>"$message"));
	//post the data
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
?>