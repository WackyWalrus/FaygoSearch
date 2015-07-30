$(document).ready(function(){
	$("#input-faygos").chosen({
		width: '176px'
	});
});

function initialize() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
    	makeMap(0,0,1);
    }
}

function showPosition(position) {
    makeMap(position.coords.latitude,position.coords.longitude,10);
}

function addStoreAction(){
	var addStore = document.getElementById('add-store');
	if(addStore.style.display == undefined || addStore.style.display == '' || addStore.style.display == 'none')
		addStore.style.display = 'block';
	else
		addStore.style.display = 'none';
}

function saveFaygo(){
	var storeName = document.getElementById('input-store-name').value,
		state = document.getElementById('select-state').value,
		city = document.getElementById('input-city').value,
		address = document.getElementById('input-address').value;
	var faygoInput = document.getElementById('input-faygos');
	var selectedFaygos = [];
	for(var i = 0; i < faygoInput.length; i++){
		if(faygoInput.options[i].selected) selectedFaygos.push(faygoInput.options[i].value);
	}

	//if(storeName && state && city && address){
		$.post('save-faygo.php',
			{'store': storeName,
			'state': state,
			'city': city,
			'address': address,
			'sodas': selectedFaygos},
			function(data){
				//console.log(data);
				document.location.href = document.location.href;
			}
		);
	//}
}