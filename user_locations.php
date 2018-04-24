<?php 
	$isDogowner = $_REQUEST["dogowner"];
	$con = mysqli_connect("localhost", "root", "", "social");
	if($isDogowner == "false"){
		$isDogowner = "true";
	} else {
		$isDogowner = "false";
	}
	$response = "";
	$data_query = mysqli_query($con, "SELECT * FROM users WHERE dogowner='$isDogowner'");
	if(mysqli_num_rows($data_query) > 0) {
		while ($row = mysqli_fetch_array($data_query)) {
				 $address = urlencode($row ['address']);
				 $lat = "";
				 $lng = "";
				 $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBBpWLiL30uk2CLoOo5YVKLj5T7NKFHSjE";
				 $resp_json = file_get_contents($url);
				 $resp = json_decode($resp_json, true);
				 if($resp['status'] == 'OK'){
					$lat = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
					$lng = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
				 }
				 $response .= ($row['username']." ".$lat." ".$lng." ");
		}
	}
	echo $response;
 ?>