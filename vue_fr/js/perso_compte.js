$(function()
{
	var placeSearch, compte_adresse_complete, youcansubmit=false;
	var componentForm = {
		street_number: 'short_name',
		route: 'long_name',
		locality: 'long_name',
		administrative_area_level_1: 'short_name',
		country: 'long_name',
		postal_code: 'short_name'
	};
	
	function initialize_search()
	{
		// Create the compte_adresse_complete object, restricting the search
		// to geographical location types.
		compte_adresse_complete = new google.maps.places.Autocomplete(
			/** @type {HTMLInputElement} */
			(document.getElementById('compte_adresse_complete')), {
					types: ['geocode']
				});
		
		compte_adresse_complete.addListener('place_changed', fillInAddress);
	}
	
	function fillInAddress()
	{
		// Get the place details from the compte_adresse_complete object.
		var place = compte_adresse_complete.getPlace();
		
		$('#compte_lat').val(place.geometry.location.lat());
		$('#compte_lng').val(place.geometry.location.lng());
		
		for (var component in componentForm)
		{
			document.getElementById('compte_' + component).value = '';
			document.getElementById('compte_' + component).disabled = false;
		}
		
		// Get each component of the address from the place details
		// and fill the corresponding field on the form.
		for (var i = 0; i < place.address_components.length; i++)
		{
			var addressType = place.address_components[i].types[0];
			
			if (componentForm[addressType])
			{
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('compte_' + addressType).value = val;
			}
		}
		
		if(place == null)
			youcansubmit = false;
		else if(youcansubmit)
			$('#compte_adresse_submit_button').click();
	}
	
	$('#compte_adresse_complete').change(function()
	{
		$('#compte_adresse_complete').val('');
		$('#compte_lat').val('');
		$('#compte_lng').val('');
		
		for (var component in componentForm)
		{
			$('#compte_' + component).val('');
		}
	});
	
	$(document).keypress(function (e)
	{
		if (e.which == 13)
		{
			if($('#compte_adresse_complete').is(':focus'))
			{
				youcansubmit = true;
				e.preventDefault();
				return false;
			}
		}
	});
	
	initialize_search();
});