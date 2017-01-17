$(function()
{
	$('.button_club').click(function()
	{
		var id = $(this).attr('data-id_club');
		var nom = $('#club_' + id).text();
		
		$('#id_club_adhesion').val(id);
		$('#nom_club').text(nom);
	});
	
	if($('#id_club_adhesion').val().length)
		$('#modal_club').modal('show');
});