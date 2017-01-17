<?php
$membres_manager = new MembresMan($bdd);

$erreurs = $infos['erreurs'];
$proposition = $infos['communaute_proposition'];
?>
<div class="row">
	<div class="row_content">
		<h2>Créer un club</h2>
		
		<?php
		if($erreurs)
		{
			if($erreurs['nom'])
				echo '<div class="alert alert-danger text-center" role="alert">Le nom doit contenir entre ' . CommunautesMan::TAILLE_MIN_NOM . ' et ' . CommunautesMan::TAILLE_MAX_NOM . ' caractères.</div>';
			else if($erreurs['description'])
				echo '<div class="alert alert-danger text-center" role="alert">La description doit contenir entre ' . CommunautesMan::TAILLE_MIN_DESCRIPTION . ' et ' . CommunautesMan::TAILLE_MAX_DESCRIPTION . ' caractères.</div>';
			else if($erreurs['image'])
				echo '<div class="alert alert-danger text-center" role="alert">L\'image est incorrecte.<br /><strong>Erreur retournée :</strong> <em>' . htmlspecialchars($erreurs['image']) . '</em>.</div>';
		}
		else if(isset($_POST['communaute_nom']))
			echo '<div class="alert alert-danger text-center" role="alert">Veuillez sélectionner un choix pour l\'image du club.</div>';
		?>
		
		<div class="row">
			<div class="formulaire col-md-offset-2 col-md-8">
				<form class="form-horizontal" role="form" enctype="multipart/form-data" action="" method="post">
					<div class="form-group">
						<label class="control-label col-sm-4">Proposé par</label>
						<div class="col-sm-8 text-center">
							<?php echo $membres_manager->getMembreByID($proposition->ID_membre)->sePresenter(); ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="communaute_nom">Nom</label>
						<div class="col-sm-8">
							<input class="form-control" id="communaute_nom" name="communaute_nom" placeholder="Nom du club" maxlength="<?php echo CommunautesMan::TAILLE_MAX_NOM; ?>" required value="<?php
							if(isset($_POST['communaute_nom']) && !$erreurs['nom'])
								echo htmlspecialchars($_POST['communaute_nom']);
							else
								echo htmlspecialchars($proposition->nom);
							?>"></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="communaute_description">Description</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="communaute_description" name="communaute_description" maxlength="<?php echo CommunautesMan::TAILLE_MAX_DESCRIPTION; ?>" placeholder="Description du club" required><?php
							if(isset($_POST['communaute_description']))
								echo htmlspecialchars($_POST['communaute_description']);
							else
								echo htmlspecialchars($proposition->description);
							?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4">Image</label>
						<div class="col-sm-8 form-inline">
							<input type="radio" name="communaute_image" id="communaute_image_new" value="new"> <label for="communaute_image_new">Nouvelle image :</label><input type="file" class="form-control" name="communaute_image_new"><?php
							if($proposition->image != '')
							{
							?>
								<br /><input type="radio" name="communaute_image" id="communaute_image_same" value="same" <?php if(!isset($_POST['communaute_image']) || $_POST['communaute_image'] != 'same') echo 'checked'; ?>> <label for="communaute_image_same">Conserver l'image proposée : </label><img style="max-width: 100%;" src="<?php echo IMAGES_COMMUNAUTES . $proposition->image; ?>" alt="">
							<?php
							}
							?>
						</div>
					</div>
					
					<hr />
					
					<div class="form-group" style="text-align: center; padding: 0; margin: 0;">
						<button class="btn btn-custom" role="submit">Créer le club <span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
