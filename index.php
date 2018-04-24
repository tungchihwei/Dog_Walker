<?php 
include("includes/header.php");
$address = urlencode($user['address']);
$latitude = "";
$longitude = "";
$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBBpWLiL30uk2CLoOo5YVKLj5T7NKFHSjE";
$resp_json = file_get_contents($url);
$resp = json_decode($resp_json, true);
if ($resp['status'] == 'OK'){
	$latitude = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
	$longitude = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
}

if(isset($_POST['post'])){  
	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";
	if($imageName != "") {
		$targetDir = "assets/images/posts/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) {
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}

	}

	if($uploadOk) {
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], 'none', $imageName);
	}
	else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}

}


 ?>
	<div class="user_details column">
		<a href="<?php echo $userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>">
			<?php 
			echo $user['first_name'] . " " . $user['last_name'];

			 ?>
			</a>
			<br>
			<?php echo "Posts: " . $user['num_posts']. "<br>"; 
			echo "Likes: " . $user['num_likes'];

			?>
			<br>
			<?php 
				if ($user['dogowner'] == 'true'){
					echo "Dog owner<br>";
				} else {
					echo "Dog walker<br>";
				}
			 ?>
			 <br>
		</div>

	</div>

	<div class="main_column column">
		<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
			<textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
			<input type="submit" name="post" id="post_button" value="Post">
			<hr>

		</form>

		<div class="posts_area"></div>
		<!-- <button id="load_more">Load More Posts</button> -->
		<img id="loading" src="assets/images/icons/loading.gif">


	</div>


	<div id = 'map'>
	</div>
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script>
    // Define your locations: HTML content for the info window, latitude, longitude
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
    	if (this.readyState == 4 && this.status == 200) {
    		var arr = this.responseText.split(' ');
    		var locations = [];
    		var user_info = [];
    		user_info.push('<h4>Your location</h4>');
    		user_info.push(<?php echo $latitude ?>);
    		user_info.push(<?php echo $longitude ?>);
    		locations.push(user_info);
    		for(var i = 0; i < arr.length; i+=3){
				(function(){
					var temparr = [];
					var tempstr = '<h4><a href=' + 'http://localhost:8080/demo/' + arr[i] + '>'+  arr[i] +'</a></h4>';
					temparr.push(tempstr);
					temparr.push(arr[i+1]);
					temparr.push(arr[i+2]);
					locations.push(temparr);
				})();
			}
    		var map = new google.maps.Map(document.getElementById('map'), {
			      zoom: 10,
			      center: new google.maps.LatLng(42.350499, -71.1053991),
			      mapTypeId: google.maps.MapTypeId.ROADMAP,
			      mapTypeControl: false,
			      streetViewControl: false,
			      panControl: false,
			      zoomControlOptions: {
			         position: google.maps.ControlPosition.LEFT_BOTTOM
			      }
			    });

			    var infowindow = new google.maps.InfoWindow({
			      maxWidth: 160
			    });

			    var markers = new Array();

			    // var iconCounter = 0;

			    // Add the markers and infowindows to the map
			    for (var i = 0; i < locations.length; i++) {  
			     	if (i == 0){
						var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
				     	var marker = new google.maps.Marker({
				        	position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				        	map: map,
				        	icon: image
				       	 // icon: icons[iconCounter]
				      	});

						      markers.push(marker);

						      google.maps.event.addListener(marker, 'click', (function(marker, i) {
						        return function() {
						          infowindow.setContent(locations[i][0]);
						          infowindow.open(map, marker);
						        }
						      })(marker, i));
					} else {
						      var marker = new google.maps.Marker({
						        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
						        map: map,
						        // icon: icons[iconCounter]
						      });

						      markers.push(marker);

						      google.maps.event.addListener(marker, 'click', (function(marker, i) {
						        return function() {
						          infowindow.setContent(locations[i][0]);
						          infowindow.open(map, marker);
						        }
						      })(marker, i));
					}

			    }

			    function autoCenter() {
			      //  Create a new viewpoint bound
			      var bounds = new google.maps.LatLngBounds();
			      //  Go through each...
			      for (var i = 0; i < markers.length; i++) {  
			                bounds.extend(markers[i].position);
			      }
			      //  Fit these bounds to the map
			      map.fitBounds(bounds);
			    }
			    autoCenter();

    	}
    };
    xmlhttp.open("GET", "user_locations.php?dogowner=" + "<?php echo  $user['dogowner']?>", true);
    xmlhttp.send();
  </script> 
	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn,
			cache:false,

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function() {
		//$('#load_more').on("click", function() {

			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
			//if (noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
						$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
						$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

						$('#loading').hide();
						$('.posts_area').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

	</script>




	</div>
</body>
</html>