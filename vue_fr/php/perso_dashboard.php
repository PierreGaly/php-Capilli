<?php
$parrainages_manager = new ParrainagesMan($bdd);
$membres_manager = new MembresMan($bdd);
$paiements_manager = new PaiementsMan($bdd);

$montant_tirelire = $paiements_manager->getTotalPaiements($membre->ID);
$parrain = $parrainages_manager->getParrain($membre->ID);
$filleuls = $parrainages_manager->getFilleuls($membre->ID);

$parrainages_non_vus = $infos['parrainages_non_vus'];
?>
<div class="modal fade modal_new_participant" role="dialog" aria-labelledby="gridSystemModalLabel" id="change_photo">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post" action="" class="form-inline" id="da_form_photo" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">Modifier votre photo personnelle</h4>
				</div>
				
				<div class="modal-body">
					<p class="text-center">
						<?php echo ($membre->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 120px;"></span>' : '<img src="avatars/' . $membre->avatar . '">'; ?>
					</p>
					
					<p class="text-center">
						<?php
						if($membre->avatar != '')
						{
						?>
							<button name="da_photo_remove" class="btn btn-default" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer votre photo personnelle ?')) return false;"><span class="glyphicon glyphicon-remove"></span> Supprimer ma photo</button>
						<?php
						}
						?>
					</p>
				</div>
				
				<div class="modal-footer" style="text-align: center;">
					<label for="da_photo">Nouvelle photo </label> <input type="file" class="form-control" id="da_photo" name="da_photo" accept="image/*">
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row_content">
	<div class="panel panel-default">
		<div class="panel-body">
			<h2 style="margin-top: 0;">
				<?php echo htmlspecialchars($membre->prenom) . ' ' . htmlspecialchars($membre->nom); ?>
				<br />
				<small>
				<?php
				$note = round($membres_manager->getNoteMean($membre->ID));
				
				if($note >= 0)
				{
					for($i=0; $i<5; $i++)
						echo ($i + 1 <= $note) ? '<span class="glyphicon glyphicon-star rose_custom"></span>' : '<span class="glyphicon glyphicon-star-empty rose_custom"></span>';
				}
				?>
				</small>
			</h2>
			
			
			<?php
			if(isset($_SESSION['da_photo_modified']))
			{
				echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>Votre photo personnelle a été modifiée.</div>';
				unset($_SESSION['da_photo_modified']);
			}
			else if(isset($_SESSION['da_photo_removed']))
			{
				echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>Votre photo personnelle a été supprimée.</div>';
				unset($_SESSION['da_photo_removed']);
			}
			else if(isset($_SESSION['da_photo_error']))
			{
				echo '<div class="alert alert-danger text-center" role="alert">Erreur lors de l\'envoi de votre photo personnelle.<br /><strong>Message d\'erreur : </strong><em>' . htmlspecialchars($_SESSION['da_photo_error']) . '</em></div>';
				unset($_SESSION['da_photo_error']);
			}
			
			if(isset($_SESSION['membre_just_validated_email']))
			{
				echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>Merci d\'avoir validé votre adresse mail.<br />Vous êtes maintenant connecté';
				
				if($membre->civilite == 1)
					echo 'e';
				
				echo ' sur ' . SITE_NOM . '.</div>';
				unset($_SESSION['membre_just_validated_email']);
			}
			
			foreach($parrainages_non_vus as $parrainage_non_vus)
				echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class=" glyphicon glyphicon-ok-sign"></span> <strong>Félicitations</strong>, vous avez un nouveau filleul : <em>' . $membres_manager->getMembreByID($parrainage_non_vus->ID_filleul)->sePresenter() . '</em>.</div>';
			?>
			
			<p class="text-center">
				<a id="lien_modifier" data-toggle="modal" data-target="#change_photo" href="" style="position: relative; font-size: 50px; display: inline-block;">
					<?php echo ($membre->avatar == '') ? '<span id="photo_modifier" class="glyphicon glyphicon-user" style="font-size: 120px;"></span>' : '<img id="photo_modifier" src="avatars/' . $membre->avatar . '" class="img-circle" style="width: 120px; height: 120px;">'; ?>
					<br />
					<span style="position: absolute; top: 50%;">
						<span class="glyphicon glyphicon-edit" id="modifier_icon" style="position: absolute; top: -25px; left: -25px; opacity: 0;"></span>
					</span>
				</a>
			</p>
			
			<p class="text-center">
				<span style="font-size: 1.2em;">
					<?php
						$age = $membre->getAge();
						
						echo $age . ' an';
						
						if($age > 1)
							echo 's';
						?>, <?php
						if($membre->type)
							echo ($membre->civilite) ? 'professionnelle' : 'professionnel';
						else
							echo 'particulier';
						?>.
					<br />
					<?php echo $membre->administrateur ? 'Administrateur' : 'Membre'; ?> depuis le <?php echo (new DateTime($membre->date_inscription))->format('d/m/Y'); ?>.
				</span>
			</p>
			
			<div class="row text-center">
				<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
					<img src="sources/cochon_<?php if($montant_tirelire >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
					<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire, 2, ',', ' '); ?> €</span>
				</p>
			</div>
			
			<p class="text-center">
			</p>
			
			<form method="post" action="perso.php?revenus" class="form-inline">
				<p class="text-center">
					<a class="btn btn-primary" href="perso.php?revenus">Détails de mes revenus <span class="glyphicon glyphicon-chevron-right"></span></a>
					|
					<button class="btn btn-custom" role="submit" name="revenus_vider_tirelire"<?php if($montant_tirelire <= 0) echo ' disabled'; ?> onclick="if(!confirm('Êtes-vous certain de vouloir verser le montant de votre tirelire <?php echo SITE_NOM; ?> vers votre compte bancaire ?')) return false;">Vider ma tirelire  <span class="glyphicon glyphicon-chevron-right"></span></button>
					<span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Cette action transfèrera votre solde actuel vers votre compte bancaire."></span>
				</p>
			</form>
			
			<p class="text-center" style="font-style: italic; font-size: 1.2em;">
				Depuis votre inscription, vous avez gagné <strong class="rose_custom"><?php echo number_format($paiements_manager->getTotalPaiementsByParrainage($membre->ID), 2, ',', ' '); ?> €</strong> grâce au parrainage.
			</p>
			
			<hr />
			
			<h3>Mes liens de parrainage</h3>

			<div class="row">
				
				<div class="alert alert-info text-center form-inline" role="alert">
					<label class="control-label">Lien de parrainage :</label>
					<span class="input-group" data-toggle="tooltip" data-placement="top" title="Il suffit de copier ce lien et l’envoyer à vos contacts pour toucher <?php echo PaiementsMan::POURCENTAGE_PARRAINAGE; ?>% du prix de chaque transaction qu'ils effectueront sur notre plateforme en tant que propriétaire." >
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" id="copy_link" data-clipboard-text="<?php echo SITE_PROTOCOLE . '://' . SITE_URL . '/?p=' . $membre->ID; ?>"><span class="glyphicon glyphicon-copy"></button>
						</span>
						
						<input style="cursor: default;" id="lien_parrainage" type="text" class="form-control" value="<?php echo SITE_PROTOCOLE . '://' . SITE_URL . '/?p=' . $membre->ID; ?>" size="21" readonly>
					</span>
					<br />
					<em style="font-size: 0.9em;">Il suffit de copier ce lien et l’envoyer à vos contacts pour toucher <?php echo PaiementsMan::POURCENTAGE_PARRAINAGE; ?>% du prix de chaque transaction qu'ils effectueront sur notre plateforme en tant que propriétaire.</em>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-offset-3 col-sm-6">
						<a class="video_link" data-video="g49AshKIQCk"></a>
					</div>
				</div>
				
				<br />
				
				<div class="col-md-offset-2 col-md-8">
					<table class="table table-striped table-hover" style="background-color: white;">
						<tbody>
							<tr>
								<td><label class="control-label">Mon parrain</label></td>
								<td class="text-center"><?php
								if(empty($parrain))
									echo '<em>Vous n\'avez pas de parrain.</em>';
								else
									echo $parrain->sePresenter();
								?></td>
							</tr>
							<tr>
								<td><label class="control-label">Mes filleuls</label></td>
								<td class="text-center"><?php
								if(empty($filleuls))
									echo '<em>Vous n\'avez aucun filleul.</em><br /><a href="documentation.php?tout_sur_club_de_lok#devenir_parrain">Comment devenir parrain ?</a>';
								else
								{
									foreach($filleuls as $filleul)
										echo $filleul->sePresenter() . '<br />';
								}
								?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<hr />
			
			<h3>Mes statistiques</h3>
			
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<td><label class="control-label">Annonces publiées</label></td>
								<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo $membres_manager->getTransactionsAnnoncesPubliees($membre->ID); ?></td>
							</tr>
							<tr>
								<td><label class="control-label">Locations effectuées</label></td>
								<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo $membres_manager->getTransactionsEffectueesMembre($membre->ID); ?></td>
							</tr>
							<tr>
								<td><label class="control-label">Taux de réponse</label></td>
								<td class="text-center rose_custom" style="font-size: 1.6em;"><?php
									$pourcentage = $membres_manager->getPourcentageReponses($membre->ID);
									echo ($pourcentage < 0) ? '-' : (round($pourcentage*100) . ' %'); ?></td>
							</tr>
							<tr>
								<td><label class="control-label">Temps de réponse moyen</label></td>
								<td class="text-center rose_custom" style="font-size: 1.6em;"><?php echo ($membre->TDR_nombre > 0) ? ($membre->encode_temps_reponse()) : '-'; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>