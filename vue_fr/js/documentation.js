$(function()
{
	var index = document.URL.indexOf('#');
	
	$('.bouton_slide_toggle').click(function()
	{
		$('#' + $(this).attr('data-url')).stop().slideToggle('linear');
		
		if($(this).html() == '<span class="glyphicon glyphicon-plus"></span>')
			$(this).html('<span class="glyphicon glyphicon-minus"></span>');
		else
			$(this).html('<span class="glyphicon glyphicon-plus"></span>');
		
		return false;
	});
	
	function open_sous_section(id)
	{
		$('#' + id).stop().show();
		$('[data-url=\'' + id + '\']').html('<span class="glyphicon glyphicon-minus"></span>');
	}
	
	if(index == -1)
		open_sous_section($('[data-url]').first().attr('data-url'));
	else
		open_sous_section(document.URL.substr(index+1));
});