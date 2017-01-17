<?php
$membres_manager = new MembresMan($bdd);
$objets_manager = new ObjetsMan($bdd);
$communautes_manager = new CommunautesMan($bdd);
$transactions_manager = new TransactionsMan($bdd);

$membre_apercu = $infos['membre_apercu'];

$commentaires = $transactions_manager->getCommentaires($membre_apercu->ID);
$nombre_annonces = $membres_manager->getTransactionsAnnoncesPubliees($membre_apercu->ID);
?>
<div class="row">
	<div class="row_content">
		<div class="panel panel-default">
			<div class="panel-body">
				<h2 style="margin-top: 0;">
					<?php echo htmlspecialchars($membre_apercu->prenom) . ' ' . htmlspecialchars($membre_apercu->nom); ?>
					<br />
					<small>
					<?php
					$note = round($membres_manager->getNoteMean($membre_apercu->ID));
				
					if($note >= 0)
					{
						for($i=0; $i<5; $i++)
							echo ($i + 1 <= $note) ? '<span class="glyphicon glyphicon-star rose_custom"></span>' : '<span class="glyphicon glyphicon-star-empty rose_custom"></span>';
					}
					?>
					</small>
				</h2>
				
				<p class="text-center">
					<span><?php echo ($membre_apercu->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 120px;"></span>' : '<img src="avatars/' . $membre_apercu->avatar . '" class="img-circle" style="width: 120px; height: 120px;"/>'; ?></span>
					
					<br />
					
					<span style="font-size: 1.2em;">
						<?php
						$age = $membre_apercu->getAge();
						
						echo $age . ' an';
						
						if($age > 1)
							echo 's';
						?>, <?php
						if($membre_apercu->type)
							echo ($membre_apercu->civilite) ? 'professionnelle' : 'professionnel';
						else
							echo 'particulier';
						?>.
						<br />
						<?php echo $membre_apercu->administrateur ? 'Administrateur' : 'Membre'; ?> depuis le <?php echo (new DateTime($membre_apercu->date_inscription))->format('d/m/Y'); ?>.
					</span>
				</p>
				
				<form method="post" action="">
					<p class="text-center">
						<a href="perso.php?messages&amp;d1=<?php echo $membre_apercu->ID; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> Envoyer un message</a>
						<?php
						if($membre_apercu->type == 0 && !$membre_apercu->email_valide && $membre && $membre->administrateur)
							echo '| <button class="btn btn-custom" name="membre_apercu_valider_email"><span class="glyphicon glyphicon-ok"></span> Valider l\'adresse mail</button>';
						?>
					</p>
				</form>
				
				<hr />
				
				<h3>Statistiques</h3>
				
				<div class="row">
					<div class="col-md-offset-2 col-md-8">
						<table class="table table-striped table-hover">
							<tbody>
								<tr>
									<td><label class="control-label">Annonces publiées</label></td>
									<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo $nombre_annonces; ?></td>
								</tr>
								<tr>
									<td><label class="control-label">Locations effectuées</label></td>
									<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo $membres_manager->getTransactionsEffectueesMembre($membre_apercu->ID); ?></td>
								</tr>
								<tr>
									<td><label class="control-label">Taux de réponse</label></td>
									<td class="text-center rose_custom" style="font-size: 1.6em;"><?php
									$pourcentage = $membres_manager->getPourcentageReponses($membre_apercu->ID);
									echo ($pourcentage < 0) ? '-' : (round($pourcentage*100) . ' %'); ?></td>
								</tr>
								<tr>
									<td><label class="control-label">Temps de réponse moyen</label></td>
									<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo ($membre_apercu->TDR_nombre > 0) ? $membre_apercu->encode_temps_reponse() : '-'; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
				<hr />
				
				<h3>Commentaires</h3>
				
				<div class="panel panel-default">
					<div class="panel-body">
						<?php
						if(empty($commentaires))
						{
						?>
							<p class="text-center">
								<em>Aucun commentaire.</em>
							</p>
						<?php
						}
						else
						{
							foreach($commentaires as $key => $commentaire)
							{
								$membre_commentaire = $membres_manager->getMembreByID($commentaire->ID_membre);
								
								if($key)
									echo '<hr />';
								?>
								<div class="media">
									<div class="media-left">
										<a href="membre.php?id=<?php echo $membre_commentaire->ID; ?>" style="text-decoration: none; position: relative; bottom: -5px;">
											<?php
											if($membre_commentaire->avatar == '')
												echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 50px;"></span>';
											else
												echo '<img class="media-object img-circle" alt="' . $membre_commentaire->prenom . ' ' . $membre_commentaire->avatar . '" src="avatars/' . $membre_commentaire->avatar . '" style="width: 50px; height: 50px;">';
											?>
										</a>
									</div>
									<div class="media-body">
										<h4 class="media-heading"><?php echo $membre_commentaire->sePresenter(); ?><br /><small>Le <?php echo $commentaire->date_creation; ?></small></h4>
										
										<?php echo nl2br(htmlspecialchars($commentaire->commentaire)); ?>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				
				<?php
				if($nombre_annonces > 0)
				{
				?>
				<hr />
				
				<h3>Dernières annonces</h3>
				
				<div class="row" style="text-align: center; padding: 2px;">
					<?php
					$objets = $objets_manager->getByProprio($membre_apercu->ID, 1, 4);
					
					foreach($objets as $key => $objet)
					{
					?>
					<div class="col-xs-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
						<div class="thumbnail" style="padding: 0; overflow: hidden; border-radius: 12px 12px 0 0; background-color: rgb(250, 250, 250); border-bottom: 3px solid rgb(200, 200, 200);">
							<div style="position: relative; width: 100%; padding-bottom: 80%;">
								<a href="annonce.php?id=<?php echo $objet->ID; ?>" class="thumbnail" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) inset; border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover;"></a>
							</div>
						
							<div class="caption">
								<h4 style="margin: 10px 0 10px 0; text-align: left;"><?php echo htmlspecialchars($objet->nom); ?></h4>
								
								<p style="text-align: left;">
									<?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?>
									<a href="membre.php?id=<?php echo $membre_apercu->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre_apercu->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : ('<img class="img-circle" alt="' . $membre_apercu->prenom . ' ' . $membre_apercu->avatar . '" style="width: 40px; height: 40px;" src="avatars/' . $membre_apercu->avatar . '">'); ?></span></a>
								</p>
							</div>
						</div>
					</div>
					<?php
					}
					?>
				</div>
				<?php
				}
				?>
				<hr />
				
				<h3>Localisation</h3>
				
				<div class="col-md-8" style="width: 100%; height: 200px;" id="map"></div>
				
				<div>
					<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp&amp;"></script>
				</div>
				
				<input type="hidden" id="lat_proprio" value="<?php echo $membre_apercu->lat; ?>">
				<input type="hidden" id="lng_proprio" value="<?php echo $membre_apercu->lng; ?>">
			</div>
		</div>
	</div>
</div>