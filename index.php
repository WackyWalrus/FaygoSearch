<?php include 'cfg.php';

if($_GET['search']){
	$zipcode = $mysqli->real_escape_string($_GET['search']);
	$zipcode_qry = $mysqli->query("SELECT * FROM saved_zipcodes WHERE zipcode = '{$zipcode}' LIMIT 1");
	if($zipcode_qry->num_rows){
		$zipcode_qry_results = $zipcode_qry->fetch_assoc();
		$lat = $zipcode_qry_results['lat'];
		$lng = $zipcode_qry_results['lng'];
	}else{
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address={$zipcode}";
		$response = curl_get_contents($url);
	    $array = json_decode($response);
	    $lat = $array->results[0]->geometry->location->lat;
	    $lng = $array->results[0]->geometry->location->lng;
	    $time = time();
	    $mysqli->query("INSERT INTO saved_zipcodes (zipcode,lat,lng,datestamp) VALUES ('{$zipcode}','{$lat}','{$lng}',{$time})");
	}
	$qry = "SELECT *,(3959 * acos(cos(radians({$lat})) * cos(radians(lat)) * cos(radians(lng) - radians({$lng})) + sin(radians({$lat})) * sin(radians(lat)))) AS distance FROM faygos HAVING distance < 25 ORDER BY distance LIMIT 0,20";
}else{
	$qry = "SELECT * FROM faygos";
}

$sql = $mysqli->query("{$qry}");

$locations = array();
while($results = $sql->fetch_assoc()){
	$grabSodaNames = $mysqli->query("SELECT * FROM sodas WHERE id IN ({$results['sodas']})");
	$sodaNames = '';
	while($gettingSodaNames = $grabSodaNames->fetch_assoc()){
		$sodaNames .= "<li>".addslashes($gettingSodaNames['name'])."</li>";
	}
	$results['sodaNames'] = $sodaNames;
	$locations[] = $results;
}
$count = $mysqli->query("SELECT COUNT(*) FROM faygos");
$countRows = $count->fetch_assoc();
$countRows = $countRows['COUNT(*)'];

$sodas = array();
$sodaSql = $mysqli->query("SELECT * FROM sodas ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/meta.php'; ?>
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<link rel="stylesheet" type="text/css" href="scripts/chosen.min.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4RJ-gzdvIohmgHvcU1LnzMyPsaQED45s"></script>
		<script type="text/javascript" src="scripts/chosen.jquery.js"></script>
		<script type="text/javascript" src="scripts.js"></script>
		<script type="text/javascript">
		function makeMap(latitude,longitude,z){
			var bounds = new google.maps.LatLngBounds();
			var mapOptions = {
			 	center: { lat: latitude, lng: longitude},
				zoom: z
			};
			// var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
			// var marker = new google.maps.Marker({
			//     position: myLatLng,
			//     map: map
			// });
			var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
			var markers = [ <?php $i = 0;
			foreach($locations as $results){ $i++
			//while($results = $sql->fetch_assoc()){ $i++; ?>
				['<?=$results["address"];?>. <?=$results["city"];?>, <?=$results["state"];?>', <?=$results['lat'];?>, <?=$results['lng'];?>]<?php if($i != $countRows){?>,<?php } ?>
				<?php
			} ?> ];
			var infoWindowContent = [ <?php $i = 0;
			foreach($locations as $results){ $i++;
			//while($results = $sql->fetch_assoc()){ $i++; ?>
				['<strong><?=$results["store_name"];?></strong><br><?=$results["city"];?> <?=$results["state"];?>. <?=$results["address"];?><br><?=$results["sodaNames"];?>']<?php if($i != $countRows){?>,<?php } ?>
				<?php 
			} ?> ];

			var infoWindow = new google.maps.InfoWindow(), marker, i;


			for( i = 0; i < markers.length; i++ ) {
		        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
		        bounds.extend(position);
		        marker = new google.maps.Marker({
		            position: position,
		            map: map,
		            title: markers[i][0]
		        });
		        
		        // Allow each marker to have an info window    
		        google.maps.event.addListener(marker, 'click', (function(marker, i) {
		            return function() {
		                infoWindow.setContent(infoWindowContent[i][0]);
		                infoWindow.open(map, marker);
		            }
		        })(marker, i));

		        // Automatically center the map fitting all markers on the screen
		        map.fitBounds(bounds);
		    }
		}

		google.maps.event.addDomListener(window,'load',initialize);


		</script>
	</head>
	<body>
		<h3><?=$siteName;?> <a href="javascript:void(0);" onclick="addStoreAction();this.style.display='none';" class="button" style="position:relative;left:50px">Add Store</a></h3>
		<div style="text-align:center;margin-bottom:20px"><?=$countRows;?> Stores with Faygo Found!</div>
		<div id="add-store">
			<h4>Add Store</h4>
			<div class="former">Store Name: *</div>
			<input type="text" id="input-store-name" />
			<div class="former">State: *</div>
			<select id="select-state">
				<option value=""></option>
				<option value="AL">AL</option>
				<option value="AK">AK</option>
				<option value="AZ">AZ</option>
				<option value="AR">AR</option>
				<option value="CA">CA</option>
				<option value="CO">CO</option>
				<option value="CT">CT</option>
				<option value="DE">DE</option>
				<option value="FL">FL</option>
				<option value="GA">GA</option>
				<option value="HI">HI</option>
				<option value="ID">ID</option>
				<option value="IL">IL</option>
				<option value="IN">IN</option>
				<option value="IA">IA</option>
				<option value="KS">KS</option>
				<option value="KY">KY</option>
				<option value="LA">LA</option>
				<option value="ME">ME</option>
				<option value="MD">MD</option>
				<option value="MA">MA</option>
				<option value="MI">MI</option>
				<option value="MN">MN</option>
				<option value="MS">MS</option>
				<option value="MO">MO</option>
				<option value="MT">MT</option>
				<option value="NE">NE</option>
				<option value="NV">NV</option>
				<option value="NH">NH</option>
				<option value="NJ">NJ</option>
				<option value="NM">NM</option>
				<option value="NY">NY</option>
				<option value="NC">NC</option>
				<option value="ND">ND</option>
				<option value="OH">OH</option>
				<option value="OK">OK</option>
				<option value="OR">OR</option>
				<option value="PA">PA</option>
				<option value="RI">RI</option>
				<option value="SC">SC</option>
				<option value="SD">SD</option>
				<option value="TN">TN</option>
				<option value="TX">TX</option>
				<option value="UT">UT</option>
				<option value="VT">VT</option>
				<option value="VA">VA</option>
				<option value="WA">WA</option>
				<option value="WV">WV</option>
				<option value="WI">WI</option>
				<option value="WY">WY</option>
			</select>
			<div class="former">City: *</div>
			<input type="text" id="input-city" />
			<div class="former">Address: *</div>
			<input type="text" id="input-address" />
			<div class="former">Faygo Flavors:</div>
			<select id="input-faygos" multiple="multiple" style="width:177px">
				<?php while($sodaResults = $sodaSql->fetch_assoc()){ ?>
					<option value="<?=$sodaResults['id'];?>"><?=$sodaResults['name'];?></option>
				<?php } ?>
			</select>
			<div class="clear"></div>
			<div style="margin-top:10px">* = Required Fields</div>
			<a href="javascript:void(0)" onclick="saveFaygo();" class="button">Save</a>
		</div>
		<div id="map-canvas"><img src="images/ajax-loader.gif" /></div>
	</body>
</html>
<?php $mysqli->close(); ?>