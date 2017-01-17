$(function()
{
	var c;
	
	function update_sous_categories()
	{
		c = $('#na_categorie').val();
		$('#na_sous_categorie').html('<option value="">Chargement...</option>');
		
		$.ajax({
			url: 'ajax.php?annonce_nouvelle&c=' + encodeURIComponent($('#na_categorie').val()),
			dataType: "json",
			success: function(donnees)
			{
				if($('#na_categorie').val() == c)
				{
					$('#na_sous_categorie').html('<option value="">Choisissez une sous-catégorie</option>');
					
					for(var i=0;i<donnees.length; i++)
						$('#na_sous_categorie').html($('#na_sous_categorie').html() + '<option value="' + donnees[i][0] + '">' + donnees[i][1] + '</option>');
				}
			},
			error: function()
			{
				$('#na_sous_categorie').html('<option value="">Erreur de connexion</option>');
			}
		});
	}
	
	$('#na_categorie').change(update_sous_categories);
});