<?php
$erreurs = $infos['erreurs'];
?>
<div class="row">
	<div class="row_content">
		<h2 style="margin-top:100px">Je m'inscris en tant que <strong>professionnel</strong><span style="color:white" class="small"><br />en 2 minutes</span></h2>

		<?php
		if($erreurs)
		{
			if($erreurs['nom'])
				echo '<div class="alert alert-danger text-center" role="alert">La raison sociale <em>' . htmlspecialchars($_POST['inscription_nom']) . '</em> est incorrect.</div>';
			else if($erreurs['adresse_complete'])
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse postale <em>' . htmlspecialchars($_POST['inscription_adresse_complete']) . '</em> est incorrecte.</div>';
			else if($erreurs['tel_portable'])
				echo '<div class="alert alert-danger text-center" role="alert">Le téléphone n°1 <em>' . htmlspecialchars($_POST['inscription_tel_portable']) . '</em> est incorrect.</div>';
			else if($erreurs['tel_fixe'])
				echo '<div class="alert alert-danger text-center" role="alert">Le téléphone n°2 <em>' . htmlspecialchars($_POST['inscription_tel_fixe']) . '</em> est incorrect.</div>';
			elseif($erreurs['email_valide'])
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail <em>' . htmlspecialchars($_POST['inscription_email']) . '</em> est invalide.</div>';
			else if($erreurs['email_existe'])
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail <em>' . htmlspecialchars($_POST['inscription_email']) . '</em> est déjà utilisée.</div>';
			else if($erreurs['mdp_correspondance'])
				echo '<div class="alert alert-danger text-center" role="alert">Les mots de passe ne correspondent pas.</div>';
			else if($erreurs['mdp'])
				echo '<div class="alert alert-danger text-center" role="alert">Le mot de passe est incorrect.</div>';
			else if($erreurs['avatar'])
				echo '<div class="alert alert-danger text-center" role="alert">Le logo est incorrect. Erreur retournée : <em>' . htmlspecialchars($erreurs['avatar']) . '</em></div>';
			else if($erreurs['parrain'])
			{
				echo '<div class="alert alert-danger text-center" role="alert">Votre lien de parrainage est invalide.</div>';

				unset($_SERVER['inscription_parrain']);
			}
			else if($erreurs['charte'])
				echo '<div class="alert alert-danger text-center" role="alert">Vous devez accepter les conditions d\'utilisation.</div>';
			else if($erreurs['lemon'])
				echo LemonWay::displayErrorMessage($erreurs['lemon']);
		}
		?>

		<div class="row">
			<div class="formulaire col-md-offset-2 col-md-8">
			<form class="form-horizontal" role="form" enctype="multipart/form-data" action="" method="post">
				<fieldset>
					<legend style="color:#FF0040">Informations sur l'entreprise</legend>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_nom">Raison sociale</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="inscription_nom" name="inscription_nom" placeholder="Raison sociale" required autofocus<?php
							if(isset($_POST['inscription_nom']) && !$erreurs['nom'])
								echo ' value="' . $_POST['inscription_nom'] . '"';
							?>>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_adresse_complete">Adresse postale</label>
						<div class="col-sm-8 has-feedback">
							<input type="text" class="form-control" id="inscription_adresse_complete" name="inscription_adresse_complete" placeholder="Adresse" required<?php
							if(isset($_POST['inscription_adresse_complete']) && !$erreurs['adresse_complete'])
								echo ' value="' . $_POST['inscription_adresse_complete'] . '"';
							?>>
							<i class="glyphicon glyphicon-map-marker form-control-feedback" style="position: absolute; right: 20px;"></i>
						</div>
					</div>

					<input type="hidden" id="inscription_street_number" name="inscription_street_number" required<?php
					if(isset($_POST['inscription_street_number']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_street_number'] . '"';
					?>>

					<input type="hidden" id="inscription_route" name="inscription_route" required<?php
					if(isset($_POST['inscription_route']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_route'] . '"';
					?>>

					<input type="hidden" id="inscription_locality" name="inscription_locality" required<?php
					if(isset($_POST['inscription_locality']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_locality'] . '"';
					?>>

					<input type="hidden" id="inscription_administrative_area_level_1" name="inscription_administrative_area_level_1" required<?php
					if(isset($_POST['inscription_administrative_area_level_1']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_administrative_area_level_1'] . '"';
					?>>

					<input type="hidden" id="inscription_country" name="inscription_country" required<?php
					if(isset($_POST['inscription_country']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_country'] . '"';
					?>>

					<input type="hidden" id="inscription_postal_code" name="inscription_postal_code" required<?php
					if(isset($_POST['inscription_postal_code']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_postal_code'] . '"';
					?>>

					<input type="hidden" id="inscription_lat" name="inscription_lat" required<?php
					if(isset($_POST['inscription_lat']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_lat'] . '"';
					?>>

					<input type="hidden" id="inscription_lng" name="inscription_lng" required<?php
					if(isset($_POST['inscription_lng']) && !$erreurs['adresse_complete'])
						echo ' value="' . $_POST['inscription_lng'] . '"';
					?>>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_tel_portable">Téléphone n°1 <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Restera privé si vous le souhaitez"></span></label>
						<div class="col-sm-8">
							<input type="tel" class="form-control" id="inscription_tel_portable" name="inscription_tel_portable" placeholder="Téléphone n°1" required<?php
							if(isset($_POST['inscription_tel_portable']) && !$erreurs['tel_portable'])
								echo ' value="' . $_POST['inscription_tel_portable'] . '"';
							?>>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_tel_fixe">Téléphone n°2 <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<input type="tel" class="form-control" id="inscription_tel_fixe" name="inscription_tel_fixe" placeholder="Téléphone n°2"<?php
							if(isset($_POST['inscription_tel_fixe']) && !$erreurs['tel_fixe'])
								echo ' value="' . $_POST['inscription_tel_fixe'] . '"';
							?>>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<legend style="color:#FF0040">Votre compte</legend>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_email">Adresse mail</label>
						<div class="col-sm-8">
							<input type="email" class="form-control" id="inscription_email" name="inscription_email" placeholder="Adresse mail" required<?php
							if(isset($_POST['inscription_email']) && !$erreurs['email_existe'] && !$erreurs['email_valide'])
								echo ' value="' . $_POST['inscription_email'] . '"';
							?>>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_mdp">Mot de passe</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" id="inscription_mdp" name="inscription_mdp" placeholder="Mot de passe" required>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_mdp2">Confirmation du mot de passe</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" id="inscription_mdp2" name="inscription_mdp2" placeholder="Mot de passe" required>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4" for="inscription_avatar">Logo <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<input type="file" class="form-control" id="inscription_avatar" accept="image/*" name="inscription_avatar"<?php
							if(isset($_POST['inscription_avatar']) && !$erreurs['avatar'])
								echo ' value="' . $_POST['inscription_avatar'] . '"';
							?>>
						</div>
					</div>

				</fieldset>

				<hr />

				<div class="form-group form-inline" style="margin: 0; padding: 0; text-align: center;">
					<input type="checkbox" class="form-control" name="inscription_charte" id="inscription_charte">
					<label for="inscription_charte" style="font-size: 0.95em; position: relative; top: 5px; left: 5px;">J'accepte les <a href="documentation.php?tout_sur_club_de_lok#cgu" onclick="window.open(this.href); return false;">conditions d'utilisation</a></label>
				</div>

				<hr />

				<div class="form-group" style="text-align: center; padding: 0; margin: 0;">
					<button class="btn btn-custom" role="submit">Je m'inscris <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div>

		<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp&amp"></script>
	</div>
</div>
