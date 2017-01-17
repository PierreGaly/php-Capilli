$(function()
{
	var localisation, youcansubmit = false;
	
	function initialize_search()
	{
		// Create the localisation object, restricting the search
		// to geographical location types.
		localisation = new google.maps.places.Autocomplete(
			/** @type {HTMLInputElement} */
			(document.getElementById('localisation')), {
					types: ['geocode']
				});
		
		$('#localisation').change(fillInAddress);
		localisation.addListener('place_changed', fillInAddress);
	}
	
	function fillInAddress()
	{
		// Get the place details from the localisation object.
		var place = localisation.getPlace();
		
		if(place != null)
		{
			$('#s_lat').val(place.geometry.location.lat());
			$('#s_lng').val(place.geometry.location.lng());
			
			if(youcansubmit)
				$('#localisation').parents('form').submit();
		}
		else
		{
			youcansubmit = false;
			
			$('#localisation').val('');
			$('#s_lat').val('');
			$('#s_lng').val('');
		}
	}
	
	$(document).keypress(function (e)
	{
		if (e.which == 13)
		{
			if($('#localisation').is(':focus'))
			{
				youcansubmit = true;
				e.preventDefault();
				return false;
			}
		}
	});
	
	initialize_search();
});