$(function()
{
	var placeSearch, inscription_adresse_complete;
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
		// Create the inscription_adresse_complete object, restricting the search
		// to geographical location types.
		inscription_adresse_complete = new google.maps.places.Autocomplete(
			/** @type {HTMLInputElement} */
			(document.getElementById('inscription_adresse_complete')), {
					types: ['geocode']
				});
		
		inscription_adresse_complete.addListener('place_changed', fillInAddress);
	}
	
	function fillInAddress()
	{
		// Get the place details from the inscription_adresse_complete object.
		var place = inscription_adresse_complete.getPlace();
		
		$('#inscription_lat').val(place.geometry.location.lat());
		$('#inscription_lng').val(place.geometry.location.lng());
		
		for (var component in componentForm)
		{
			document.getElementById('inscription_' + component).value = '';
			document.getElementById('inscription_' + component).disabled = false;
		}
		
		// Get each component of the address from the place details
		// and fill the corresponding field on the form.
		for (var i = 0; i < place.address_components.length; i++)
		{
			var addressType = place.address_components[i].types[0];
			
			if (componentForm[addressType])
			{
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById('inscription_' + addressType).value = val;
			}
		}
	}
	
	$('#inscription_adresse_complete').change(function()
	{
		$('#inscription_adresse_complete').val('');
		$('#inscription_lat').val('');
		$('#inscription_lng').val('');
		
		for (var component in componentForm)
		{
			$('#inscription_' + component).val('');
		}
	});
	
	$('#inscription_date_naissance').daterangepicker({
		singleDatePicker: true,
		timePicker24Hour: true,
		showDropdowns: true,
		autoApply: true,
		startDate: $('#inscription_date_naissance').val(),
		minDate: '01/01/1900',
		maxDate: moment(),
		opens: "right",
		locale: {
		format: "DD/MM/YYYY",
			firstDay: 1,
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			daysOfWeek: ['Dim', 'Lun', 'Ma', 'Me', 'Je', 'Ven', 'Sa']
		}
	});
	
	initialize_search();
});