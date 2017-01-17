<?php
$objets_manager = new ObjetsMan($this->bdd);
$objets = $objets_manager->getByProprio($membre->ID, 2);

if(empty($objets))
{
?>
	<h2>Mes annonces</h2>
	
	<div class="row">
		<div class="col-xs-12 col-sm-offset-3 col-sm-6">
			<a class="video_link" data-video="TsO8cyBJwSo"></a>
		</div>
	</div>
	
	<br />

	<p class="text-center blue_custom">
		<a href="annonce.php" class="btn btn-custom">Déposer une annonce</a>
	</p>
<?php
}
else
{
?>
	<div class="row_content">
		<div class="col-md-12">
			<h2>Mes annonces</h2>
			
			<?php
			if(isset($_SESSION['annonce_removed']))
			{
				echo '<div class="alert alert-info text-center" role="alert">Votre annonce a bien été supprimée.</div>';
				unset($_SESSION['annonce_removed']);
			}
			
			$communautes_manager = new CommunautesMan($bdd);
			$membres_manager = new MembresMan($bdd);
			?>
			
			<div class="row" style="text-align: center; padding: 2px;">
				<?php
				$objets_no_notif = array();
				
				foreach($objets as $objet)
				{
					$nbr_notifs_objet = $transactions_manager->countNotifsTransactionsAsProprioByID_objet($objet->ID);
					
					if($nbr_notifs_objet > 0)
					{
						?>
						<div class="col-xs-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
							<div class="thumbnail clignoter" style="padding: 0; overflow: hidden; border-radius: 12px 12px 0 0; background-color: rgb(250, 250, 250); border-bottom: 3px solid rgb(200, 200, 200);">
								<div style="position: relative; width: 100%; padding-bottom: 80%;">
									<a href="annonce.php?id=<?php echo $objet->ID; ?>&amp;reservations" class="thumbnail" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) inset; border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover;"></a>
								</div>
							
								<div class="caption">
									<h4 style="margin: 10px 0 10px 0; text-align: left;"><?php echo htmlspecialchars($objet->nom); ?> <span class="badge badge-custom"><?php echo $nbr_notifs_objet; ?></span></h4>
									
									<p style="text-align: left;">
										<?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?>
										<a href="membre.php?id=<?php echo $membre->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : ('<img class="img-circle" alt="' . $membre->prenom . ' ' . $membre->nom . '" style="width: 40px; height: 40px;" src="avatars/' . $membre->avatar . '">'); ?></span></a>
									</p>
								</div>
							</div>
						</div>
						<?php
					}
					else
						$objets_no_notif[] = $objet;
				}
				
				foreach($objets_no_notif as $objet)
				{
					?>
					<div class="col-xs-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
						<div class="thumbnail" style="padding: 0; overflow: hidden; border-radius: 12px 12px 0 0; background-color: rgb(250, 250, 250); border-bottom: 3px solid rgb(200, 200, 200);">
							<div style="position: relative; width: 100%; padding-bottom: 80%;">
								<a href="annonce.php?id=<?php echo $objet->ID; ?>&amp;reservations" class="thumbnail" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) inset; border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover;"></a>
							</div>
						
							<div class="caption">
								<h4 style="margin: 10px 0 10px 0; text-align: left;"><?php echo htmlspecialchars($objet->nom); ?></h4>
								
								<p style="text-align: left;">
									<?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?>
									<a href="membre.php?id=<?php echo $membre->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : ('<img class="img-circle" alt="' . $membre->prenom . ' ' . $membre->nom . '" style="width: 40px; height: 40px;" src="avatars/' . $membre->avatar . '">'); ?></span></a>
								</p>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		
		<div class="col-xs-12">
			<hr />
			<p class="text-center"><a href="annonce.php" class="btn btn-custom"><span class="glyphicon glyphicon-edit"></span> Déposer une annonce</a></p>
		</div>
	</div>
<?php
}
?>