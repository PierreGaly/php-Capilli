<div class="row">
	<div class="row_content">
		<h2>Mot de passe oublié</h2>
		
		<?php
		if(isset($_SESSION['mdp_sent']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Un mail contenant votre nouveau mot de passe vous a été envoyé à l\'adresse : <em>' . htmlspecialchars($_SESSION['mdp_sent']) . '</em>.</div>';
			echo '<p class="text-center"><a href="connexion.php" class="btn btn-custom">Page de connexion <span class="glyphicon glyphicon-chevron-right"></span></a></p>';
			unset($_SESSION['mdp_sent']);
		}
		else
		{
			if(isset($_POST['oubli_mdp_email']))
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail est incorrecte.</div>';
			?>
			<div class="row">
			<form class="form-horizontal" method="post" action="">
				<div class="col-md-offset-3 col-md-6 formulaire">
					<div class="form-group">
						<label class="col-xs-12 col-md-6 control-label" for="oubli_mdp_email">Adresse mail</label>
						<div class="col-xs-12 col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
								<input required id="oubli_mdp_email" type="text" class="form-control" name="oubli_mdp_email" ng-model="Email" autofocus maxlength="<?php echo MembresMan::TAILLE_MAX_EMAIL; ?>">
							</div>
							<span class="help-block">Entre <?php echo MembresMan::TAILLE_MIN_EMAIL; ?> et <?php echo MembresMan::TAILLE_MAX_EMAIL; ?> caractères.</span>
						</div>
					</div>
					
					<hr style="margin: 5px 0 15px 0;" />
					
					<div class="form-group">
						<div class="col-xs-12 col-md-12">
							<button type="submit" id="submit_button" class="btn btn-custom center-block">Réinitialiser mon mot de passe <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
						</div>
					</div>
				</div>
			</form>
			</div>
			
			<div class="row">
				<p class="text-center">
					<a href="connexion.php">Page de connexion</a>
					<br />
					<a href="inscription.php" title="Accéder à l'inscription">Vous n'avez pas encore créé de compte ?</a>
					<br />
					<a href="connexion.php?v">Vous n'avez pas reçu le mail d'inscription ?</a>
				</p>
			</div>
		<?php
		}
		?>
	</div>
</div>