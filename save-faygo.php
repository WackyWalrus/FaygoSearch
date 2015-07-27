<?php
include 'cfg.php';

function curl_get_contents($url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

$urlState = urlencode($_POST['state']);
$urlCity = urlencode($_POST['city']);
$urlAddress = urlencode($_POST['address']);

if($_POST['store'] && $_POST['state'] && $_POST['city'] && $_POST['address']){
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$urlAddress},+{$urlCity},+{$urlState}&key=AIzaSyC4RJ-gzdvIohmgHvcU1LnzMyPsaQED45s";
	$response = curl_get_contents($url);
    $array = json_decode($response);

    $addressData = $array->results[0]->address_components;
    $address = "{$addressData[0]->long_name} {$addressData[1]->long_name}";
    $city = $addressData[2]->long_name;
    $state = $addressData[4]->short_name;
    $lat = $array->results[0]->geometry->location->lat;
    $lng = $array->results[0]->geometry->location->lng;
    $storeName = $mysqli->real_escape_string($_POST['store']);
    $faygos = array();
    foreach($_POST['sodas'] as $soda){
        $faygos[] = intval($soda);
    }
    $faygos = implode(',',$faygos);
    $time = time();

    $mysqli->query("INSERT INTO faygos (`store_name`,`lat`,`lng`,`state`,`city`,`address`,`sodas`,`datestamp`) VALUES ('{$storeName}','{$lat}','{$lng}','{$state}','{$city}','{$address}','{$faygos}',{$time})");

}else{
    echo 'test';
}

?>