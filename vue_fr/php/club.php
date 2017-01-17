<?php
$communautes_manager = new CommunautesMan($bdd);
$membres_manager = new MembresMan($bdd);
$club = $infos['club'];

$isMember = $membre ? ($membre->administrateur ? 2 : $communautes_manager->isMemberOrPendingMember($club->ID, $membre->ID)) : 0;
$nombre_annonces = $communautes_manager->countAnnoncesByCommunaute($club->ID);;
$nombre_membres = $communautes_manager->countMembersByCommunaute($club->ID);
$nombre_messages = $communautes_manager->countMessagesByCommunaute($club->ID);

if($isMember != 2)
	$onglet = -1;
else if(isset($_GET['forum']))
	$onglet = 1;
else if(isset($_GET['membres']))
	$onglet = 2;
else if(isset($_GET['demandes']))
	$onglet = 3;
else
	$onglet = 0;
?>
<div class="row" style="padding-bottom: 0;">
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
		<!-- Wrapper for slides -->
		<div class="carousel-inner text-center" role="listbox" style="font-size: 0;">
			<div class="item active">
				<div style="display: inline-block;">
					<span style="vertical-align: middle; display: table-cell;">
						<img src="<?php echo IMAGES_COMMUNAUTES . $club->image; ?>" alt="<?php echo htmlspecialchars($club->nom); ?>" style="max-height: <?php echo CommunautesMan::PHOTO_MAX_HAUTEUR; ?>px; max-width: 100%;">
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h2><?php echo htmlspecialchars($club->nom); ?></h2>
		
		<?php
		if($isMember == 1)
			echo '<div class="alert alert-info text-center" role="alert">Votre demande d\'adhésion est en attente d\'acceptation.</div>';
		else if($isMember == 0)
			echo '<div class="alert alert-info text-center" role="alert">Vous n\'avez pas accès à ce club car vous n\'êtes pas membre.<br /><br /><a class="btn btn-primary" href="clubs.php?id=' . $club->ID . '"><span class="glyphicon glyphicon-ok"></span> Je souhaite en faire partie</a></div>';
		else if($isMember == 2 && isset($_SESSION['communaute_created']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Le club <em>' . htmlspecialchars($club->nom) . '</em> a été créé.</div>';
			unset($_SESSION['communaute_created']);
		}
		
		if($infos['isNotif'])
			echo '<div class="alert alert-info text-center" role="alert"><strong>Félicitations !</strong><br />Vous faîtes désormais partie du club <em>' . htmlspecialchars($club->nom) . '</em>.</div>';
		?>
		
		<div class="row">
			<div class="col-xs-12 col-sm-4 col-md-3">
				<div class="panel panel-primary">
					<div class="panel-heading text-center" style="font-size: 1.3em;">Informations générales</div>
					<ul class="list-group">
						<li class="list-group-item text-center"><span class="glyphicon glyphicon-list-alt"></span> <?php echo $nombre_annonces . ' annonce'; if($nombre_annonces > 1) echo 's';?></li>
						<li class="list-group-item text-center"><span class="glyphicon glyphicon-comment"></span> <?php echo $nombre_messages . ' message'; if($nombre_messages > 1) echo 's';?></li>
						<li class="list-group-item text-center"><span class="glyphicon glyphicon-user"></span> <?php echo $nombre_membres . ' membre'; if($nombre_membres > 1) echo 's';?></li>
						<li class="list-group-item text-center"><span class="glyphicon glyphicon-calendar"></span> A ouvert le <?php echo (new DateTime($club->date_creation))->format('d/m/Y'); ?></li>
					</ul>
				</div>
			</div>
			
			<div class="col-xs-12 col-sm-8 col-md-9">
				<div class="panel panel-default">
					<div class="panel-heading" style="font-size: 1.3em;">Description du club</div>
					<div class="panel-body">
						<?php echo nl2br(htmlspecialchars($club->description)); ?>
					</div>
				</div>
			</div>
		</div>
		
		<?php
		if($onglet != -1)
		{
		?>
		<hr />
		
		<div class="panel panel-default" style="margin-top: 20px;">
			<div class="panel-heading">
				<div class="btn-group btn-group-justified" role="group">
					<div class="btn-group" role="group">
						<a href="club.php?id=<?php echo $club->ID; ?>" class="btn btn-default<?php if($onglet == 0) echo ' active'; ?>"><span class="glyphicon glyphicon-list-alt"></span><span class="hidden-xs"> Dernières annonces</span></a>
					</div>
					<div class="btn-group" role="group">
						<a href="club.php?id=<?php echo $club->ID; ?>&amp;forum" class="btn btn-default<?php if($onglet == 1) echo ' active'; ?>"><span class="glyphicon glyphicon-comment"></span><span class="hidden-xs"> Forum</span></a>
					</div>
					<div class="btn-group" role="group">
						<a href="club.php?id=<?php echo $club->ID; ?>&amp;membres" class="btn btn-default<?php if($onglet == 2) echo ' active'; ?>"><span class="glyphicon glyphicon-user"></span><span class="hidden-xs"> Membres</span></a>
					</div>
					<div class="btn-group" role="group">
						<a href="club.php?id=<?php echo $club->ID; ?>&amp;demandes" class="btn btn-default<?php if($onglet == 3) echo ' active'; ?>"><span class="glyphicon glyphicon-question-sign"></span><span class="hidden-xs"> Demandes d'adhésion</span><?php
						$nbr_notif_demandes = $communautes_manager->countDemandesPendingByCommunaute($club->ID);
						
						if($nbr_notif_demandes > 0)
							echo ' <span class="badge badge-custom">' . $nbr_notif_demandes . '</span>';
						?></a>
					</div>
				</div>
			</div>
			<div class="panel-body">
		<?php
		}
		
		if($onglet == 0)
		{
		?>
			<div class="row" style="text-align: center;">
				<?php
				$objets_manager = new ObjetsMan($bdd);
				$objets = $objets_manager->getByClub($club->ID, 3);
				
				if(empty($objets))
					echo '<p class="text-center"><em>Aucune annonce.</em></p>';
				else
				{
					foreach($objets as $objet)
					{
						$membre_annonce = $membres_manager->getMembreByID($objet->ID_proprio);
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
											<a href="membre.php?id=<?php echo $membre_annonce->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre_annonce->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : ('<img class="img-circle" style="width: 40px; height: 40px;" alt="' . $membre_annonce->prenom . ' ' . $membre_annonce->nom . '" src="avatars/' . $membre_annonce->avatar . '">'); ?></span></a>
										</p>
									</div>
								</div>
							</div>
						<?php
					}
				}
				?>
			</div>
			
			<p class="text-center">
				<a href="recherche.php?cl=<?php echo $club->ID; ?>" class="btn btn-primary">Explorer toutes les annonces du club <span class="glyphicon glyphicon-chevron-right"></span></a>
			</p>
		<?php
		}
		else if($onglet == 1)
		{
		?>
			<div class="row" style="background-color: white; border: 1px solid rgb(200, 200, 200); border-radius: 20px; overflow: hidden; margin: 20px;">
				<div class="media" style="padding: 15px; background-color: rgb(240, 240, 240);">
					<div class="media-body">
						<h4 class="media-heading">Votre message</h4>
						
						<div class="alert alert-danger text-center" role="alert" style="display: none;" id="alert_message">
							Il est interdit d’échanger les numéros de téléphone via notre système de messagerie. Si vous voulez le numéro de téléphone de la personne, vous devez effectuer une réservation et il faut que celle ci soit acceptée par le propriétaire.
						</div>
						
						<form method="post" action="" class="text-center">
							<textarea name="message" id="message" class="form-control" placeholder="Entrez votre message" autofocus></textarea>
							<button class="btn btn-custom" id="bouton_envoyer_message" role="submit" disabled>Envoyer <span class="glyphicon glyphicon-chevron-right"></span></button>
						</form>
					</div>
					
					<div class="media-right hidden-xs">
						<a href="#">
							<?php
							if($membre->avatar == '')
								echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
							else
								echo '<img class="media-object" src="avatars/' . $membre->avatar . '" style="width: 100px; height: 100px;">';
							?>
						</a>
					</div>
				</div>
				
				<?php
				$nombre_resultats_par_page = 5;
				
				$nbr_pages = max(ceil($nombre_messages/$nombre_resultats_par_page), 1);
				
				if(isset($_GET['p']) && ((int) $_GET['p']) > 1 && ((int) $_GET['p']) <= $nbr_pages)
					$page_number = (int) $_GET['p'];
				else
					$page_number = 1;
				
				$messages = $communautes_manager->getMessagesByCommunaute($club->ID, ($page_number - 1)*$nombre_resultats_par_page, $nombre_resultats_par_page);
				
				foreach($messages as $message)
				{
				?>
					<hr style="margin: 0" />
					
					<div class="media" style="padding: 15px;">
						<?php
						if($message->ID_membre != $membre->ID)
						{
							$membre_message = $membres_manager->getMembreByID($message->ID_membre);
							?>
							<div class="media-left">
								<a href="#">
									<?php
									if($membre_message->avatar == '')
										echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
									else
										echo '<img class="media-object" src="avatars/' . $membre_message->avatar . '" style="width: 100px; height: 100px;">';
									?>
								</a>
							</div>
							<?php
						}
						?>
						
						<div class="media-body">
							<h4 class="media-heading">Le <?php echo (new DateTime($message->date_creation))->format('d/m/Y à H:i'); ?></h4>
							
							<?php echo nl2br(htmlspecialchars($message->message)); ?>
						</div>
						
						<?php
						if($message->ID_membre == $membre->ID)
						{
							?>
							<div class="media-right">
								<a href="#">
									<?php
									if($membre->avatar == '')
										echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
									else
										echo '<img class="media-object" src="avatars/' . $membre->avatar . '" style="width: 100px; height: 100px;">';
									?>
								</a>
							</div>
							<?php
						}
						?>
					</div>
				<?php
				}
				?>
				
				<hr style="margin: 0" />
				
				<div class="media">
					<div style="width: 100%;">
						<div class="text-center">
							<ul class="pagination">
								<li class="hidden-xs hidden-sm<?php if($page_number == 1) echo ' disabled'; ?>"><a href="club.php?id=<?php echo $club->ID; ?>&amp;p=1" aria-label="Début"><span aria-hidden="true">&laquo;</span></a></li>
								<li class="hidden-xs<?php if($page_number == 1) echo ' disabled'; ?>"><a href="club.php?id=<?php echo $club->ID; ?>&amp;p=<?php echo max(1, $page_number-1); ?>" aria-label="Précédant"><span aria-hidden="true">&lsaquo;</span></a></li>
								<?php
								for($i=1; $i<=$nbr_pages; $i++)
								{
									echo '<li';
									
									if($i == $page_number)
										echo ' class="active"';
									
									echo '><a href="club.php?id=' . $club->ID . '&amp;p=' . $i . '">' . $i . '</a></li>';
								}
								?>
								<li class="hidden-xs<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="club.php?id=<?php echo $club->ID; ?>&amp;p=<?php echo min($nbr_pages, $page_number+1); ?>" aria-label="Suivant"><span aria-hidden="true">&rsaquo;</span></a></li>
								<li class="hidden-xs hidden-sm<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="club.php?id=<?php echo $club->ID; ?>&amp;p=<?php echo $nbr_pages; ?>" aria-label="Fin"><span aria-hidden="true">&raquo;</span></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
		else if($onglet == 2)
		{
		?>
			<div class="row" style="text-align: center;">
				<?php
				$membres_manager = new MembresMan($bdd);
				$membres_club = $membres_manager->getByClub($club->ID, 1);
				
				if(empty($membres_club))
					echo '<p class="text-center"><em>Aucun membre.</em></p>';
				else
				{
					foreach($membres_club as $membre_club)
					{
						?>
							<div class="col-sm-6 col-md-4" style="display: inline-block; float: none; margin-right:-3px;">
								<div class="panel panel-default" style="margin-top: 60px;">
									<div style="margin: auto; width: 100px; position: relative;">
										<?php
										if($membre_club->avatar == '')
											echo '<span class="glyphicon glyphicon-user" style="position: absolute; top: -50px; left: 5px; font-size: 100px;"></span>';
										else
											echo '<img src="avatars/' . $membre_club->avatar . '" class="img-circle" style="position: absolute; top: -50px; left: 5px; width: 100px; height: 100px;">';
										?>
									</div>
									
									<div class="panel-heading text-center" style="padding-top: 50px;">
										<h4 class="blue_custom"><a href="membre.php?id=<?php echo $membre_club->ID; ?>"><?php echo htmlspecialchars($membre_club->prenom) . ' ' . htmlspecialchars($membre_club->nom); ?></a></h4>
										
										<p style="margin: 0;">
											<?php
											$pourcentage = $membres_manager->getPourcentageReponses($membre_club->ID);
											
											if($membre_club->TDR_nombre > 0)
												echo 'Répond en <strong>' . $membre_club->encode_temps_reponse() . '</strong>';
											
											if($membre_club->TDR_nombre > 0 && $pourcentage >= 0)
												echo '<br />';
											
											if($pourcentage >= 0)
												echo '<strong>' . round($pourcentage * 100) . ' %</strong> de taux de réponse';
											?>
										</p>
									</div>
								</div>
							</div>
						<?php
					}
				}
				?>
			</div>
		<?php
		}
		else if($onglet == 3)
		{
		?>
			<?php
			if(isset($_SESSION['demande_accepted']))
			{
				if($_SESSION['demande_accepted'])
					echo '<div class="alert alert-info text-center" role="alert">Vous avez accepté la demande d\'adhésion.</div>';
				else
					echo '<div class="alert alert-danger text-center" role="alert">La demande d\'adhésion n\'a pas pu être acceptée.</div>';
				
				unset($_SESSION['demande_accepted']);
			}
			?>
			
			<div class="row" style="text-align: center; padding: 2px;">
				<?php
				$membres_manager = new MembresMan($bdd);
				$membres_club = $membres_manager->getByClub($club->ID, 0);
				
				if(empty($membres_club))
					echo '<div class="alert alert-info text-center" role="alert">Aucune demande en attente.</div>';
				else
				{
					foreach($membres_club as $membre_club)
					{
						?>
							<div class="col-sm-6 col-md-4" style="display: inline-block; float: none; margin: -2px;">
								<div class="panel panel-default" style="margin-top: 60px;">
									<div style="margin: auto; width: 100px; position: relative;">
										<?php
										if($membre_club->avatar == '')
											echo '<span class="glyphicon glyphicon-user" style="position: absolute; top: -50px; left: 5px; font-size: 100px;"></span>';
										else
											echo '<img src="avatars/' . $membre_club->avatar . '" class="img-circle" style="position: absolute; top: -50px; left: 5px; width: 100px; height: 100px;">';
										?>
									</div>
									
									<div class="panel-heading text-center" style="padding-top: 50px;">
										<h4 class="blue_custom"><a href="membre.php?id=<?php echo $membre_club->ID; ?>"><?php echo htmlspecialchars($membre_club->prenom) . ' ' . htmlspecialchars($membre_club->nom); ?></a></h4>
									</div>
									
									<form method="post" action="">
										<div class="panel-body">
											<input type="hidden" name="id_membre_accept" value="<?php echo $membre_club->ID; ?>">
											<button class="btn btn-custom" role="submit"><span class="glyphicon glyphicon-ok"></span> Accepter la demande</button>
										</div>
									</form>
								</div>
							</div>
						<?php
					}
				}
				?>
			</div>
		<?php
		}
		
		if($onglet != -1)
		{
		?>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>