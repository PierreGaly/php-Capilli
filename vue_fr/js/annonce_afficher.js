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
	
    $('#date_picker2').datetimepicker({
        useCurrent: false, //Important! See issue #1075
        minDate: moment(),
        locale: moment.locale('fr'),
		format: "DD/MM/YYYY",
        widgetPositioning: {
			horizontal: 'right',
			vertical: 'top'
			}
	/*	locale: {
			format: "DD/MM/YYYY",
			firstDay: 1,
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			daysOfWeek: ['Dim', 'Lun', 'Ma', 'Me', 'Je', 'Ven', 'Sa']
		},*/
    }).val('');
    
    $('#date_picker3').datetimepicker({
        useCurrent: false, //Important! See issue #1075
        minDate: moment(),
        locale: moment.locale('fr'),
		format: "DD/MM/YYYY",
        widgetPositioning: {
			horizontal: 'right',
			vertical: 'top'
			}
    }).val('');
    
    
    
    $("#date_picker2").on("dp.change", function (e) {
        $('#date_picker3').data("DateTimePicker").minDate(e.date);
        update_price2();
    });
    
    $("#date_picker3").on("dp.change", function (e) {
        $('#date_picker2').data("DateTimePicker").maxDate(e.date);
        update_price2();
    });
	
	function update_price2()
	{
		if($('#date_picker2').val() !== '' && $('#date_picker3').val() !== '')
		{
			var d1 = $('#date_picker2').val(), d2 = $('#date_picker3').val();
			
			$('#dates_commande').val(d1 + ' - ' + d2);
			$('#prix_span').html('<span style="color: grey; font-style: italic; font-size: 0.7em;">Calcul du prix...</span>');
			
			$.ajax({
				url: 'ajax.php?annonce&id=' + $('#ID_objet').val() + '&q=' + encodeURIComponent($('#quantite_commande').val()) + '&d1=' + encodeURIComponent(d1) + '&d2=' + encodeURIComponent(d2),
				dataType: "json",
				success: function(prix)
				{
					if(!prix[0])
						$('#prix_span').html('<span style="color: grey; font-style: italic; font-size: 0.7em;">Indisponible sur cette période.</span>');
					else
					{
						var prix_total = prix[2];
						
						if(prix[1] > 0)
							prix_total *= prix[1];
						
						$('#prix_span').html('<strong class="rose_custom">' + format_prix(prix_total.toString()) + ' €</strong>');
						
						if(prix[1] == 0)
							$('#prix_span').html($('#prix_span').html() + ' <small style="color: grey;">/ unité</small>');
					}
				},
				error: function()
				{
					$('#prix_span').html('<strong class="rose_custom">' + $('#prix_journee').val() + ' €</strong> <small style="color: grey;">/ jour</small>');
				}
			});
		}
		else
		{
			$('#prix_span').html('<strong class="rose_custom">' + $('#prix_journee').val() + ' €</strong> <small style="color: grey;">/ jour</small>');
			$('#dates_commande').val('');
		}
	}
	
	/*
	$('#date_picker').daterangepicker({
		timePicker24Hour: true,
		autoApply: true,
		startDate: moment(),
		minDate: moment(),
		opens: "left",
		locale: {
			format: "DD/MM/YYYY",
			firstDay: 1,
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			daysOfWeek: ['Dim', 'Lun', 'Ma', 'Me', 'Je', 'Ven', 'Sa']
		}
	}).val('');
	
	function update_price()
	{
		if($('#date_picker').val() != '')
		{
			var d1 = $('#date_picker').val().substr(0, 10), d2 = $('#date_picker').val().substr(13, 10);
			$('#prix_span').html('<span style="color: grey; font-style: italic; font-size: 0.7em;">Calcul du prix...</span>');
			
			$.ajax({
				url: 'ajax.php?annonce&id=' + $('#ID_objet').val() + '&q=' + encodeURIComponent($('#quantite_commande').val()) + '&d1=' + encodeURIComponent(d1) + '&d2=' + encodeURIComponent(d2),
				dataType: "json",
				success: function(prix)
				{
					if(!prix[0])
						$('#prix_span').html('<span style="color: grey; font-style: italic; font-size: 0.7em;">Indisponible sur cette période.</span>');
					else
					{
						var prix_total = prix[2];
						
						if(prix[1] > 0)
							prix_total *= prix[1];
						
						$('#prix_span').html('<strong class="rose_custom">' + format_prix(prix_total.toString()) + ' €</strong>');
						
						if(prix[1] == 0)
							$('#prix_span').html($('#prix_span').html() + ' <small style="color: grey;">/ unité</small>');
					}
				},
				error: function()
				{
					$('#prix_span').html('<strong class="rose_custom">' + $('#prix_journee').val() + ' €</strong> <small style="color: grey;">/ jour</small>');
				}
			});
		}
		else
			$('#prix_span').html('<strong class="rose_custom">' + $('#prix_journee').val() + ' €</strong> <small style="color: grey;">/ jour</small>');
	}
	
	$('#date_picker').on('change', update_price);
	*/
	
	$('.li_quantites').click(function()
	{
		$('#span_quantity').text($(this).text());
		$('#quantite_commande').val($(this).text());
		update_price2();
	});
	
	initialize();
	
	if($('#modal_panier').length)
		$('#modal_panier').modal('show');
});