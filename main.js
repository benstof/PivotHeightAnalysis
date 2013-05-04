     
var map;
var elSvc;
var markersArray = [];
var linesArray = [];
var infoArray = [];
var path = [];
var cityCircle;
var glob_pos;
var elHigh;
var elLow;
var elHLoc;
var elLLoc;
var temp_markersArray = [];
var units = 'meters';

function clearTempOverlays() {

 for (var i = 0; i < temp_markersArray.length; i++ ) {
  temp_markersArray[i].setMap(null);
} 

}

function clearOverlays() {

 for (var i = 0; i < markersArray.length; i++ ) {
  markersArray[i].setMap(null);
} 

for (var i = 0; i < infoArray.length; i++ ) {
  infoArray[i].setMap(null);
} 

for (var i = 0; i < linesArray.length; i++ ) {
  linesArray[i].setMap(null);
} 

}


function updatePivot() {

  clearOverlays();


  var image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";

  var marker = new google.maps.Marker({
    map: map,
    icon:image,
    draggable:true,
    position: glob_pos
  });
  markersArray.push(marker);

  google.maps.event.addListener(marker, 'dragend', function() 
  {
    geocodePosition(marker.getPosition());
  });


  elHigh = -100000;
  elLow = 100000;

  var rad = document.getElementById('radius').value;   

      //console.log(units);
      if(units == 'feet'){
       rad = rad / 3.28;
     }
      //console.log(rad);
      document.getElementById('rad_val').value = rad.toString();
      
      var populationOptions = {
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        map: map,
        center: glob_pos, //results[0].geometry.location, //new google.maps.LatLng(-34.397, 150.644),
        radius: parseInt(rad)
      };
      document.getElementById('clocation').value = glob_pos.toString();
      pivot = new google.maps.Circle(populationOptions);
      markersArray.push(pivot);
      
      var origin = pivot.center;
      //alert(origin.toString());

      $('#coords').html('<br/>'+origin.toString());
      
      for (var i = 0; i < 36; i++ ) {


        var endpoint = google.maps.geometry.spherical.computeOffset(pivot.center,pivot.radius,i*10);
        path.push(endpoint);
      //console.log(endpoint);
      
      var samplesize = rad / 5;
      //alert(samplesize);
      
      var radiusLineSettings = {
        clickable: false,
        map: map,
        strokeColor: "#000000",
        strokeWeight: 0.5,
        path: [origin,endpoint]
      };

      var pathRequest = {
        'path': [origin,endpoint],
        'samples':5
      }
      elSvc.getElevationAlongPath(pathRequest, plotElevations);


      var pathRequest = {
        'path': [origin,origin],
        'samples':5
      }
      elSvc.getElevationAlongPath(pathRequest, plotCenter);

  //pivotRadiusLine = new google.maps.Polyline(radiusLineSettings);   
  //linesArray.push(pivotRadiusLine); 


}

}




function plotCenter(results, status){

  	//console.log('kjk');
  	if(status == google.maps.ElevationStatus.OK){

  		elevations = results;
  		elevation = elevations[0].elevation.toFixed(1);

     if(units == 'feet'){
       elevation = (elevation * 3.28).toFixed(1);
     }

   }


   $("#cpoint").html(elevation);
   document.getElementById('cpoint_val').value = elevation.toString();

 }

 function plotElevations(results, status){


   if(status == google.maps.ElevationStatus.OK){

    elevations = results;

    for (var i = 0; i < results.length; i++ ) {

      if(units == 'feet'){
        elevations[i].elevation = elevations[i].elevation * 3.28;
      }
  		    //console.log(elevations[i].elevation);
  		    
  		    if(elevations[i].elevation > elHigh) {
           elHigh = elevations[i].elevation.toFixed(1);
           elHLoc = elevations[i].location;
         }

         if(elevations[i].elevation < elLow) {
           elLow = elevations[i].elevation.toFixed(1);
           elLLoc = elevations[i].location;
         }

       }

     }

     clearTempOverlays();


     var image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";

     var marker = new google.maps.Marker({
      map: map,
      draggable : false,
      position : elHLoc,
      icon : image
    });
     temp_markersArray.push(marker);

     var image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";

     var marker = new google.maps.Marker({
      map: map,
      draggable :false,
      position : elLLoc,
      icon : image
    });
     temp_markersArray.push(marker);


     $("#hpoint").html(elHigh);
     $("#lpoint").html(elLow);
     document.getElementById('hpoint_val').value = elHigh.toString();
     document.getElementById('lpoint_val').value = elLow.toString();     
     


   }


   function geocodePosition(pos) 
   {
    glob_pos = pos;
    elHigh = -100000;
    elLow = 100000;

  //document.getElementById('address').value = glob_pos.toString();


  var rad = document.getElementById('radius').value;
  geocoder.geocode
  ({
    latLng: pos
  }, 
  function(results, status) 
  {
    if (status == google.maps.GeocoderStatus.OK) 
    {
      updatePivot();
    } 
    else 
    {
                  //$("#mapErrorMsg").html('Cannot determine address at this location.'+status).show(100);
                }
              }
              );
}

function updateUnits(){

 units = $("input[name=units]:checked").attr('id');
 document.getElementById('un_val').value = units;

 if(units == 'feet'){
  $(".units_lbl").html('ft');  
} else {
  $(".units_lbl").html('m');  
}

updatePivot();
}

function codeAddress() {



  units = $("input[name=units]:checked").attr('id');

  document.getElementById('un_val').value = units;

  if(units == 'feet'){
    $(".units_lbl").html('ft');  
  } else {
    $(".units_lbl").html('m');  
  }
  

  clearOverlays();

  elSvc = new google.maps.ElevationService();

  rad = document.getElementById('radius').value;
  elHigh = -100000;
  elLow = 100000;

  //adt = $("input[name=adt]:checked").attr('id');
  adt = $('input.adt:checked').attr('id');



if(adt == 'address_rb' || adt == 'current_rb'){


  address = document.getElementById('address_text').value;



  if (adt == 'current_rb') {

    address = current_addr; 
    //console.log('current'); 

  } 


  //console.log(address);


  geocoder.geocode( { 'address': address}, function(results, status) {

    if (status == google.maps.GeocoderStatus.OK) {

      map.setCenter(results[0].geometry.location);
      glob_pos = results[0].geometry.location; 

//console.log('jjj');
      updatePivot();
      

    } else {

    //alert('Geocode was not successful for the following reason: ' + status);

   }

  });


} else {

  var lat_val = document.getElementById('lat_address').value;
  var lng_val = document.getElementById('lng_address').value;

  //address = '41.072683, -97.372842';
  ltlng = new google.maps.LatLng(lat_val, lng_val);
  glob_pos = ltlng;
  
  map.setCenter(ltlng);

  updatePivot();

}


} 


function updateAdt() {

  //adt = $("input[name=adt]:checked").attr('id');
  adt = $('input.adt:checked').attr('id');

  if(adt == 'address_rb'){
   $("#address_text").prop('disabled', false);
   $("#lng_address").prop('disabled', true);
   $("#lat_address").prop('disabled', true);

 }

 if(adt == 'location_rb'){
   $("#address_text").prop('disabled', true);
   $("#lng_address").prop('disabled', false);
   $("#lat_address").prop('disabled', false);
 }

}



/*
 if(adt == 'current_rb'){


   document.getElementById('address').value = '';


     $("#address").prop('disabled', true);
     $("#lng_address").prop('disabled', true);
     $("#lat_address").prop('disabled', true);


if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(successo, erroro);
} else {
  alert('geolocation not supported');
}

function successo(position) {
  //alert(position.coords.latitude + ', ' + position.coords.longitude);

  $("#lat_address").val(position.coords.latitude);
  $("#lng_address").val(position.coords.longitude);

  this_lat = position.coords.latitude;
  this_lng = position.coords.longitude;

  current_addr = position.coords.latitude +', '+ position.coords.longitude;
  codeAddress();
}

function erroro(msg) {
  alert('error: ' + msg);
}


  } // Update current 


}
*/


function aggree_to_terms() {

  $.cookie('disclaimer', 'y');

  // Hide disclaimer
  $('#disclaimer_box').hide();

}

function initialize() {


  $("#aggree_btn").click(function() {
   aggree_to_terms();
 });


  var aggree = $.cookie('disclaimer');
  if (aggree != 'y') {
    // Show disclaimer
    $('#disclaimer_box').fadeIn();

  }


geocoder = new google.maps.Geocoder();

this_lat = 28.542180934818862;
this_lng = -81.6891067430725;

  var mapOptions = {
    center: new google.maps.LatLng(this_lat, this_lng),
    zoom: 18,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

    map = new google.maps.Map(document.getElementById("map_canvas"),
    mapOptions);


  codeAddress();

}