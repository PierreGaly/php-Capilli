<?php
$comptes_bancaires_manager = new ComptesBancairesMan($bdd);

$compte_bancaire = $comptes_bancaires_manager->getByMembre($membre->ID);
$erreurs = $infos['erreurs'];
?>
<div class="row_content">
	<div class="panel panel-default">
		<div class="panel-body">
			<h2>Paramètres de mon compte</h2>
			
			<hr />
			
			<h3 id="cb">Mes informations bancaires</h3>
			
			<?php
			if(!empty($_SESSION['compte_compte_bancaire_deleted']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre compte bancaire a été supprimé.</div>';
				unset($_SESSION['compte_compte_bancaire_deleted']);
			}
			
			if(!empty($_SESSION['compte_compte_bancaire_added']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre compte bancaire a été ajouté.</div>';
				unset($_SESSION['compte_compte_bancaire_added']);
			}
			
			if(empty($compte_bancaire))
				echo '<div class="alert alert-warning text-center" role="alert">Vous n\'avez pas renseigné de compte bancaire.<br /><strong>Vous ne pouvez donc pas percevoir l\'argent disponible dans votre tirelire.</strong></div>';
			
			if(!empty($erreurs['titulaire']))
				echo '<div class="alert alert-danger text-center" role="alert">Le titulaire du compte bancaire est incorrect.</div>';
			else if(!empty($erreurs['iban']))
				echo '<div class="alert alert-danger text-center" role="alert">Le code IBAN est incorrect.</div>';
			else if(!empty($erreurs['bic']))
				echo '<div class="alert alert-danger text-center" role="alert">Le code BIC est incorrect.</div>';
			else if(!empty($erreurs['only_one']))
				echo '<div class="alert alert-danger text-center" role="alert">Vous avez déjà renseigné un compte bancaire.</div>';
			else if(!empty($erreurs['nom_agence']))
				echo '<div class="alert alert-danger text-center" role="alert">Le nom de l\'agence est inccorect.</div>';
			else if(!empty($erreurs['rue_agence']))
				echo '<div class="alert alert-danger text-center" role="alert">La rue de l\'agence est inccorecte.</div>';
			else if(!empty($erreurs['lemon']))
				echo LemonWay::displayErrorMessage($erreurs['lemon']);
			
			if(!empty($_SESSION['compte_compte_bancaire_wrong_password']))
			{
				echo '<div class="alert alert-danger text-center" role="alert">Le mot de passe que vous avez entré est incorrect.</div>';
				unset($_SESSION['compte_compte_bancaire_wrong_password']);
			}
			?>
			
			<form class="form-horizontal" method="post" action="perso.php?compte#cb">
				<div class="row">
					<table class="table table-striped table-hover table-bordered table-condensed" style="background-color: white;">
						<?php
						if(empty($compte_bancaire))
						{
						?>
							<tfoot>
								<tr>
									<th colspan="2" class="text-center form-inline">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
											<input type="password" class="form-control" name="compte_bancaire_mdp_verif" id="compte_bancaire_mdp_verif" placeholder="Mot de passe actuel" required>
											<span class="input-group-btn">
												<button class="btn btn-primary" role="submit" name="compte_add_compte_bancaire">Ajouter le compte bancaire <span class="glyphicon glyphicon-chevron-right"></span></button>
											</span>
										</div>
									</th>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<td><label for="compte_titulaire" class="control-label">Titulaire du compte</label></td>
									<td class="text-center"><input type="text" class="form-control" name="compte_titulaire" id="compte_titulaire" <?php
									if(empty($_POST['compte_titulaire']))
										echo 'value="' . ($membre->civilite ? 'MME' : 'MR') . ' ' . htmlspecialchars(mb_strtoupper($membre->nom, 'UTF-8') . ' ' . $membre->prenom) . '"';
									else if(empty($erreurs['titulaire']))
										echo 'value="' . htmlspecialchars($_POST['compte_titulaire']) . '"';
									?> placeholder="Nom tel qu'il apparaît sur le compte" required></td>
								</tr>
								<tr>
									<td><label for="compte_iban" class="control-label">IBAN (lettres incluses)</label></td>
									<td class="text-center"><input type="text" class="form-control" name="compte_iban" id="compte_iban" placeholder="FRkk BBBB BGGG GGCC CCCC CCCC CKK" required <?php
									if(!empty($_POST['compte_iban']) && empty($erreurs['iban']))
										echo 'value="' . htmlspecialchars($_POST['compte_iban']) . '"';
									?>></td>
								</tr>
								<tr>
									<td><label for="compte_bic" class="control-label">BIC (ou SWIFT)</label></td>
									<td class="text-center"><input type="text" class="form-control" name="compte_bic" id="compte_bic" placeholder="BBBB PP EE (BBB)" maxlength="<?php echo ComptesBancairesMan::BIC_MAX_LENGTH + 3; ?>" required <?php
									if(!empty($_POST['compte_bic']) && empty($erreurs['bic']))
										echo 'value="' . htmlspecialchars($_POST['compte_bic']) . '"';
									?>></td>
								</tr>
								<tr>
									<td><label for="compte_nom_agence" class="control-label">Nom de l'agence</label><br /><small>Facultatif si compte français (FR) ou monégasque (MC)</small></td>
									<td class="text-center"><input type="text" class="form-control" name="compte_nom_agence" id="compte_nom_agence" placeholder="Nom de l'agence" maxlength="<?php echo ComptesBancairesMan::NOM_AGENCE; ?>" <?php
									if(!empty($_POST['compte_nom_agence']) && (!empty($erreurs['iban']) || empty($erreurs['nom_agence'])))
										echo 'value="' . htmlspecialchars($_POST['compte_nom_agence']) . '"';
									?>></td>
								</tr>
								<tr>
									<td><label for="compte_rue_agence" class="control-label">Rue de l'agence</label><br /><small>Facultatif si compte français (FR) ou monégasque (MC)</small></td>
									<td class="text-center"><input type="text" class="form-control" name="compte_rue_agence" id="compte_rue_agence" placeholder="Rue de l'agence" maxlength="<?php echo ComptesBancairesMan::RUE_AGENCE; ?>" <?php
									if(!empty($_POST['compte_rue_agence']) && (!empty($erreurs['iban']) || empty($erreurs['rue_agence'])))
										echo 'value="' . htmlspecialchars($_POST['compte_rue_agence']) . '"';
									?>></td>
								</tr>
							</tbody>
						<?php
						}
						else
						{
						?>
							<thead>
								<tr>
									<th class="text-center">Titulaire</th>
									<th class="text-center">IBAN</th>
									<th class="text-center">BIC</th>
									<th class="text-center">Nom de l'agence</th>
									<th class="text-center">Rue de l'agence</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th colspan="5" class="text-center form-inline">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
											<input type="password" class="form-control" name="compte_bancaire_mdp_verif" id="compte_bancaire_mdp_verif" placeholder="Mot de passe actuel" required>
											<span class="input-group-btn">
												<button class="btn btn-default" role="submit" name="compte_del_compte_bancaire" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer ce compte bancaire ?')) return false;">Supprimer le compte bancaire <span class="glyphicon glyphicon-chevron-right"></span></button>
											</span>
										</div>
									</th>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<td class="text-center"><?php echo htmlspecialchars($compte_bancaire->titulaire); ?></td>
									<td class="text-center"><?php echo htmlspecialchars($comptes_bancaires_manager->getHumanReadableIBAN($compte_bancaire->iban)); ?></td>
									<td class="text-center"><?php echo htmlspecialchars($comptes_bancaires_manager->getHumanReadableBIC($compte_bancaire->bic)); ?></td>
									<td class="text-center"><?php echo htmlspecialchars($compte_bancaire->nom_agence); ?></td>
									<td class="text-center"><?php echo htmlspecialchars($compte_bancaire->rue_agence); ?></td>
								</tr>
							</tbody>
						<?php
						}
						?>
					</table>
				</div>
			</form>
			
			<hr />
			
			<h3 id="ip">Mes informations personnelles</h3>
			
			<?php
			if(!empty($erreurs['email_existe']))
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail est déjà utilisée.</div>';
			else if(!empty($erreurs['email_valide']))
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse mail est incorrecte.</div>';
			else if(!empty($erreurs['email_lemon']))
				echo LemonWay::displayErrorMessage($erreurs['email_lemon']);
			else if(!empty($erreurs['adresse_valide']))
				echo '<div class="alert alert-danger text-center" role="alert">L\'adresse postale est incorrecte.</div>';
			else if(!empty($erreurs['adresse_lemon']))
				echo LemonWay::displayErrorMessage($erreurs['adresse_lemon']);
			else if(!empty($erreurs['tel_fixe_valide']))
				echo '<div class="alert alert-danger text-center" role="alert">Le numéro de téléphone fixe est incorrect.</div>';
			else if(!empty($erreurs['tel_fixe_lemon']))
				echo LemonWay::displayErrorMessage($erreurs['tel_fixe_lemon']);
			else if(!empty($erreurs['tel_portable_valide']))
				echo '<div class="alert alert-danger text-center" role="alert">Le numéro de téléphone portable est incorrect.</div>';
			else if(!empty($erreurs['tel_portable_lemon']))
				echo LemonWay::displayErrorMessage($erreurs['tel_portable_lemon']);
			
			if(!empty($_SESSION['compte_email']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre adresse mail a été mise à jour.</div>';
				unset($_SESSION['compte_email']);
			}
			else if(!empty($_SESSION['compte_adresse']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre adresse postale a été mise à jour.</div>';
				unset($_SESSION['compte_adresse']);
			}
			else if(!empty($_SESSION['compte_tel_portable']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre numéro de téléphone n°1 a été mis à jour.</div>';
				unset($_SESSION['compte_tel_portable']);
			}
			else if(!empty($_SESSION['compte_tel_fixe']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre numéro de téléphone n°2 a été mis à jour.</div>';
				unset($_SESSION['compte_tel_fixe']);
			}
			?>
			
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<form role="form" method="post" action="perso.php?compte#ip">
						<table class="table table-striped table-hover table-bordered table-condensed" style="background-color: white;">
							<tbody>
								<tr>
									<td class="text-center">
										<div class="input-group" style="width: 100%;">
											<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
											<input type="email" class="form-control" name="compte_email" style="width: 100%;" id="compte_email" value="<?php echo $membre->email; ?>" placeholder="Adresse mail">
										</div>
									</td>
									<td><button style="width:100%;" class="btn btn-primary" role="submit" name="compte_update_email" id="compte_email_submit_button" value="ok">Mettre à jour <span class="glyphicon glyphicon-chevron-right"></span></button></td>
								</tr>
								<tr>
									<form role="form" method="post" action="">
									<td class="text-center">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-home"></span></span>
											<input type="text" class="form-control" name="compte_adresse_complete" id="compte_adresse_complete" value="<?php echo htmlspecialchars($membre->adresse_complete); ?>" placeholder="Adresse postale">
											<input type="hidden" id="compte_street_number" name="compte_street_number" value="<?php echo $membre->street_number; ?>">
											<input type="hidden" id="compte_route" name="compte_route" value="<?php echo $membre->route; ?>">
											<input type="hidden" id="compte_locality" name="compte_locality" value="<?php echo $membre->locality; ?>">
											<input type="hidden" id="compte_administrative_area_level_1" name="compte_administrative_area_level_1" value="<?php echo $membre->administrative_area_level_1; ?>">
											<input type="hidden" id="compte_country" name="compte_country" value="<?php echo $membre->country; ?>">
											<input type="hidden" id="compte_postal_code" name="compte_postal_code" value="<?php echo $membre->postal_code; ?>">
											<input type="hidden" id="compte_lat" name="compte_lat" value="<?php echo $membre->lat; ?>">
											<input type="hidden" id="compte_lng" name="compte_lng" value="<?php echo $membre->lng; ?>">
										</div>
									</td>
									<td><button style="width:100%;" class="btn btn-primary" role="submit" name="compte_update_adresse" id="compte_adresse_submit_button" value="ok">Mettre à jour <span class="glyphicon glyphicon-chevron-right"></span></button></td>
								</tr>
								<tr>
									<form role="form" method="post" action="">
									<td class="text-center">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span></span>
											<input type="text" class="form-control" name="compte_tel_portable" id="compte_tel_portable" value="+<?php echo substr($membre->tel_portable, 2, 2) . '.' . substr($membre->tel_portable, 4); ?>" placeholder="Téléphone n°1">
										</div>
									</td>
									<td><button style="width:100%;" class="btn btn-primary" role="submit" name="compte_update_tel_portable">Mettre à jour <span class="glyphicon glyphicon-chevron-right"></span></button></td>
								</tr>
								<tr>
									<form role="form" method="post" action="">
									<td class="text-center">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span></span>
											<input type="text" class="form-control" name="compte_tel_fixe" id="compte_tel_fixe" value="<?php if($membre->tel_fixe != '') echo '+' . substr($membre->tel_fixe, 2, 2) . '.' . substr($membre->tel_fixe, 4); ?>" placeholder="Téléphone n°2">
										</div>
									</td>
									<td><button style="width:100%;" class="btn btn-primary" role="submit" name="compte_update_tel_fixe">Mettre à jour <span class="glyphicon glyphicon-chevron-right"></span></button></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
			
			<hr />
			
			<h3 id="mdp">Modifier mon mot de passe</h3>
			
			<?php
			if(!empty($erreurs['mdp_verif']))
				echo '<div class="alert alert-danger text-center" role="alert">Le mot de passe actuel est incorrect.</div>';
			else if(!empty($erreurs['mdp_correspondance']))
				echo '<div class="alert alert-danger text-center" role="alert">Les nouveaux mots de passe ne correspondent pas.</div>';
			else if(!empty($erreurs['mdp_valide']))
				echo '<div class="alert alert-danger text-center" role="alert">Le nouveau mot de passe est incorrect.</div>';
			
			if(!empty($_SESSION['compte_mdp']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre mot de passe a bien été mis à jour.</div>';
				unset($_SESSION['compte_mdp']);
			}
			?>
			
			<form class="form-horizontal" method="post" action="perso.php?compte#mdp">
				<div class="row">
					<div class="col-md-offset-2 col-md-8">
						<table class="table table-striped table-hover table-bordered table-condensed" style="background-color: white;">
							<tfoot>
								<tr>
									<th colspan="2" class="text-center form-inline"><button class="btn btn-primary" role="submit" name="compte_update_mdp">Mettre à jour mon mot de passe <span class="glyphicon glyphicon-chevron-right"></span></button></th>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<td><label for="compte_mdp" class="control-label">Nouveau mot de passe</label></td>
									<td class="text-center"><input type="password" class="form-control" name="compte_mdp" id="compte_mdp" placeholder="Nouveau mot de passe" required></td>
								</tr>
								<tr>
									<td><label for="compte_mdp2" class="control-label">Retapez le nouveau mot de passe</label></td>
									<td class="text-center"><input type="password" class="form-control" name="compte_mdp2" id="compte_mdp2" placeholder="Nouveau mot de passe" required></td>
								</tr>
								<tr>
									<td><label for="compte_mdp_verif" class="control-label">Mot de passe actuel</label></td>
									<td class="text-center col-xs-6">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
											<input type="password" class="form-control" name="compte_mdp_verif" id="compte_mdp_verif" placeholder="Mot de passe actuel" required>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</form>
			
			<hr />
			
			<h3 id="di">Suppression du compte</h3>
			
			<?php
			if(isset($_POST['compte_desinscription_submit']))
				echo '<div class="alert alert-danger text-center" role="alert">Le mot de passe est incorrect.</div>';
			
			if(!empty($_SESSION['desinscription_erreur_objets']))
			{
				echo '<div class="alert alert-danger text-center" role="alert">Votre compte ne peut pas être supprimé car des locations sont en cours ou à venir pour certaines de vos annonces.</div>';
				unset($_SESSION['desinscription_erreur_objets']);
			}
			
			if(!empty($_SESSION['desinscription_erreur_tirelire']))
			{
				echo '<div class="alert alert-danger text-center" role="alert">Votre compte ne peut pas être supprimé car il vous reste <strong>' . number_format($_SESSION['desinscription_erreur_tirelire'], 2, ',', ' ') . ' €</strong> dans votre tirelire.<br />Veuillez la vider avant de vous désinscrire.</div>';
				unset($_SESSION['desinscription_erreur_tirelire']);
			}
			
			if(!empty($_SESSION['desinscription_erreur_lemon']))
			{
				echo LemonWay::displayErrorMessage($_SESSION['desinscription_erreur_lemon']);
				unset($_SESSION['desinscription_erreur_lemon']);
			}
			
			if(!empty($_SESSION['desinscription_erreur_versements_reels_attente']))
			{
				echo '<div class="alert alert-danger text-center" role="alert">Votre compte ne peut pas être supprimé car des virements bancaires sont en attente.<br />Veuillez réessayer plus tard.</div>';
				unset($_SESSION['desinscription_erreur_versements_reels_attente']);
			}
			?>
			
			<form class="form-horizontal" method="post" action="perso.php?compte#di">
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<table class="table table-striped table-hover table-bordered table-condensed" style="background-color: white;">
						<tfoot>
							<tr>
								<th colspan="2" class="text-center form-inline"><button class="btn btn-default" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer définitivement votre compte ?')) return false;" role="submit" name="compte_desinscription_submit">Supprimer définitivement mon compte <span class="glyphicon glyphicon-chevron-right"></span></button></th>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td><label for="compte_desinscription_mdp" class="control-label">Mot de passe</label></td>
								<td class="text-center col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input type="password" class="form-control" name="compte_desinscription_mdp" id="compte_desinscription_mdp" placeholder="Mot de passe" required>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			</form>
		</div>
	</div>
	
	<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp"></script>
</div>