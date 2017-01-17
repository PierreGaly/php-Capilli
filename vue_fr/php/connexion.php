<div class="row">
	<div class="row_content">
		<hr style="width:200%; margin-top:100px">
		<h2 >Connexion à <?php echo SITE_NOM; ?></h2>

		<?php
		if(!empty($infos['need_connec']))
			echo '<div class="alert alert-danger text-center" role="alert">Vous devez vous connecter pour pouvoir accéder à cette page.</div>';

		if((isset($_POST['email_connec']) && isset($_POST['mdp_connec'])) || (isset($_POST['email_connec2']) && isset($_POST['mdp_connec2'])))
			echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail ou le mot de passe est incorrect.</div>';
		?>

		<div class="row">
		<form class="form-horizontal" method="post" action="">
			<div class="col-md-offset-3 col-md-6 formulaire">
				<div class="form-group">
					<label class="col-xs-12 col-md-6 control-label" for="email_connec2">Adresse mail</label>
					<div class="col-xs-12 col-md-6">
						<input required id="email_connec2" type="text" class="form-control" name="email_connec2" ng-model="Email" autofocus maxlength="<?php echo MembresMan::TAILLE_MAX_EMAIL; ?>"<?php if(isset($_POST['email']) && empty($this->infos['erreurs']['email'])) echo ' value="' . $_POST['email'] . '"'; ?>>
						<span class="help-block">Entre <?php echo MembresMan::TAILLE_MIN_EMAIL; ?> et <?php echo MembresMan::TAILLE_MAX_EMAIL; ?> caractères.</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-md-6 control-label" for="mdp_connec2">Mot de passe</label>
					<div class="col-xs-12 col-md-6">
						<input required id="mdp_connec2" type="password" class="form-control" name="mdp_connec2" ng-model="Mot de passe" maxlength="<?php echo MembresMan::TAILLE_MAX_MDP; ?>">
						<span class="help-block">Entre <?php echo MembresMan::TAILLE_MIN_MDP; ?> et <?php echo MembresMan::TAILLE_MAX_MDP; ?> caractères.</span>
					</div>
				</div>

				<hr style="margin: 5px 0 15px 0;" />

				<div class="form-group">
					<div class="col-xs-12 text-center">
						<input type="checkbox" name="cookies_connec2" id="cookies_connec2" /><label class="control-label" for="cookies_connec2" style="padding-left: 10px;"> Me connecter automatiquement</a></label>
					</div>
				</div>

				<hr style="margin: 5px 0 15px 0;" />

				<div class="form-group">
					<div class="col-xs-12 col-md-12">
						<input type="hidden" name="redirect_connec2" value="<?php if(isset($_POST['redirect_connec'])) echo $_POST['redirect_connec']; else if(isset($_POST['redirect_connec2'])) echo $_POST['redirect_connec2']; else if(!empty($infos['need_connec'])) echo $_SERVER['REQUEST_URI']; else echo '/'; ?>">
						<button type="submit" id="submit_button" class="btn btn-custom center-block">Se connecter <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
					</div>
				</div>
			</div>
		</form>
		</div>

		<div class="row">
			<p class="text-center">
				<a href="oubli_mdp.php" style="color:white">Mot de passe oublié</a>
				<br />
				<a href="inscription.php" style="color:white" title="Accéder à l'inscription">Vous n'avez pas encore créé de compte ?</a>
				<br />
				<a style="color:white" href="connexion.php?v">Vous n'avez pas reçu le mail d'inscription ?</a>
			</p>
		</div>
	</div>
</div>
