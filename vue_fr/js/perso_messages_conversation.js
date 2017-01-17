$(function()
{
	var keyword = '';
	
	function chosen_ajaxify(id, ajax_url)
	{
		$('div#' + id + '_chosen .search-field input').keyup(function()
		{
			if(keyword != $(this).val())
			{
				keyword = $(this).val(), keyword_pattern = new RegExp(keyword, 'gi');
				$('div#' + id + '_chosen ul.chosen-results').empty().append('<li class="no-results">Chargement...</li>');
				
				$.ajax({
					url: ajax_url + encodeURIComponent(keyword),
					dataType: "json",
					success: function(membres)
					{
						if(keyword == $('div#' + id + '_chosen .search-field input').val())
						{
							
							var search_choices = [], values = [], i=0, result=false;
							
							$('div#' + id + '_chosen ul.chosen-choices .search-choice').each(function()
							{
								index = parseInt($(this).find('a').attr('data-option-array-index')) + 1;
								values[i] = $("#"+id+" option:nth-child("+index+")").val();
								search_choices[i] = $(this).find('span').text();
								i++;
							});
							
							$("#"+id).empty();
							i=0;
							
							$('div#' + id + '_chosen ul.chosen-choices .search-choice').each(function()
							{
								$('#'+id).append('<option value="' + values[i] + '" selected>' + search_choices[i] + '</option>');
								i++;
							});
							
							$.each(membres, function(key, membre)
							{
								if($("#"+id).find('option[value=\'' + membre['ID'] + '\']').length == 0)
								{
									$('#'+id).append('<option value="' + membre['ID'] + '">' + membre['prenom'] + ' ' + membre['nom'] + '</option>');
									result = true;
								}
							});
							
							if(!result)
								$('div#' + id + '_chosen ul.chosen-results').append('<li class="no-results">Aucun résultat</li>');
							
							$("#"+id).trigger("chosen:updated");
							
							$('div#' + id + '_chosen').removeClass('chosen-container-single-nosearch');
							$('div#' + id + '_chosen .search-field input').val(keyword);
							$('div#' + id + '_chosen .search-field input').removeAttr('readonly');
							$('div#' + id + '_chosen .search-field input').focus();
							$('div#' + id + '_chosen .search-field input').css('width', '100%');
						}
					}
				});
			}
		});
	}
	
	$('#message').keyup(function()
	{
		if($(this).val() != '')
		{
			if($(this).val().match(/\(?((\d{2})\)?[- .]?){5}/))
				$('#alert_message').css('display', 'block');
			else
				$('#alert_message').css('display', 'none');
			
			$('#messagerie_envoyer').removeAttr('disabled');
		}
		else
		{
			$('#messagerie_envoyer').attr('disabled', '');
			$('#alert_message').css('display', 'none');
		}
	});
	
	$('#participants').chosen({
		no_results_text: 'Aucun résultat',
		width: '100%',
		single_backstroke_delete: false,
		display_selected_options: false,
		search_contains: true,
		enable_split_word_search: false
	});
	
	chosen_ajaxify('participants', 'ajax.php?perso_messages_conversation&c=' + $('#ID_conversation').val() + '&pseudo=');
	
	$('.modal').on('shown.bs.modal', function()
	{
		$(this).find('.search-field input').focus();
	});
});