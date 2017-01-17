$(function()
{
	if($('#reservation_proposer').length)
	{
		$('#reservation_proposer').daterangepicker({
			timePicker24Hour: true,
			autoApply: true,
			startDate: moment(),
			minDate: $('#minDate').val(),
			maxDate: $('#maxDate').val(),
			opens: "center",
			locale: {
				format: "DD/MM/YYYY",
				firstDay: 1,
				monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
				daysOfWeek: ['Dim', 'Lun', 'Ma', 'Me', 'Je', 'Ven', 'Sa']
			}
		}).val('');
	}
	
	/*
	function update_message(choix)
	{
		$('#reservation_choix').val(choix);
		
		var message = '';
		
		if(choix == 1 && $('#reservation_proposer').val().length == 23)
			message = '<div class="alert alert-info text-center" role="alert" id="reservation_alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Vous souhaitez proposer de nouvelles dates : <em>du <strong>' + $('#reservation_proposer').val().substr(0, 10) + '</strong> au <strong>' + $('#reservation_proposer').val().substr(13, 10) + '</strong></em>.</div>';
		else
		{
			$('#reservation_proposer').val('');
			
			if(choix == 0)
				message = '<div class="alert alert-info text-center" role="alert" id="reservation_alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Vous souhaitez <strong>acceptez</strong> la réservation</div>';
			else if(choix == 2)
				message = '<div class="alert alert-danger text-center" role="alert" id="reservation_alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Vous souhaitez <strong>refusez</strong> la réservation</div>';
		}
		
		if(message != '')
		{
			if($('#message_form').html() != '')
				$('#message_form').animate({ opacity: 0 }, 300, "linear", function() { $(this).html(message).animate({ opacity: 1 }, 300, "linear") });
			else
				$('#message_form').html(message).show(300);
			
			$('#reservation_alert').on('close.bs.alert', function()
			{
				update_message(-1);
			});
			
			$('#submit_button').removeAttr('disabled');
		}
		else
		{
			$('#message_form').html(message).hide(300);
			$('#submit_button').attr('disabled', 'disabled');
		}
	}
	
	if($('#reservation_accepter').length > 0)
	{
		$('#reservation_accepter').click(function() { update_message(0) });
		$('#reservation_proposer').change(function() { update_message(1) });
		$('#reservation_refuser').click(function() { update_message(2) });
	}
	*/
	
	function update_modal(choix)
	{
		var titre, body, button, reservation_proposer;
		$('#reservation_choix').val(choix);
		
		if(choix == 0)
		{
			title = 'Acceptation la réservation';
			body = 'Êtes-vous certain de vouloir accepter la réservation ?';
			button = 'Accepter la réservation';
			reservation_proposer = '';
		}
		else if(choix == 1 && $('#reservation_proposer').val().length == 23)
		{
			title = 'Modification la réservation';
			body = 'Êtes-vous certain de vouloir modifier les dates de la réservation : <em>du <strong>' + $('#reservation_proposer').val().substr(0, 10) + '</strong> au <strong>' + $('#reservation_proposer').val().substr(13, 10) + '</strong></em> ?';
			button = 'Modifier la réservation';
			reservation_proposer = $('#reservation_proposer').val();
		}
		else
		{
			title = 'Refus de la réservation';
			body = 'Êtes-vous certain de vouloir refuser la réservation ?';
			button = 'Refuser la réservation';
			reservation_proposer = '';
		}
		
		$('#reservation_proposer_hidden').val(reservation_proposer);
		$('#modal_reservation_titre').html(title);
		$('#modal_reservation_body').html(body);
		$('#modal_reservation_button').html(button);
		
		$('#modal_reservation').modal('show');
	}
	
	if($('#reservation_accepter').length > 0)
	{
		$('#reservation_accepter').click(function() { update_modal(0) });
		$('#reservation_proposer').change(function() { update_modal(1) });
		$('#reservation_refuser').click(function() { update_modal(2) });
	}
	
	$('.star_note_proprio').mouseenter(function()
	{
		var number = $(this).attr('data-number');
		$(this).parent().attr('href', 'reservation.php?id=' + $(this).parent().attr('data-id') + '&np=' + number);
		
		$('.star_note_proprio').each(function()
		{
			if($(this).attr('data-number') <= number)
				$(this).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
		});
	});
	
	$('.star_note_proprio').mouseout(function()
	{
		$(this).parent().attr('href', 'reservation.php?id=' + $(this).parent().attr('data-id'));
		
		$('.star_note_proprio').each(function()
		{
			$(this).removeClass('glyphicon-star').addClass('glyphicon-star-empty');
		});
	});
	
	$('.star_note_bien').mouseenter(function()
	{
		var number = $(this).attr('data-number');
		$(this).parent().attr('href', 'reservation.php?id=' + $(this).parent().attr('data-id') + '&nb=' + number);
		
		$('.star_note_bien').each(function()
		{
			if($(this).attr('data-number') <= number)
				$(this).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
		});
	});
	
	$('.star_note_bien').mouseout(function()
	{
		$(this).parent().attr('href', 'reservation.php?id=' + $(this).parent().attr('data-id'));
		
		$('.star_note_bien').each(function()
		{
			$(this).removeClass('glyphicon-star').addClass('glyphicon-star-empty');
		});
	});
});