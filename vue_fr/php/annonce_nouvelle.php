<?php
$erreurs = $infos['erreurs'];
?>
<div class="row">
	<div class="row_content">
		<h2>Déposer une annonce</h2>
		
		<?php
		if($erreurs)
		{
			if($erreurs['titre'])
				echo '<div class="alert alert-danger text-center" role="alert">Le titre de l\'annonce doit être compris entre ' . ObjetsMan::TAILLE_MIN_TITRE . ' et ' . ObjetsMan::TAILLE_MAX_TITRE . ' caractères.</div>';
			else if($erreurs['sous_categorie'])
				echo '<div class="alert alert-danger text-center" role="alert">Vous n\'avez pas sélectionné de sous-catégorie.</div>';
			else if($erreurs['club'])
				echo '<div class="alert alert-danger text-center" role="alert">Vous ne faîtes plus partie du club.</div>';
			else if($erreurs['description'])
				echo '<div class="alert alert-danger text-center" role="alert">La description doit être complétée.</div>';
			else if($erreurs['marque'])
				echo '<div class="alert alert-danger text-center" role="alert">La marque de du bien ne peut pas dépasser ' . ObjetsMan::TAILLE_MAX_MARQUE . ' caractères.</div>';
			else if($erreurs['modele'])
				echo '<div class="alert alert-danger text-center" role="alert">Le modèle de du bien ne peut pas dépasser ' . ObjetsMan::TAILLE_MAX_MODELE . ' caractères.</div>';
			else if($erreurs['prix_journee'])
				echo '<div class="alert alert-danger text-center" role="alert">Le prix à la journée est incorrect.<br /><strong>Veuillez entrez une somme comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
			else if($erreurs['prix_weekend'])
				echo '<div class="alert alert-danger text-center" role="alert">Le forfait weekend est incorrect.<br /><strong>Veuillez entrez une somme comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
			else if($erreurs['prix_semaine'])
				echo '<div class="alert alert-danger text-center" role="alert">Le forfait semaine est incorrect.<br /><strong>Veuillez entrez une somme comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
			else if($erreurs['prix_mois'])
				echo '<div class="alert alert-danger text-center" role="alert">Le forfait mois est incorrect.<br /><strong>Veuillez entrez une somme comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
			else if($erreurs['caution'])
				echo '<div class="alert alert-danger text-center" role="alert">La valeur de la caution est incorrecte.<br /><strong>Veuillez entrez une somme comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
			else if($erreurs['nb_objets'])
				echo '<div class="alert alert-danger text-center" role="alert">Le nombre d\'objets est incorrect.</div>';
			
			if(!empty($erreurs['photos']))
			{
				$chaine = '';
				
				foreach($erreurs['photos'] as $photo)
				{
					if($photo[1] == 'erreur_envoi')
						$chaine .= '<li style=""><em>' . htmlspecialchars($photo[0]) . '</em> : <strong>erreur lors de l\'envoi</strong></li>';
					else if($photo[1] == 'extension')
						$chaine .= '<li style=""><em>' . htmlspecialchars($photo[0]) . '</em> : <strong>extension invalide</strong></li>';
					else if($photo[1] == 'taille')
						$chaine .= '<li style=""><em>' . htmlspecialchars($photo[0]) . '</em> : <strong>le fichier est trop volumineux (max : ' . ObjetsMan::TAILLE_MAX_PHOTO/1024/1024 . ' Mo)</strong></li>';
				}
				
				if(!empty($chaine))
					echo '<div class="alert alert-danger text-center" role="alert">Erreurs des photos :<br /><ul style="display: inline-block; text-align: left;">' . $chaine . '</ul></div>';
				else
					echo '<div class="alert alert-danger text-center" role="alert">Veuillez renvoyer vos photos.</div>';
			}
		}
		?>
		
		<div class="row">
			<div class="formulaire col-md-offset-2 col-md-8">
			<form class="form-horizontal" method="post" action="" enctype="multipart/form-data" role="form">
				<fieldset>
					<legend>Votre annonce</legend>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_titre">Titre de l'annonce</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="na_titre" id="na_titre" placeholder="Titre" maxlength="<?php echo ObjetsMan::TAILLE_MAX_TITRE; ?>" required autofocus<?php
							if(isset($_POST['na_titre']) && !$erreurs['titre'])
								echo ' value="' . $_POST['na_titre'] . '"';
							?>>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_nb_objets">Nombre d'objets de ce type</label>
						<div class="col-sm-8">
							<input type="number" class="form-control" id="na_nb_objets" name="na_nb_objets" min="1" maxlength="<?php echo ObjetsMan::NB_MAX_OBJETS; ?>" required value="<?php
							if(isset($_POST['na_nb_objets']) && !$erreurs['nb_objets'])
								echo $_POST['na_nb_objets'];
							else
								echo '1';
							?>">
						</div>
					</div>
					
					<div class="form-group form-inline">
						<label class="control-label col-sm-4" for="na_categorie">Catégorie</label>
						<div class="col-sm-8">
							<select name="na_categorie" id="na_categorie" class="form-control" required>
								<option value="">Choisissez une catégorie</option>
								<?php
								$categories_manager = new CategoriesMan($bdd);
								$categories = $categories_manager->getCategories();
								
								$cat = -1;
								
								foreach($categories as $categorie)
								{
									echo '<option value="' . $categorie->ID . '"';
									
									if(isset($_POST['na_categorie']) && $_POST['na_categorie'] == $categorie->ID)
									{
										$cat = $categorie->ID;
										echo ' selected';
									}
									
									echo '>' . htmlspecialchars($categorie->nom) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					
					<div class="form-group form-inline">
						<label class="control-label col-sm-4">Sous-catégorie</label>
						<div class="col-sm-8" for="na_sous_categorie">
							<select name="na_sous_categorie" id="na_sous_categorie" class="form-control" required>
								<option value="">Choisissez une sous-catégorie</option>
								<?php
								$sous_categories_manager = new SousCategoriesMan($bdd);
								$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($cat);
								
								foreach($sous_categories as $sous_categorie)
								{
									echo '<option value="' . $sous_categorie->ID . '"';
									
									if(isset($_POST['na_sous_categorie']) && $_POST['na_sous_categorie'] == $sous_categorie->ID)
										echo ' selected';
									
									echo '>' . htmlspecialchars($sous_categorie->nom) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					
					<div class="form-group form-inline">
						<label class="control-label col-sm-4" for="na_club">À destination de <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Vous pouvez restreindre l'accès à votre bien seulement aux membres d'un club dont vous faîtes partie."></span></label>
						<div class="col-sm-8">
							<select name="na_club" id="na_club" class="form-control">
								<option value="-1">Tout le monde</option>
								<?php
								$communautes_manager = new CommunautesMan($bdd);
								$clubs = $communautes_manager->getCommunautesByMembre($membre->ID);
								
								foreach($clubs as $club)
								{
									echo '<option value="' . $club->ID . '"';
									
									if(isset($_POST['na_club']) && $_POST['na_club'] == $club->ID)
									{
										$cat = $club->ID;
										echo ' selected';
									}
									
									echo '>' . htmlspecialchars($club->nom) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_description">Description de l'annonce</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="na_description" name="na_description" placeholder="Description" required><?php
							if(isset($_POST['na_description']) && !$erreurs['description'])
								echo htmlspecialchars($_POST['na_description']);
							?></textarea>
						</div>
					</div>
					
					<div class="form-group form-inline">
						<div class="col-sm-12 text-center">
							<input type="checkbox" class="form-control" id="na_cheque" name="na_cheque"<?php
							if(isset($_POST['na_cheque']))
								echo ' checked';
							?>>
							<label class="control-label" for="na_cheque" style="padding-left: 5px;">Cochez si vous avez besoin d'un chèque de caution <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Vous n'êtes pas obligé de demander un chèque de caution car <?php echo SITE_NOM; ?> prend automatiquement l'empreinte bancaire des locataires afin de vous protéger en cas de litige."></span></label>
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>À propos de votre bien</legend>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_location">Conditions de location <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Décrivez les conditions de location qui apparaitront sur le contrat de location"></span><small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<textarea class="form-control" id="na_location" name="na_location" placeholder="Conditions de location"><?php
							if(isset($_POST['na_location']) && !$erreurs['location'])
								echo htmlspecialchars($_POST['na_location']);
							?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_utilisation">Conditions d'utilisation <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Décrivez les conditions d'utilisation pour informer le locataire des limites pour l'usage de votre bien"></span><small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<textarea class="form-control" id="na_utilisation" name="na_utilisation" placeholder="Conditions d'utilisation"><?php
							if(isset($_POST['na_utilisation']) && !$erreurs['utilisation'])
								echo htmlspecialchars($_POST['na_utilisation']);
							?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_marque">Marque <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="na_marque" name="na_marque" maxlength="<?php echo ObjetsMan::TAILLE_MAX_MARQUE; ?>"<?php
								if(isset($_POST['na_marque']) && !$erreurs['marque'])
									echo '  value="' . $_POST['na_marque'] . '"';
								?>>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_modele">Modèle <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="na_modele" name="na_modele" maxlength="<?php echo ObjetsMan::TAILLE_MAX_MODELE; ?>"<?php
								if(isset($_POST['na_modele']) && !$erreurs['modele'])
									echo '  value="' . $_POST['na_modele'] . '"';
								?>>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_photos">Photos du bien<small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<input type="file" class="form-control" id="na_photos" name="na_photos[]" multiple accept="image/*">
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>Détails du prix</legend>
					
					<blockquote style="background-color: rgb(252, 252, 252);">
						<p class="text-justify">
						<span class="glyphicon glyphicon-pushpin rose_custom" style="float: left; font-size: 3em; margin: 10px 30px 30px 0;"></span>
							« La finalité de notre plateforme est de faciliter la vie <strong>aux étudiants</strong>.<br />Dans ce sens, plus le prix de location du bien sera bas, plus nous améliorerons le classement de l’annonce sur la page de présentation des biens. »
						</p>
						
						<footer style="background-color: rgba(0, 0, 0, 0);"><strong>Mohamed Zarkik</strong>, <cite title="Source Title">fondateur de <?php echo SITE_NOM; ?>.</cite></footer>
					</blockquote>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_prix_journee">Prix à la journée</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
								<input type="text" class="form-control input_prix" id="na_prix_journee" name="na_prix_journee" placeholder="0,00" required<?php
								if(isset($_POST['na_prix_journee']) && !$erreurs['prix_journee'])
									echo '  value="' . $_POST['na_prix_journee'] . '"';
								?>>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_prix_weekend">Forfait Week-end <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
								<input type="text" class="form-control input_prix" id="na_prix_weekend" name="na_prix_weekend" placeholder="0,00"<?php
								if(isset($_POST['na_prix_weekend']) && !$erreurs['prix_weekend'])
									echo '  value="' . $_POST['na_prix_weekend'] . '"';
								?>>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_prix_semaine">Forfait semaine <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
								<input type="text" class="form-control input_prix" id="na_prix_semaine" name="na_prix_semaine" placeholder="0,00"<?php
								if(isset($_POST['na_prix_semaine']) && !$erreurs['prix_semaine'])
									echo '  value="' . $_POST['na_prix_semaine'] . '"';
								?>>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_prix_mois">Forfait mois <small class="help-block">Facultatif</small></label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
								<input type="text" class="form-control input_prix" id="na_prix_mois" name="na_prix_mois" placeholder="0,00"<?php
								if(isset($_POST['na_prix_mois']) && !$erreurs['prix_mois'])
									echo '  value="' . $_POST['na_prix_mois'] . '"';
								?>>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-4" for="na_caution">Valeur de l'objet</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
								<input type="text" class="form-control input_prix" id="na_caution" name="na_caution" placeholder="0,00" required<?php
								if(isset($_POST['na_caution']) && !$erreurs['caution'])
									echo '  value="' . $_POST['na_caution'] . '"';
								?>>
							</div>
						</div>
					</div>
				</fieldset>
				
				<hr />
				
				<div class="form-group" style="text-align: center; padding: 0; margin: 0;">
					<button class="btn btn-custom" role="submit">Déposer l'annonce <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div>
	</div>
</div>
