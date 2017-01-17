<div class="row">
	<div class="row_content">
		<h2>Renvoyer le mail d'inscription</h2>
		
		<?php
		if(isset($_SESSION['mail_inscription_sent']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Un mail d\'inscription vous a été renvoyé à l\'adresse mail suivante : <em>' . htmlspecialchars($_SESSION['mail_inscription_sent']) . '</em>.</div>';
			unset($_SESSION['mail_inscription_sent']);
		}
		else
		{
			if(isset($_GET['h']))
				echo '<div class="alert alert-danger text-center" role="alert">Le lien de validation de votre compte est incorrect.</div>';
			else
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail de votre compte n\'a pas été validée.</div>';
		}
		?>
		
		<div class="row">
		<form class="form-horizontal" method="post" action="">
			<div class="col-md-offset-3 col-md-6 formulaire">
				<div class="form-group">
					<label class="col-xs-12 col-md-6 control-label" for="email_connec3">Adresse mail</label>
					<div class="col-xs-12 col-md-6">
						<input required id="email_connec3" type="text" class="form-control" name="email_connec3" ng-model="Email" autofocus maxlength="<?php echo MembresMan::TAILLE_MAX_EMAIL; ?>" value="<?php echo $_GET['v']; ?>">
						<span class="help-block">Entre <?php echo MembresMan::TAILLE_MIN_EMAIL; ?> et <?php echo MembresMan::TAILLE_MAX_EMAIL; ?> caractères.</span>
					</div>
				</div>
				
				<hr style="margin: 5px 0 15px 0;" />
				
				<div class="form-group">
					<div class="col-xs-12 col-md-12">
						<button type="submit" id="submit_button" class="btn btn-custom center-block">Renvoyer le mail d'inscription <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
					</div>
				</div>
			</div>
		</form>
		</div>
		
		<div class="row">
			<p class="text-center">
				<a href="connexion.php">Page de connexion</a>
				<br />
				<a href="oubli_mdp.php">Mot de passe oublié</a>
				<br />
				<a href="inscription.php" title="Accéder à l'inscription">Vous n'avez pas encore créé de compte ?</a>
			</p>
		</div>
	</div>
</div>