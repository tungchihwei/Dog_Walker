<html>
<head>
	<title></title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
	<div id="hidden_form_container" action="geolocation.php" style="display:none;"></div>
  <script>
    function geoFindMe() {
          function success(position) {
            var latitude  = position.coords.latitude;
            var longitude = position.coords.longitude;
            var content = latitude + " " + longitude;
            alert(content);
            $.post( "geolocation.php",{'location': content});
            alert("success");
          }
          function error() {}
          navigator.geolocation.getCurrentPosition(success, error);
    }
    geoFindMe();
  </script>
</body>
</html>