$(function()
{
	var c;
	
	function update_sous_categories()
	{
		c = $('#ma_categorie').val();
		$('#ma_sous_categorie').html('<option value="">Chargement...</option>');
		
		$.ajax({
			url: 'ajax.php?annonce_nouvelle&c=' + encodeURIComponent($('#ma_categorie').val()),
			dataType: "json",
			success: function(donnees)
			{
				if($('#ma_categorie').val() == c)
				{
					$('#ma_sous_categorie').html('');
					
					for(var i=0;i<donnees.length; i++)
						$('#ma_sous_categorie').html($('#ma_sous_categorie').html() + '<option value="' + donnees[i][0] + '">' + donnees[i][1] + '</option>');
				}
			},
			error: function()
			{
				$('#ma_sous_categorie').html('<option value="">Erreur de connexion</option>');
			}
		});
	}
	
	$('.thumbnail').click(function()
	{
		$('#button_photo_principale').attr('href', 'annonce.php?id=' + $('#id_annonce').val() + '&edit&pp=' + encodeURIComponent($(this).attr('data-filename'))).removeAttr('disabled');
		$('#button_photo_supprimer').attr('href', 'annonce.php?id=' + $('#id_annonce').val() + '&edit&ps=' + encodeURIComponent($(this).attr('data-filename'))).removeAttr('disabled');
		return false;
	});
	
	$('#ma_categorie').change(update_sous_categories);
});