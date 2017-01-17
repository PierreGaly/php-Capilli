<?php
$transactions_manager = new TransactionsMan($bdd);
$objets_manager = new ObjetsMan($this->bdd);
$communautes_manager = new CommunautesMan($bdd);
$membres_manager = new MembresMan($bdd);

$objet = $infos['objet'];
$proprio = $objets_manager->getProprio($objet->ID_proprio);
$nom_html = htmlspecialchars($objet->nom);
$photos = glob(IMAGES_BIENS . $objet->ID . '/*.*');
$transactions = $infos['transactions'];
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
			Bienvenue sur la page de gestion des réservations de votre annonce.
			<br /><br />
			<a class="btn btn-primary active" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;reservations"><span class="glyphicon glyphicon-calendar"></span> Gérer les réservations<?php
			$nbr_notifs_objet = $transactions_manager->countNotifsTransactionsAsProprioByID_objet($objet->ID);
			
			if($nbr_notifs_objet > 0)
				echo ' <span class="badge badge-custom">' . $nbr_notifs_objet . '</span>';
			?></a>
			<span class="hidden-xs"> | </span>
			<a class="btn btn-custom" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;edit"><span class="glyphicon glyphicon-pencil"></span> Modifier l'annonce</a>
		</div>
		
		<?php
		if($objet->actif == 0)
			echo '<div class="alert alert-danger text-center" role="alert">Vous seul pouvez voir cette annonce car vous l\'avez désactivée.</div>';
		?>
		
		<div class="row">
			<div class="panel panel-default">
				<!--div class="panel-heading">
					<div class="btn-group btn-group-justified" role="group">
						<div class="btn-group" role="group">
							<a role="button" class="btn btn-custom<?php //if($res_type == 'a') echo ' active'; ?>" href="annonce.php?id=<?php //echo $objet->ID; ?>&amp;reservations=a">Réservations en attente<?php
							/*$transactions_state_a = $transactions_manager->getTransactionsProprio($membre->ID, 'a', 0);
							$nbr_notif_transactions = 0;
							
							foreach($transactions_state_a as $transaction)
								$nbr_notif_transactions += $transactions_manager->countNotifsTransactionsAsProprioByID_transation($transaction->ID);
							
							echo ' (' . count($transactions_state_a) . ')';
							
							if($nbr_notif_transactions > 0)
								echo ' <span class="badge badge-custom">' . $nbr_notif_transactions . '</span>';
							*/?></a>
						</div>
						<div class="btn-group" role="group">
							<a role="button" class="btn btn-default<?php //if($res_type == 'p') echo ' active'; ?>" href="annonce.php?id=<?php //echo $objet->ID; ?>&amp;reservations=p">Réservations passées<?php
							/*$transactions_state_p = $transactions_manager->getTransactionsProprio($membre->ID, 'p', 1);
							$nbr_notif_transactions = 0;
							
							foreach($transactions_state_p as $transaction)
								$nbr_notif_transactions += $transactions_manager->countNotifsTransactionsAsProprioByID_transation($transaction->ID);
							
							echo ' (' . count($transactions_state_p) . ')';
							
							if($nbr_notif_transactions > 0)
								echo ' <span class="badge badge-custom">' . $nbr_notif_transactions . '</span>';
							*/?></a>
						</div>
						<div class="btn-group" role="group">
							<a role="button" class="btn btn-default<?php //if($res_type == 'c') echo ' active'; ?>" href="annonce.php?id=<?php //echo $objet->ID; ?>&amp;reservations=c">Réservations en cours<?php
							/*$transactions_state_c = $transactions_manager->getTransactionsProprio($membre->ID, 'c', 1);
							$nbr_notif_transactions = 0;
							
							foreach($transactions_state_c as $transaction)
								$nbr_notif_transactions += $transactions_manager->countNotifsTransactionsAsProprioByID_transation($transaction->ID);
							
							echo ' (' . count($transactions_state_c) . ')';
							
							if($nbr_notif_transactions > 0)
								echo ' <span class="badge badge-custom">' . $nbr_notif_transactions . '</span>';
							*/?></a>
						</div>
						<div class="btn-group" role="group">
							<a role="button" class="btn btn-default<?php //if($res_type == 'v') echo ' active'; ?>" href="annonce.php?id=<?php //echo $objet->ID; ?>&amp;reservations=v">Réservations à venir<?php
							/*$transactions_state_v = $transactions_manager->getTransactionsProprio($membre->ID, 'v', 1);
							$nbr_notif_transactions = 0;
							
							foreach($transactions_state_v as $transaction)
								$nbr_notif_transactions += $transactions_manager->countNotifsTransactionsAsProprioByID_transation($transaction->ID);
							
							echo ' (' . count($transactions_state_v) . ')';
							
							if($nbr_notif_transactions > 0)
								echo ' <span class="badge badge-custom">' . $nbr_notif_transactions . '</span>';
							*/?></a>
						</div>
					</div>
				</div-->
				<div class="panel-body">
					<script type="text/javascript" src="bootstrap-year-calendar-master/bootstrap-year-calendar.min.js"></script>
					<script type="text/javascript" src="bootstrap-year-calendar-master/bootstrap-year-calendar.fr.min.js"></script>
					<link rel="stylesheet" type="text/css" href="bootstrap-year-calendar-master/bootstrap-year-calendar.min.css" />
					
					<div class="list-group">
						<a class="list-group-item disabled" href="" style="cursor: default;" onclick="return false;">
							<h3 style="margin: 0;">Réservations</h3>
						</a>
						<?php
						if(empty($transactions))
						{
						?>
							<a href="" class="list-group-item text-center" style="cursor: default;" onclick="return false;">
								<span class="glyphicon glyphicon-check  blue_custom" style="font-size: 100px;"></span>
								<br />
								Aucune réservation
							</a>
						<?php
						}
						else
						{
							foreach($transactions as $transaction)
							{
								$objet = $objets_manager->getByID($transaction->ID_objet, 2);
								$locataire = $membres_manager->getMembreByID($transaction->ID_locataire);
								$nbr_notifs_transaction = $transactions_manager->countNotifsTransactionsAsProprioByID_transation($transaction->ID);
								?>
								<input type="hidden" class="cal_dates" value="<?php echo date_format(date_create($transaction->date_debut_loc), 'Ymd') . date_format(date_create($transaction->date_fin_loc), 'Ymd') . $transaction->ID; ?>">
								
								<a href="reservation.php?id=<?php echo $transaction->ID; ?>" class="list-group-item<?php if($nbr_notifs_transaction > 0) echo ' clignoter'; ?>">
									<p class="list-group-item-text pull-right">
										<span class="badge badge-custom" style="font-size: 1.2em;"><?php echo number_format($transaction->quantite * (($proprio->ID == $membre->ID) ? $transaction->prix_unitaire_proprio : $transaction->prix_unitaire_locataire), 2, ',', ' '); ?> €</span>
									</p>
									
									<span class="pull-left">
										<?php
										echo ($locataire->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 50px; margin-right: 10px;"></span>' : '<img src="avatars/' . $locataire->avatar . '" class="img-circle" style="width: 50px; height: 50px; margin-right: 10px;"/>';
										?>
									</span>
									
									<h4 class="list-group-item-heading" id="text_transaction_<?php echo $transaction->ID; ?>">
										<?php
										if($transaction->annulation == 1)
											echo '<span class="label label-custom">Annulée</span>';
										else if($transaction->reponse == 2)
											echo '<span class="label label-primary">En attente</span>';
										else if($transaction->reponse == 1)
											echo '<span class="label label-custom">Validée</span>';
										else if($transaction->reponse == 0)
											echo '<span class="label label-custom">Refusée</span>';
										?>
										<span style="font-style: italic;">du <?php echo (new DateTime($transaction->date_debut_loc))->format('d/m/Y'); ?> au <?php echo (new DateTime($transaction->date_fin_loc))->format('d/m/Y'); ?></span>
									</h4>
									
									<p class="list-group-item-text">
										<span class="blue_custom"><?php echo $locataire->prenom . ' ' . $locataire->nom; ?></span> a réservé : <span class="blue_custom"><?php echo htmlspecialchars($objet->nom); ?></span> <strong>x <?php echo $transaction->quantite; ?></strong></span>
									</p>
								</a>
								<?php
							}
						}
						?>
					</div>
					
					<hr style="margin: 50px 0 10px 0" />
					
					<div class="row hidden-xs">
						<div id="calendar" class="calendar" style="overflow: initial;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>