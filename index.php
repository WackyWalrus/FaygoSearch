<?php include 'cfg.php';
$sql = $mysqli->query("SELECT * FROM faygos");
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
		<title><?=$siteName;?></title>
		<style type="text/css" href="styles.css"></style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4RJ-gzdvIohmgHvcU1LnzMyPsaQED45s"></script>
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
				['<?=$results["store_name"];?><br><?=$results["city"];?> <?=$results["state"];?>. <?=$results["address"];?><br><?=$results["sodaNames"];?>']<?php if($i != $countRows){?>,<?php } ?>
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
		<h3><?=$siteName;?> <a href="javascript:void(0);" onclick="addStoreAction();" class="button" style="position:relative;left:50px">Add Store</a></h3>
		<div style="text-align:center;margin-bottom:20px"><?=$countRows;?> Stores with Faygo Found!</div>
		<div id="add-store">
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
			<select id="input-faygos" multiple="multiple">
				<?php while($sodaResults = $sodaSql->fetch_assoc()){ ?>
					<option value="<?=$sodaResults['id'];?>"><?=$sodaResults['name'];?></option>
				<?php } ?>
			</select>
			<div class="clear"></div>
			<div style="margin-top:10px">* = Required Fields</div>
			<a href="javascript:void(0)" onclick="saveFaygo();" class="button">Save</a>
		</div>
		<div id="map-canvas"></div>
	</body>
</html>
<?php $mysqli->close(); ?>