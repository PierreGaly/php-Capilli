<?php
$erreurs = $infos['erreurs'];
?>
<div class="row">
	<div class="row_content">
		<h2>Proposer un nouveau club</h2>
		
		<?php
		if(isset($_SESSION['communaute_proposed']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Merci d\'avoir proposé le club <em>' . htmlspecialchars($_SESSION['communaute_proposed']) . '</em>.<br />Vous serez informé sous peu de son acceptation par les administrateurs.</div>';
			unset($_SESSION['communaute_proposed']);
		}
		else
		{
			if($erreurs)
			{
				if($erreurs['nom'])
					echo '<div class="alert alert-danger text-center" role="alert">Le nom doit contenir entre ' . CommunautesMan::TAILLE_MIN_NOM . ' et ' . CommunautesMan::TAILLE_MAX_NOM . ' caractères.</div>';
				else if($erreurs['description'])
					echo '<div class="alert alert-danger text-center" role="alert">La description doit contenir entre ' . CommunautesMan::TAILLE_MIN_DESCRIPTION . ' et ' . CommunautesMan::TAILLE_MAX_DESCRIPTION . ' caractères.</div>';
				else if($erreurs['image'])
					echo '<div class="alert alert-danger text-center" role="alert">L\'image est incorrecte.<br /><strong>Erreur retournée :</strong> <em>' . htmlspecialchars($erreurs['image']) . '</em>.</div>';
			}
			?>
			
			<div class="row">
				<div class="formulaire col-md-offset-2 col-md-8">
					<form class="form-horizontal" role="form" enctype="multipart/form-data" action="" method="post">
						<div class="form-group">
							<label class="control-label col-sm-4" for="communaute_nom">Nom</label>
							<div class="col-sm-8">
								<input autofocus class="form-control" id="communaute_nom" name="communaute_nom" placeholder="Nom du club" maxlength="<?php echo CommunautesMan::TAILLE_MAX_NOM; ?>" required<?php
								if(isset($_POST['communaute_nom']) && !$erreurs['nom'])
									echo ' value="' . htmlspecialchars($_POST['communaute_nom']) . '"';
								?>>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-4" for="communaute_description">Description</label>
							<div class="col-sm-8">
								<textarea class="form-control" id="communaute_description" name="communaute_description" placeholder="Description du club" maxlength="<?php echo CommunautesMan::TAILLE_MAX_DESCRIPTION; ?>" required><?php
								if(isset($_POST['communaute_description']))
									echo htmlspecialchars($_POST['communaute_description']);
								?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-4" for="communaute_image">Image <small class="help-block">Facultatif</small></label>
							<div class="col-sm-8">
								<input type="file" class="form-control" id="communaute_image" name="communaute_image"<?php
								if(isset($_POST['communaute_image']) && !$erreurs['image'])
									echo ' value="' . $_POST['communaute_image'] . '"';
								?>>
							</div>
						</div>
						
						<hr />
						
						<div class="form-group" style="text-align: center; padding: 0; margin: 0;">
							<button class="btn btn-custom" role="submit">Proposer le club <span class="glyphicon glyphicon-chevron-right"></span></button>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
