$(function()
{
	var placeSearch, inscription_adresse_complete, ecole_name = '';
	
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
	
	function update_ecole_name()
	{
		if($('.bootstrap-select input').val() === '')
		{
			$('#inscription_ecole').html('');
			$('.selectpicker').selectpicker('refresh');
		}
		else if($('.bootstrap-select input').val() !== ecole_name)
		{
			$.ajax({
				url: 'ajax.php?inscription&ecole_name=' + encodeURIComponent($('.bootstrap-select input').val()),
				dataType: "json",
				success: function(donnees)
				{
					if(donnees[0] == ecole_name)
					{
						var tmp = '';
						
						for(var i=0; i<donnees[1].length; i++)
							tmp += '<option value="' + donnees[1][i][0] + '" data-subtext="' + donnees[1][i][2] + '">' + donnees[1][i][1] + '</option>';
						
						$('#inscription_ecole').html(tmp);
						$('.selectpicker').selectpicker('refresh');
					}
				}
			});
		}
		
		ecole_name = $('.bootstrap-select input').val();
	}
	
	$('.selectpicker').on('loaded.bs.select', function()
	{
		$('.bootstrap-select input').on('keyup', update_ecole_name);
	});
	
	$('.selectpicker').on('show.bs.select', function()
	{
		$('.bootstrap-select input').val(ecole_name);
	});
	
	initialize_search();
});