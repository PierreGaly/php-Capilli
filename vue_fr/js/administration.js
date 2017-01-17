$(function()
{
	$('.bouton_slide_toggle').click(function()
	{
		$('#categorie_list_' + $(this).attr('data-id')).stop().slideToggle('linear');
		
		if($(this).html() == '<span class="glyphicon glyphicon-plus"></span>')
			$(this).html('<span class="glyphicon glyphicon-minus"></span>');
		else
			$(this).html('<span class="glyphicon glyphicon-plus"></span>');
		
		return false;
	});
	
	$('.categorie_modifier').click(function()
	{
		var id = $(this).attr('data-id');
		
		$('#modifier_cat_name').val($('#name_cat_' + id).text());
		$('#modifier_cat_ordre').val($('#ordre_cat_' + id).val());
		$('#modifier_cat_id').val(id);
		
		$('#modal_categorie').modal('show');
		
		return false;
	});
	
	$('.sous_categorie_modifier').click(function()
	{
		var id = $(this).attr('data-id');
		
		$('#modifier_sous_cat_name').val($('#name_sous_cat_' + id).text());
		$('#modifier_sous_cat_ordre').val($('#ordre_sous_cat_' + id).text());
		$('#modifier_sous_cat_image').attr('src', $('#image_sous_cat_' + id).val());
		$('#modifier_sous_cat_id').val(id);
		
		$('#modal_sous_categorie').modal('show');
		
		return false;
	});
	
	function delete_class(id)
	{
		$('#' + id).removeClass('list-group-item-info');
	}
	
	var client = new ZeroClipboard($('.copy_link')), id;
	
	client.on('ready', function( readyEvent )
	{
		client.on('copy', function(event)
		{
			id = $(event.target).attr('id');
		});
		
		client.on('aftercopy', function(event)
		{
			$('#' + id).addClass('list-group-item-info');//.removeClass('list-group-item-info');
			
			setTimeout(delete_class, 500, id);
		});
	});
});