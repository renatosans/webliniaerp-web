<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
 Copyright 2008 Google Inc.
 Licensed under the Apache License, Version 2.0:
 http://www.apache.org/licenses/LICENSE-2.0
-->
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title>Google Maps API Example: Simple Geocoding</title>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyBvJfsjF_egk5t2brKIWX1dpkPMmZQdbHI" type="text/javascript"></script>
	<script type="text/javascript">
		var map = null;
		var geocoder = null;

		function initialize() {
			if (GBrowserIsCompatible()) {
				map = new GMap2(document.getElementById("map_canvas"));
				map.setCenter(new GLatLng(37.4419, -122.1419), 1);
				map.setUIToDefault();
				geocoder = new GClientGeocoder();
			}
		}

		function getAddresses() {
			var arr = [];
				arr.push({logradouro: "Rua Cordilheira do Araripe", numero: "359"});
				arr.push({logradouro: "Rua Fortunato Minozzi", numero: "144"});
				arr.push({logradouro: "Rua Barão de Itapetininga", numero: "88"});
				arr.push({logradouro: "Rua Jiparana", numero: "30"});

			for (var i = 0; i < arr.length; i++) {
				showAddress(arr[i].logradouro, arr[i].numero);
			};
		}

		function showAddress(address) {
			if (geocoder) {
				geocoder.getLatLng(
					address,// address + "," + number,
					function(point) {
						if (!point) {
							// alert(address + "," + number + " not found");
							alert(address + " not found");
						} else {
							//map.setCenter(point, 15);

							var marker = new GMarker(point, {draggable: true});

							map.addOverlay(marker);

							GEvent.addListener(marker, "dragend", function() {
								marker.openInfoWindowHtml(marker.getLatLng().toUrlValue(6));
								// marker.openInfoWindowHtml(address + "," + number);
							});

							GEvent.addListener(marker, "click", function() {
								marker.openInfoWindowHtml(marker.getLatLng().toUrlValue(6));
								// marker.openInfoWindowHtml(address + "," + number);
							});

							GEvent.trigger(marker, "click");
						}
					}
					);
			}
		}
	</script>
</head>

<body onload="initialize()" onunload="GUnload()">
	<form action="#" onsubmit="showAddress(this.address.value); return false">
		<p>
			Enter an address, and then drag the marker to tweak the location.
			<br/>
			The latitude/longitude will appear in the infowindow after each geocode/drag.
		</p>
		<p>
			<input type="text" style="width:350px" name="address" value="1600 Amphitheatre Pky, Mountain View, CA" />
			<input type="submit" value="Go!" />
		</p>
		<div id="map_canvas" style="width: 600px; height: 400px"></div>
	</form>

</body>
</html>
