$(function()
{
	var placeSearch, localisation, markers = [], markers_object = [], index2proprios = [], marker_open = [], infowindow = [], infowindow_open = [], window_liste = [], markers_number = [], index=0, map, c, youcansubmit = false;
	
	function initialize_maps()
	{
		var bounds = new google.maps.LatLngBounds();
		var mapCanvas = document.getElementById('map');
		var mapOptions = {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng($('#default_s_lat').val(), $('#default_s_lng').val()),
			zoom: 6
			};
		var marker_icon = { 
			url: 'sources/logo_marker.png',
			size: new google.maps.Size(40, 33),
			origin: new google.maps.Point(0,0),
			anchor: new google.maps.Point(20, 33)
			};
		var marker_icon_gris = { 
			url: 'sources/logo_marker_gris.png',
			size: new google.maps.Size(40, 33),
			origin: new google.maps.Point(0,0),
			anchor: new google.maps.Point(20, 33)
			};
		
		map = new google.maps.Map(mapCanvas, mapOptions);
		
		$('input[type=\'hidden\'][name^=\'lat_\']').each(function()
		{
			var id = $(this).attr('name').substr(4), marker, id_proprio, i;
			
			if($('input[name=\'lng_' + id + '\']').length)
			{
				marker = new google.maps.LatLng($(this).val(), $('input[name=\'lng_' + id + '\']').val());
				bounds.extend(marker);
				id_proprio = $('#proprio_objet_' + id).val();
				
				for(i=0; i<index; i++)
				{
					if(index2proprios[i] == id_proprio)
						break;
				}
				
				if(i == index)
				{
					index2proprios[index] = id_proprio;
					marker_open[index] = false;
					markers_object[index] = new Array(id);
					markers[index] = new google.maps.Marker({
						position: marker,
						map: map,
						icon: marker_icon
						});
					
					index++;
				}
				else
					markers_object[i].push(id);
			}
		});
		
		if (index > 1)
			map.fitBounds(bounds);
		else if(index == 1)
		{
			map.setCenter(bounds.getCenter());
			map.setZoom(14);
		}
		
		for(var i=0; i<index; i++)
		{
			infowindow[i] = new google.maps.InfoWindow({
				content: '<div>Cliquez pour découvrir la liste des annonces</div>',
				disableAutoPan: false
			});
			
			var string = '';
			
			for(var k=0; k<markers_object[i].length; k++)
				string += $('#div_content_objet_' + markers_object[i][k]).html();
			
			window_liste[i] = new google.maps.InfoWindow({
				content: '<div style="min-width: 450px; overflow: hidden;">' + string + '</div>',
				disableAutoPan: false
			});
			
			infowindow_open[i] = false;
			markers_number[i] = 0;
			
			markers[i].addListener('mouseover', function()
			{
				var j;
				
				for(j=0; j<index; j++)
				{
					if(markers[j] == this)
						break;
				}
				
				if(!marker_open[j])
				{
					infowindow[j].open(map, this);
					infowindow_open[j] = true;
				}
			});
			
			markers[i].addListener('mouseout', function()
			{
				var j;
				
				for(j=0; j<index; j++)
				{
					if(markers[j] == this)
						break;
				}
				
				infowindow[j].close(map, this);
				infowindow_open[j] = false;
			});
			
			markers[i].addListener('click', function()
			{
				var key;
				
				for(var j=0; j<index; j++)
				{
					if(markers[j] == this)
						key = j;
					else
					{
						window_liste[j].close(map, this);
						marker_open[j] = false;
					}
				}
				
				if(!marker_open[key])
				{
					if(infowindow_open[key])
						infowindow[key].close(map, this);
					
					window_liste[key].open(map, this);
					marker_open[key] = true;
					markers[key].setIcon(marker_icon_gris)
					
					google.maps.event.addListener(window_liste[key],'closeclick',function(e)
					{
						var k;
						
						for(k=0; k<index; k++)
						{
							if(window_liste[k] == this)
								break;
						}
						
						marker_open[k] = false;
					});
				}
			});
		}
	}
	
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
	
	function resize_map()
	{
		if($('#annonces').css('overflow-y') != 'auto')
		{
			$('#annonces').height('');
			$('#map').height('');
		}
		else
		{
			$('#annonces').height($(window).height() - $('#annonces').position().top);
			$('#map').height($(window).height() - $('#map').position().top);
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
	
	$('.ligne_objet').mouseenter(function (id)
	{
		for(var i=0; i<markers.length; i++)
		{
			for(var j=0; j<markers_object[i].length; j++)
			{
				if(markers_object[i][j] == $(this).attr('data-id'))
					break;
			}
			
			if(j == markers_object[i].length)
				markers[i].setAnimation(null);
			else
			{
				markers_number[i] += 1;
				markers[i].setAnimation(google.maps.Animation.BOUNCE);
			}
		}
	});
	
	$('.ligne_objet').mouseleave(function (id)
	{
		for(var i=0; i<markers.length; i++)
		{
			for(var j=0; j<markers_object[i].length; j++)
			{
				if(markers_object[i][j] == $(this).attr('data-id'))
					break;
			}
			
			if(j < markers_object[i].length)
			{
				if(markers_number[i] > 0)
					markers_number[i] -= 1;
				
				if(markers_number[i] <= 0)
					markers[i].setAnimation(null);
			}
		}
	});
	
	function update_sous_categories()
	{
		c = $('#c').val();
		
		if(c == -1)
		{
			$('#bloc_sous_categories').css('display', 'none');
			$('#sc').html('<option value="-1">Toutes les sous-catégories</option>');
		}
		else
		{
			$('#sc').html('<option value="-1">Chargement...</option>');
			
			$.ajax({
				url: 'ajax.php?annonce_nouvelle&c=' + encodeURIComponent($('#c').val()),
				dataType: "json",
				success: function(donnees)
				{
					if($('#c').val() == c)
					{
						$('#sc').html('<option value="-1">Toutes les sous-catégories</option>');
						
						for(var i=0;i<donnees.length; i++)
							$('#sc').html($('#sc').html() + '<option value="' + donnees[i][0] + '">' + donnees[i][1] + '</option>');
						
						$('#bloc_sous_categories').css('display', 'block');
					}
				},
				error: function()
				{
					$('#sc').html('<option value="-1">Erreur de connexion</option>');
				}
			});
		}
	}
	
	$('#c').change(update_sous_categories);
	
	$(window).resize(resize_map);
	
	initialize_maps();
	initialize_search();
	resize_map();
});