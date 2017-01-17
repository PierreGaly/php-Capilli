<?php
$objets_manager = new ObjetsMan($this->bdd);
$communautes_manager = new CommunautesMan($bdd);

$objet = $infos['objet'];
$proprio = $objets_manager->getProprio($objet->ID_proprio);
$erreurs = $infos['erreurs'];
$nom_html = htmlspecialchars($objet->nom);
$photos = glob(IMAGES_BIENS . $objet->ID . '/*.*');
?>
<div class="row" style="padding-bottom: 0;">
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<?php
			if(empty($photos))
				echo '<li data-target="#myCarousel" data-slide-to="0" class="active"></li>';
			else
			{
				foreach($photos as $key => $photo)
				{
					echo '<li data-target="#myCarousel" data-slide-to="' . $key . '"';
					
					if($key == 0)
						echo ' class="active"';
					
					echo '></li>';
				}
			}
			?>
		</ol>
		
		<!-- Wrapper for slides -->
		<div class="carousel-inner text-center" role="listbox" style="font-size: 0;">
			<?php
			if(empty($photos))
			{
				?>
					<div class="item active">
						<div style="display: inline-block;">
							<span style="height: <?php echo ObjetsMan::PHOTO_MAX_HAUTEUR; ?>px; vertical-align: middle; display: table-cell;">
								<img src="<?php echo ObjetsMan::DEFAULT_PHOTO_PATH; ?>" alt="<?php echo $nom_html; ?>" style="max-height: 100%; max-width: 100%;">
							</span>
						</div>
					</div>
				<?php
			}
			else
			{
				foreach($photos as $key => $photo)
				{
				?>
					<div class="item<?php if($key == 0) echo ' active'; ?>">
						<div style="display: inline-block;">
							<span style="height: <?php echo ObjetsMan::PHOTO_MAX_HAUTEUR; ?>px; vertical-align: middle; display: table-cell;">
								<img src="<?php echo $photo; ?>" alt="<?php echo $nom_html . ' (' . ($key+1) . ')'; ?>" style="max-height: 100%; max-width: 100%;">
							</span>
						</div>
					</div>
				<?php
				}
			}
			?>
		</div>
		
		<!-- Left and right controls -->
		<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Précédant</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Suivant</span>
		</a>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h2><?php echo htmlspecialchars($objet->nom); ?><br /><small><?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?></small></h2>
		
		<div class="alert alert-info text-center" role="alert">
			Bienvenue sur la page d'administration de votre annonce.
			<br /><br />
			<a class="btn btn-primary" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;reservations"><span class="glyphicon glyphicon-calendar"></span> Gérer les réservations<?php
			$nbr_notifs_objet = $transactions_manager->countNotifsTransactionsAsProprioByID_objet($objet->ID);
			
			if($nbr_notifs_objet > 0)
				echo ' <span class="badge badge-custom">' . $nbr_notifs_objet . '</span>';
			?></a>
			<span class="hidden-xs"> | </span>
			<a class="btn btn-custom active" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;edit"><span class="glyphicon glyphicon-pencil"></span> Modifier l'annonce</a>
		</div>
		
		<?php
		if($objet->actif == 0)
			echo '<div class="alert alert-danger text-center" role="alert">Vous seul pouvez voir cette annonce car vous l\'avez désactivée.</div>';
		
		if(isset($_SESSION['annonce_not_remove']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Vous ne pouvez pas supprimer cette annonce car des réservations sont en cours.<br />Vous pouvez cependant la rendre inactive pour la désindexer du site.</div>';
			unset($_SESSION['annonce_not_remove']);
		}
		?>
		
		<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_photos">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<form role="form" method="post" action="" enctype="multipart/form-data" class="form-inline">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Modifier les photos de l'annonce</h4>
						</div>
						
						<div class="modal-body">
							<div class="row">
								<?php
								if(empty($photos))
									echo '<p class="text-center"><em>Aucune photo.</em></p>';
								else
								{
									foreach($photos as $key => $photo)
									{
										$file_infos = pathinfo($photo);
										?>
										<div class="col-xs-6 col-md-3">
											<div style="position: relative; width: 100%; padding-bottom: 100%; margin-bottom:20px;">
												<a data-filename="<?php echo $file_infos['basename']; ?>" href="" class="thumbnail" style="position:absolute; width:100%; height:100%;">
													<img style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto; max-width: 100%; max-height: 100%;" src="<?php echo $photo; ?>" alt="<?php echo $nom_html . ' (' . ($key+1) . ')'; ?>">
													<?php if(IMAGES_BIENS . $objet->ID . '/' . $objet->photo_principale == $photo) echo '<span class="glyphicon glyphicon-camera" style="font-size: 2em;"></span>'; ?>
												</a>
											</div>
										</div>
										<?php
									}
								}
								?>
							</div>
							
							<hr />
							
							<div class="row text-center">
								<input type="hidden" id="id_annonce" value="<?php echo $_GET['id']; ?>">
								<a href="" class="btn btn-custom" id="button_photo_principale" disabled><span class="glyphicon glyphicon-camera"></span> Définir comme photo principale</a>
								<br />
								<a href="" class="btn btn-default" id="button_photo_supprimer" disabled><span class="glyphicon glyphicon-remove"></span> Supprimer la photo</a>
							</div>
						</div>
						
						<div class="modal-footer" style="text-align: center;">
							<label class="control-label" for="photos">Ajouter des photos</label>
							<input type="file" class="form-control" id="photos" name="photos[]" required multiple accept="image/*">
							<button class="form-control btn btn-custom" role="submit">Envoyer les photos <span class="glyphicon glyphicon-chevron-right" style="margin-right: 5px;"></span></button>
						</div>
					</form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-body" style="padding: 0;">
						<h2>Informations sur le bien</h2>
						
						<?php
						if(!empty($erreurs['sous_categorie']))
							echo '<div class="alert alert-danger text-center" role="alert">La sous-catégorie est incorrecte.</div>';
						else if(!empty($erreurs['description']))
							echo '<div class="alert alert-danger text-center" role="alert">La description doit être complétée.</div>';
						else if(!empty($erreurs['informations']['nb_objets']))
							echo '<div class="alert alert-danger text-center" role="alert">Le nombre d\'objets est incorrect.</div>';
						else if(!empty($erreurs['informations']['marque']))
							echo '<div class="alert alert-danger text-center" role="alert">La marque de du bien ne peut pas dépasser ' . ObjetsMan::TAILLE_MAX_MARQUE . ' caractères.</div>';
						else if(!empty($erreurs['informations']['modele']))
							echo '<div class="alert alert-danger text-center" role="alert">Le modèle de du bien ne peut pas dépasser ' . ObjetsMan::TAILLE_MAX_MODELE . ' caractères.</div>';
						else if(!empty($erreurs['tarifs']['prix_journee']))
							echo '<div class="alert alert-danger text-center" role="alert">La prix à la journée est incorrect.</div>';
						else if(!empty($erreurs['tarifs']['prix_weekend']))
							echo '<div class="alert alert-danger text-center" role="alert">Le forfait weekend est incorrect.</div>';
						else if(!empty($erreurs['tarifs']['prix_semaine']))
							echo '<div class="alert alert-danger text-center" role="alert">Le forfait semaine est incorrect.</div>';
						else if(!empty($erreurs['tarifs']['prix_mois']))
							echo '<div class="alert alert-danger text-center" role="alert">Le forfait mois est incorrect.</div>';
						else if(!empty($erreurs['tarifs']['caution']))
							echo '<div class="alert alert-danger text-center" role="alert">La valeur de la caution est incorrecte.</div>';
						
						if(isset($_SESSION['ma_photo_principale']))
						{
							echo '<div class="alert alert-info text-center" role="alert">La photo <em>' . htmlspecialchars($_SESSION['ma_photo_principale']) . '</em> a été définie comme photo principale.</div>';
							unset($_SESSION['ma_photo_principale']);
						}
						
						if(isset($_SESSION['ma_photo_removed']))
						{
							echo '<div class="alert alert-info text-center" role="alert">La photo <em>' . htmlspecialchars($_SESSION['ma_photo_removed']) . '</em> a été retirée de l\'annonce.</div>';
							unset($_SESSION['ma_photo_removed']);
						}
						
						if(isset($_SESSION['annonce_photos']))
						{
							echo '<div class="alert alert-info text-center" role="alert">Résultat de l\'envoi des photos :<br /><ul style="display: inline-block; text-align: left;">';
							
							foreach($_SESSION['annonce_photos'] as $photo)
							{
								echo '<li style=""><em>' . htmlspecialchars($photo[0]) . '</em> : <strong>';
								
								switch($photo[1])
								{
									case 'erreur_envoi':
										echo 'erreur lors de l\'envoi';
										break;
									
									case 'extension':
										echo 'extension invalide';
										break;
									
									case 'taille':
										echo 'le fichier est trop volumineux (max : ' . ObjetsMan::TAILLE_MAX_PHOTO/1024/1024 . ' Mo)';
										break;
									
									default:
										echo 'envoyé avec succès';
								}
								
								echo '</strong></li>';
							}
							
							echo  '</ul></div>';
							
							unset($_SESSION['annonce_photos']);
						}
						?>
						
						<div class="col-md-6 text-center" style="margin-bottom: 20px;">
							<form method="post" action="">
								<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Visibilité de l'annonce <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Vous pouvez rendre votre annonce inactive pour qu'elle ne soit visible plus que par les membres qui ont effectué une demande de location."></span></h4>
								
								<div class="col-sm-12">
									<div class="btn-group btn-group-justified" role="group" aria-label="...">
										<div class="btn-group" role="group">
											<button type="submit" name="ma_submit_actif" class="btn btn-custom<?php if($objet->actif) echo ' active'; ?>">Active</button>
										</div>
										<div class="btn-group" role="group">
											<button type="submit" name="ma_submit_inactif" class="btn btn-default<?php if(!$objet->actif) echo ' active'; ?>">Inactive</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						
						<div class="col-md-6">
							<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Photos de l'annonce</h4>
							
							<?php
							if($objet->photo_principale == '')
								echo '<div class="alert alert-danger text-center" role="alert"><span class="glyphicon glyphicon-info-sign"></span> Vous n\'avez pas défini de photo principale pour votre annonce.</div>';
							?>
							
							<button class="btn btn-custom center-block" data-toggle="modal" data-target="#modal_photos"><span class="glyphicon glyphicon-pencil"></span> Modifier les photos de l'annonce</button>
						</div>
						
						<div class="col-md-12 text-center" style="margin-bottom: 20px;">
							<form method="post" action="">
								<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Description</h4>
								
								<textarea class="form-control" name="ma_description" id="ma_description"><?php echo nl2br(htmlspecialchars($objet->description)); ?></textarea>
								<button class="btn btn-primary" role="submit">Mettre à jour la description <span class="glyphicon glyphicon-chevron-right"></span></button>
							</form>
						</div>
						
						<div class="col-md-6 text-center" style="margin-bottom: 20px;">
							<form method="post" action="">
								<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Conditions de location <small>(facultatif)</small></h4>
								
								<textarea class="form-control" name="ma_location" id="ma_location"><?php echo nl2br(htmlspecialchars($objet->conditions_location)); ?></textarea>
								<button class="btn btn-primary" role="submit">Mettre à jour les conditions de location <span class="glyphicon glyphicon-chevron-right"></span></button>
							</form>
						</div>
						
						<div class="col-md-6 text-center" style="margin-bottom: 20px;">
							<form method="post" action="">
								<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Conditions d’utilisation <small>(facultatif)</small></h4>
								
								<textarea class="form-control" name="ma_utilisation" id="ma_utilisation"><?php echo nl2br(htmlspecialchars($objet->conditions_utilisation)); ?></textarea>
								<button class="btn btn-primary" role="submit">Mettre à jour les conditions d'utilisation <span class="glyphicon glyphicon-chevron-right"></span></button>
							</form>
						</div>
						
						<div class="col-md-12 text-center" style="margin-bottom: 20px;">
							<form method="post" action="" class="form-horizontal">
								<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom text-left">Catégorie de l'annonce</h4>
								
								<div class="form-group form-inline">
									<label class="control-label col-sm-6" for="ma_categorie">Catégorie</label>
									<div class="col-sm-6 text-left">
										<select name="ma_categorie" id="ma_categorie" class="form-control" required>
											<?php
											$categories_manager = new CategoriesMan($bdd);
											$sous_categories_manager = new SousCategoriesMan($bdd);
											$categories = $categories_manager->getCategories();
											
											$cat = 1;
											$objet_ID_categorie = -1;
											
											if(!isset($_POST['ma_categorie']))
												$objet_ID_categorie = $sous_categories_manager->getSousCategorieByID($objet->ID_sous_categorie)->ID_categorie;
											
											foreach($categories as $categorie)
											{
												echo '<option value="' . $categorie->ID . '"';
												
												if((isset($_POST['ma_categorie']) && $_POST['ma_categorie'] == $categorie->ID) || (!isset($_POST['ma_categorie']) && $objet_ID_categorie == $categorie->ID))
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
									<label class="control-label col-sm-6">Sous-catégorie</label>
									<div class="col-sm-6 text-left" for="ma_sous_categorie">
										<select name="ma_sous_categorie" id="ma_sous_categorie" class="form-control" required>
											<?php
											$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($cat);
											
											foreach($sous_categories as $sous_categorie)
											{
												echo '<option value="' . $sous_categorie->ID . '"';
												
												if((isset($_POST['ma_sous_categorie']) && $_POST['ma_sous_categorie'] == $sous_categorie->ID) || (!isset($_POST['ma_sous_categorie']) && $objet->ID_sous_categorie == $sous_categorie->ID))
													echo ' selected';
												
												echo '>' . htmlspecialchars($sous_categorie->nom) . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								
								<button class="btn btn-primary" role="submit">Mettre à jour la catégorie de l'annonce <span class="glyphicon glyphicon-chevron-right"></span></button>
							</form>
						</div>
						
						<div class="col-md-6">
							<form method="post" action="">
								<table class="table table-striped table-hover">
									<caption class="text-center">Informations complémentaires</caption>
									<tbody>
										<tr>
											<td><label class="control-label" for="ma_nb_objets">Nombre d'objets de ce type</label></td>
											<td style="vertical-align: middle;">
												<input type="number" class="form-control" id="ma_nb_objets" name="ma_nb_objets" min="1" maxlength="<?php echo ObjetsMan::NB_MAX_OBJETS; ?>" required value="<?php echo $objet->nb_objets; ?>">
											</td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_marque">Marque <small class="help-block">Facultatif</small></label></td>
											<td style="vertical-align: middle;"><input type="text" class="form-control" name="ma_marque" id="ma_marque" value="<?php echo $objet->marque; ?>"></td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_modele">Modèle <small class="help-block">Facultatif</small></label></td>
											<td style="vertical-align: middle;"><input type="text" class="form-control" name="ma_modele" id="ma_modele" value="<?php echo $objet->modele; ?>"></td>
										</tr>
										<tr>
											<td colspan="2" class="text-center"><button class="btn btn-primary" role="submit">Mettre à jour les informations <span class="glyphicon glyphicon-chevron-right"></span></button></td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
						
						<div class="col-md-6">
							<form method="post" action="">
								<table class="table table-striped table-hover">
									<caption class="text-center">Tarifs</caption>
									<tbody>
										<tr>
											<td><label class="control-label" for="ma_prix_journee">Journée</label></td>
											<td style="vertical-align: middle;">
												<div class="input-group">
													<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
													<input type="text" class="form-control input_prix" name="ma_prix_journee" id="ma_prix_journee" placeholder="0,00" value="<?php echo $objet->prix_journee; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_prix_weekend">Week-end <small class="help-block">Facultatif</small></label></td>
											<td style="vertical-align: middle;">
												<div class="input-group">
													<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
													<input type="text" class="form-control input_prix" name="ma_prix_weekend" id="ma_prix_weekend" placeholder="0,00" value="<?php echo $objet->prix_weekend; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_prix_semaine">Semaine <small class="help-block">Facultatif</small></label></td>
											<td style="vertical-align: middle;">
												<div class="input-group">
													<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
													<input type="text" class="form-control input_prix" name="ma_prix_semaine" id="ma_prix_semaine" placeholder="0,00" value="<?php echo $objet->prix_semaine; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_prix_mois">Mois <small class="help-block">Facultatif</small></label></td>
											<td style="vertical-align: middle;">
												<div class="input-group">
													<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
													<input type="text" class="form-control input_prix" name="ma_prix_mois" id="ma_prix_mois" placeholder="0,00" value="<?php echo $objet->prix_mois; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td><label class="control-label" for="ma_caution">Caution</label></td>
											<td style="vertical-align: middle;">
												<div class="input-group">
													<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
													<input type="text" class="form-control input_prix" name="ma_caution" id="ma_caution" placeholder="0,00" value="<?php echo $objet->caution; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="form-group form-inline">
													<div class="col-sm-12 text-center">
														<input type="checkbox" class="form-control" id="ma_cheque" name="ma_cheque"<?php
														if($objet->cheque_caution)
															echo ' checked';
														?>>
														<label class="control-label" for="ma_cheque" style="padding-left: 5px;">
														J'ai besoin d'un chèque de caution <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Vous n'êtes pas obligé de demander un chèque de caution car <?php echo SITE_NOM; ?> prend automatiquement l'empreinte bancaire des loueurs afin de vous protéger en cas de litige."></span>
														</label>
														<small><br />Ne prendra effet que sur les prochaines réservations</small>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="2" class="text-center"><button class="btn btn-primary" role="submit">Mettre à jour les tarifs <span class="glyphicon glyphicon-chevron-right"></span></button></td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
					</div>
					
					<div class="panel-footer text-center">
						<form method="post" action="" style="margin: 0; padding: 0;">
							<button name="ma_remove" class="btn btn-default" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer cette annonce ?')) return false;"><span class="glyphicon glyphicon-remove"></span> Supprimer cette annonce</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>