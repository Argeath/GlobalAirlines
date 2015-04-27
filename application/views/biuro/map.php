<div id="map-canvas" style="height: 100%;"></div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCtUtNhB57nIXbWPfLdR1g1sna8pJi0SUQ&sensor=true"></script>
<script type="text/javascript">
var citymap = {};
var zlmap = {};
<?= $citymap; ?>

<?= $zlmap; ?>
var cityCircle;
var zleceniePath;



  function initialize() {
	var mapOptions = {
	  center: new google.maps.LatLng(54.357957,18.64875),
	  zoom: 4,
	  maxZoom: 7,
	  minZoom: 1,
	  mapTypeId: google.maps.MapTypeId.TERRAIN
	};
	var map = new google.maps.Map(document.getElementById("map-canvas"),
		mapOptions);
		
	var airportIcon = '/assets/airporticon.png';
		
	
	for (var city in citymap) {
		var populationOptions = {
		  strokeColor: '#FF0000',
		  strokeOpacity: 0.8,
		  strokeWeight: citymap[city].weight,
		  fillColor: '#FF0000',
		  fillOpacity: 0.35,
		  map: map,
		  center: citymap[city].center,
		  radius: citymap[city].radius
		};
		var markerOptions = {
			position: citymap[city].center,
			map: map,
			icon: airportIcon
		
		};
		// Add the circle for this city to the map.
		cityCircle = new google.maps.Circle(populationOptions);
		
		var marker = new google.maps.Marker(markerOptions);
		
		attachInfoWindow(marker, citymap[city].name);
	}
	var infowindow;
	function attachInfoWindow(marker, name) {
		google.maps.event.addListener(marker, 'click', function() {
			if(infowindow) infowindow.close();
			infowindow = new google.maps.InfoWindow({ content: name });
			infowindow.open(map,marker);
		});
	}
	
	for (var zlecenie in zlmap) {
		var flightPlanCoordinates = [
			zlmap[zlecenie].first,
			zlmap[zlecenie].second
		];
		var zlOptions = {
			path: flightPlanCoordinates,
			geodesic: true,
			strokeColor: zlmap[zlecenie].color,
			strokeOpacity: 0.8,
			strokeWeight: 1.2,
			map: map
		};
		// Add the circle for this city to the map.
		zleceniePath = new google.maps.Polyline(zlOptions);
	}
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>