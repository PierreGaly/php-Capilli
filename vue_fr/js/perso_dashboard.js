$(function()
{
	var client = new ZeroClipboard($('#copy_link'));
	
	client.on('ready', function( readyEvent )
	{
		client.on('aftercopy', function(event)
		{
			var lien = $('#lien_parrainage').val();
			
			$('#lien_parrainage').addClass('rose_custom').val('Lien copié !');
			setTimeout(function()
			{
				$('#lien_parrainage').removeClass('rose_custom').val(lien);
			}, 1500);
		});
	});
	
	$('#da_photo').change(function()
	{
		$('#da_form_photo').submit();
	});
	
	$('#lien_modifier').mouseover(function()
	{
		$('#photo_modifier').stop(true).animate({ opacity: 0.5 }, 100, 'linear');
		$('#modifier_icon').stop(true).animate({ opacity: 1 }, 100, 'linear');
	});
	
	$('#lien_modifier').mouseout(function()
	{
		$('#photo_modifier').stop(true).animate({ opacity: 1 }, 100, 'linear');
		$('#modifier_icon').stop(true).animate({ opacity: 0 }, 100, 'linear');
	});
});
