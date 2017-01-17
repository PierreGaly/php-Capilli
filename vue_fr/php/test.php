<?php
function convert($chaine)
{
	$caracteres = array('a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
	'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
	'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
	'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
	'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
	'Œ' => 'oe', 'œ' => 'oe',
	'$' => 's');

	$chaine = strtr($chaine, $caracteres);
	$chaine = preg_replace('#[^A-Za-z0-9]+#', '_', $chaine);
	$chaine = trim($chaine, '-');
	$chaine = strtolower($chaine);

	return $chaine;
		}
?>
<div class="row">
	<div class="row_content">
		<h2>Créer des sous-catégories</h2>
		
		<?php
		if(isset($_SESSION['creer_sous_categories']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Les sous-catégories ont été créées.</div>';
			unset($_SESSION['creer_sous_categories']);
		}
		
		if(isset($_POST['texte']) && isset($_POST['ID_categorie']) && isset($_POST['proposer']))
		{
			$sous_categories_names = explode("\r\n", $_POST['texte']);
			
			?>
			<form method="post" action="" class="formulaire col-xs-12">
				<div class="form-group">
					<label class="col-xs-12 col-md-6 control-label" for="ID_categorie">ID catégorie</label>
					<div class="col-xs-12 col-md-6">
						<input class="form-control" type="number" id="ID_categorie" name="ID_categorie" value="<?php echo $_POST['ID_categorie']; ?>">
					</div>
				</div>
				
				<hr />
				
				<fieldset>
			<?php
			
			foreach($sous_categories_names as $key => $sous_categorie_name)
			{
				$sous_categorie_name = trim($sous_categorie_name);
				
				?>
				<div class="form-group">
					<label class="col-xs-12 col-md-4 control-label" for="cat_name_<?php echo $key; ?>">Sous catégorie <?php echo ($key+1); ?></label>
					<div class="col-xs-12 col-md-4">
						<input class="form-control" type="text" id="cat_name_<?php echo $key; ?>" name="cat_name_<?php echo $key; ?>" value="<?php echo $sous_categorie_name; ?>">
					</div>
					<div class="col-xs-12 col-md-4">
						<input class="form-control" type="text" name="cat_image_<?php echo $key; ?>" value="<?php echo convert($sous_categorie_name); ?>.jpg">
					</div>
				</div>
				<?php
			}
			?>
				</fieldset>
				
				<hr />
				
				<div class="form-group">
					<div class="col-xs-12 text-center">
						<button class="btn btn-custom" role="submit" name="creer">Créer les sous-catégories <span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</div>
			</form>
			<?php
		}
		else
		{
		?>
			<form method="post" action="" class="formulaire col-xs-6 col-md-offset-3">
				<fieldset>
					<div class="form-group">
						<label class="col-xs-12 col-md-4 control-label" for="ID_categorie">ID catégorie</label>
						<div class="col-xs-12 col-md-8">
							<input class="form-control" type="number" name="ID_categorie" id="ID_categorie"/>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-md-4 control-label" for="texte">Sous catégories</label>
						<div class="col-xs-12 col-md-8">
							<textarea class="form-control" name="texte" id="texte"></textarea>
						</div>
					</div>
				</fieldset>
				
				<hr />
				
				<div class="form-group">
					<div class="col-xs-12 text-center">
						<button class="btn btn-custom" role="submit" name="proposer">Soumettre <span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</div>
			</form>
		<?php
		}
		?>
	</div>
</div>