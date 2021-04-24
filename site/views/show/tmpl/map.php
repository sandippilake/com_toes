<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die; 
//map_key

$params = JComponentHelper::getParams('com_toes');
$map_key = $params->get('map_key');

//var_dump($this->venue);
$google_search_address = '';
if($this->venue->address_latitude && $this->venue->address_longitude){
	
	header('Location:https://maps.google.com/maps?q='.$this->venue->address_latitude.'+'.$this->venue->address_longitude);

}else{
	$google_search_address_array = [];
	if($this->venue->venue_name)$google_search_address_array[] = trim($this->venue->venue_name) ;
	if($this->venue->address_line_1)$google_search_address_array[] = trim($this->venue->address_line_1) ;
	if($this->venue->address_line_2)$google_search_address_array[] = trim($this->venue->address_line_2) ;
	if($this->venue->address_line_3)$google_search_address_array[] = trim($this->venue->address_line_3) ;
	if($this->venue->address_city)$google_search_address_array[] = trim($this->venue->address_city) ;
	if($this->venue->address_zip_code)$google_search_address_array[] = trim($this->venue->address_zip_code) ;
	if($this->venue->address_state)$google_search_address_array[] = trim($this->venue->address_state) ;
	if($this->venue->address_country)$google_search_address_array[] = trim($this->venue->address_country) ;
	$google_search_address = urlencode(implode(', ',$google_search_address_array));	
	header('Location:https://maps.google.com/maps?q='.$google_search_address);
}

  
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 <script  
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $map_key?>&sensor=false">
    </script>
 


<style>
#map{width:100%;height:600px;clear:both;}
#Directions{width:100%;height:600px;overflow-y:scroll;clear:both;}
input#Address{width:400px;height:20px}
</style>
<div id="map">


</div>
<div>
<p>
<input type="text" size="100" name="Address" id="Address" value=""/>
</p>
<p>
<input id="clr_btn" type="button" class="primary-button" value="Clear Directions">
<input id="directions_btn" type="button" class="primary-button" value="Show Directions">
</p>

</div>

<div id="panel-direction"></div>

<script>
	var google_search_address = '';
	function setMapHeight(){
    $("#map, #panel-direction").css("height", "500px");
	}

	function setMapHeightMin(){
	   $("#map, #panel-direction").css("height", "30px");
	}
	var geocoder = new google.maps.Geocoder();
	<?php if($this->venue->address_latitude){?>
		venuelat = <?php echo $this->venue->address_latitude?> ;
	<?php } ?>
	<?php if($this->venue->address_latitude){?>
		venuelng = <?php echo $this->venue->address_longitude?> ;
	<?php } ?>
	
	<?php if($google_search_address){?>
		google_search_address = '<?php echo $google_search_address?>' ;
	<?php } ?>
	if(typeof venuelat === 'undefined' || typeof venuelng === 'undefined'   ){		
		geocoder.geocode({'address': google_search_address}, function(results, status) {
		 
		  if (status === 'OK') {			  
			console.log(results[0].geometry.location.lat());
			venuelat = results[0].geometry.location.lat();
			console.log(results[0].geometry.location.lng());
			venuelng = results[0].geometry.location.lng();
		  } else {
			alert('Geocode was not successful for the following reason: ' + status);
		  }
		});
		
		
		
	}

//array to hold the geo address
var geoAddress = [];
var mylat = null;
var mylng = null;
var address = '';
var map;
var styledMapType = new google.maps.StyledMapType(
            [
              {elementType: 'geometry', stylers: [{color: '#ebe3cd'}]},
              {elementType: 'labels.text.fill', stylers: [{color: '#523735'}]},
              {elementType: 'labels.text.stroke', stylers: [{color: '#f5f1e6'}]},
              {
                featureType: 'administrative',
                elementType: 'geometry.stroke',
                stylers: [{color: '#c9b2a6'}]
              },
              {
                featureType: 'administrative.land_parcel',
                elementType: 'geometry.stroke',
                stylers: [{color: '#dcd2be'}]
              },
              {
                featureType: 'administrative.land_parcel',
                elementType: 'labels.text.fill',
                stylers: [{color: '#ae9e90'}]
              },
              {
                featureType: 'landscape.natural',
                elementType: 'geometry',
                stylers: [{color: '#dfd2ae'}]
              },
              {
                featureType: 'poi',
                elementType: 'geometry',
                stylers: [{color: '#dfd2ae'}]
              },
              {
                featureType: 'poi',
                elementType: 'labels.text.fill',
                stylers: [{color: '#93817c'}]
              },
              {
                featureType: 'poi.park',
                elementType: 'geometry.fill',
                stylers: [{color: '#a5b076'}]
              },
              {
                featureType: 'poi.park',
                elementType: 'labels.text.fill',
                stylers: [{color: '#447530'}]
              },
              {
                featureType: 'road',
                elementType: 'geometry',
                stylers: [{color: '#f5f1e6'}]
              },
              {
                featureType: 'road.arterial',
                elementType: 'geometry',
                stylers: [{color: '#fdfcf8'}]
              },
              {
                featureType: 'road.highway',
                elementType: 'geometry',
                stylers: [{color: '#f8c967'}]
              },
              {
                featureType: 'road.highway',
                elementType: 'geometry.stroke',
                stylers: [{color: '#e9bc62'}]
              },
              {
                featureType: 'road.highway.controlled_access',
                elementType: 'geometry',
                stylers: [{color: '#e98d58'}]
              },
              {
                featureType: 'road.highway.controlled_access',
                elementType: 'geometry.stroke',
                stylers: [{color: '#db8555'}]
              },
              {
                featureType: 'road.local',
                elementType: 'labels.text.fill',
                stylers: [{color: '#806b63'}]
              },
              {
                featureType: 'transit.line',
                elementType: 'geometry',
                stylers: [{color: '#dfd2ae'}]
              },
              {
                featureType: 'transit.line',
                elementType: 'labels.text.fill',
                stylers: [{color: '#8f7d77'}]
              },
              {
                featureType: 'transit.line',
                elementType: 'labels.text.stroke',
                stylers: [{color: '#ebe3cd'}]
              },
              {
                featureType: 'transit.station',
                elementType: 'geometry',
                stylers: [{color: '#dfd2ae'}]
              },
              {
                featureType: 'water',
                elementType: 'geometry.fill',
                stylers: [{color: '#b9d3c2'}]
              },
              {
                featureType: 'water',
                elementType: 'labels.text.fill',
                stylers: [{color: '#92998d'}]
              }
            ],
            {name: 'Styled Map'});

//function framework

	//function framework
ticaMap = {
	initNavigateMap: function (mapID, panelDirectionID, startLatitude, startLongitude, endLatitude, endLongitude,address){
		//alert(address);
		
		console.log('startLatitude :'+startLatitude);
		console.log('startLongitude :'+startLongitude);
		console.log('endLatitude :'+endLatitude);
		console.log('endLongitude :'+endLongitude);
		
		var directionsDisplay = new google.maps.DirectionsRenderer;
		var directionsService = new google.maps.DirectionsService;
		
		
		//initialize the map
		map = new google.maps.Map(document.getElementById(mapID), {
		  zoom: 7,
		  center: {lat: startLatitude, lng: startLongitude}
		}); 
		
		//clear the direction panel
		$("#" + panelDirectionID).html("");
		directionsDisplay.setMap(map);
		directionsDisplay.setPanel(document.getElementById(panelDirectionID));

		//prepare the latitude and longitude data
		start = startLatitude + ", " + startLongitude;
		end = endLatitude + ", " + endLongitude;
		ticaMap.calculateAndDisplayRoute(directionsService, directionsDisplay, start, end,endLatitude,endLongitude,startLatitude,startLongitude);
	},

	//function to get the driving route
	calculateAndDisplayRoute: function (directionsService, directionsDisplay, start, end,endLatitude,endLongitude,startLatitude,startLongitude) {
		directionsService.route({
		  origin: start,
		  destination: end,
		  travelMode: 'DRIVING'
		}, function(response, status) {
		  if (status === 'OK') {
			directionsDisplay.setDirections(response);
		  } else {
			  
			alert('No driving directions found from the start location');
			ticaMap.initMapOnly("map",endLatitude,endLongitude,startLatitude,startLongitude);

		  }
		});
	},

	//get geolocation based on address
	codeAddress: function (address) {
		return new Promise(function(resolve, reject){
			geocoder.geocode({ 'address': address }, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					resolve(results);
				} else {
					reject(Error("Geocode for address " + address + " was not successful for the following reason: " + status));
				}
			});
		});
	},
	
	//function to get geolocation of both addresses.
	getGeolocationData: function(){
		if($("#Address").val()!= ""){
			geoAddress = [];
			ticaMap.codeAddress($("#Address").val()).then(function(response){
				var geoData = {
					latitude: response[0].geometry.location.lat(),
					longitude: response[0].geometry.location.lng()
				}
				geoAddress.push(geoData);
			}).then(function(){
				 
					var geoData2 = {
						latitude: venuelat,
						longitude: venuelng
					}
					geoAddress.push(geoData2);
				 
				
			 
				ticaMap.initNavigateMap("map", "panel-direction", geoAddress[0].latitude, geoAddress[0].longitude, geoAddress[1].latitude, geoAddress[1].longitude);
			});
		}else{
			alert("Please enter both addresses");
		}
	},
	getCurrentGeolocationData: function(){
			
		 
			geoAddress = [];
			if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                function(position){
				mylat  = position.coords.latitude;
				//console.log(position.coords.latitude);
				mylng = position.coords.longitude;
				if(mylat != null && mylng != null){
				var mylocation = {lat:mylat ,lng:mylng} ;
			
				geocoder.geocode({'latLng': mylocation }, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {  
					marker = new google.maps.Marker({
					position: {lat:mylat ,lng:mylng}, 
					map: map,icon: {                             
					url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"                           
					}
					}); 	
					address= (results[1].formatted_address);
					$('#Address').val(address);
					console.log('address :'+address); 
					} else {
					  //alert("No results found");
					}
				  } else {
					//alert("Geocoder failed due to: " + status);
				  }
					
				});
				var geoData1 = {
					latitude: position.coords.latitude,
					longitude: position.coords.longitude
				}
				/*
				var geoData1 = {
					latitude: 40.45918900,
					longitude: -75.29325430
				}
				*/
				geoAddress.push(geoData1);
					
				var geoData2 = {
					latitude: venuelat,
					longitude: venuelng
				}
				geoAddress.push(geoData2);	
				ticaMap.initNavigateMap("map", "panel-direction", geoAddress[0].latitude, geoAddress[0].longitude, geoAddress[1].latitude, geoAddress[1].longitude,address);
				}else{
				console.log(geoAddress[0].latitude);
				console.log(geoAddress[0].longitude);
					
				ticaMap.initMapOnly("map",venuelat,venuelng,geoAddress[0].latitude, geoAddress[0].longitude);
					
				}
				}, 
				function(e){
					alert('Can not find your location');
					ticaMap.clearEntries();
					ticaMap.initMapOnly("map",venuelat,venuelng);
				});
 
                
            } else {
                alert("Geolocation is not supported by this browser.");
            }
			 
			//geoAddress.push(geoData);
 				 
					
				 
				
			 
 
	},
	initMapOnly:function(mapID, lat, lng, mylat = '', mylng = ''){
		 map = new google.maps.Map(document.getElementById(mapID), {
		  zoom: 7,
		  center: {lat: lat, lng:lng}, 
		  mapTypeControlOptions: {mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain','styled_map']}
		}); 
		marker = new google.maps.Marker({
					position: {lat:lat ,lng:lng}, 
					map: map 
				  }); 
		 
		if(mylat && mylng ){
			console.log('mylat :'+mylat);
			console.log('mylng :'+mylng);
			
			mymarker = new google.maps.Marker({
					position: {lat:mylat ,lng:mylng}, 
					map: map 
			});	 
		}
		 
			
		 
		
		
	},	
	//clear entries and map display
	clearEntries: function(){
		$("#Address").val("");
		$("#map, #panel-direction").html("");
	}
}
	var maploaded = false;
    setMapHeight();
    window.setInterval(function(){
	if((typeof venuelat === 'undefined' || typeof venuelng === 'undefined') || maploaded) 
	return;
	else{
	maploaded = true;
	ticaMap.getCurrentGeolocationData();	
	}  		
	}, 500);
    
    
    
    $('#clr_btn').on('click',function(){
		ticaMap.clearEntries();
		ticaMap.initMapOnly("map",venuelat,venuelng);
		
		
	});
	$('#directions_btn').on('click',function(){
		address = $('#Address').val();
		if(address){
			ticaMap.getGeolocationData();
			
		}else{
			
			
		}
		
	});
</script>
    
   
    
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    
