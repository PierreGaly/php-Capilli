$(function()
{
	function initialize()
	{
		var mapCanvas = document.getElementById('map');
		
		var mapOptions = {
			center: new google.maps.LatLng($('#lat_proprio').val(), $('#lng_proprio').val()),
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP
			};
		
		var map = new google.maps.Map(mapCanvas, mapOptions);
		
		var marker_icon = { 
			url: 'sources/logo_marker.png',
			size: new google.maps.Size(40, 33),
			origin: new google.maps.Point(0,0),
			anchor: new google.maps.Point(20, 33)
			};
		
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng($('#lat_proprio').val(), $('#lng_proprio').val()),
			map: map,
			icon: marker_icon
			});
	}
	
	initialize();
});