$(function()
{
	$('#message').keyup(function()
	{
		if($(this).val() == '')
			$('#bouton_envoyer_message').attr('disabled', 'disabled');
		else
			$('#bouton_envoyer_message').removeAttr('disabled');
	});
});