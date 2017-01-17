<?php
$objets_manager = new ObjetsMan($this->bdd);
$transactions_manager = new TransactionsMan($bdd);
$membres_manager = new MembresMan($bdd);
$communautes_manager = new CommunautesMan($bdd);

$objet = $infos['objet'];
$proprio = $objets_manager->getProprio($objet->ID_proprio);
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
		<h2><?php echo htmlspecialchars($objet->nom); ?><br /><small><?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : ('<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'); ?></small></h2>
		
		<?php
		if(isset($_POST['dates_commande']))
		{
			if($membre && $membre->ID == $objet->ID_proprio)
				echo '<div class="alert alert-danger text-center" role="alert">Vous ne pouvez pas commander des biens dont vous êtes le propriétaire.</div>';
			else
				echo '<div class="alert alert-danger text-center" role="alert">Vous devez sélectionner une période de location.</div>';
		}
		else if(isset($_SESSION['commande_ajoutee']))
		{
			?>
				<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_panier">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="gridSystemModalLabel">Votre commande</h4>
							</div>
							
							<div class="modal-body text-center">
								<div class="alert alert-info text-center" role="alert"><span class="glyphicon glyphicon-ok rose_custom" style="font-size: 2em; float: left; margin-right: 10px;"></span> Votre commande a bien été ajoutée à votre panier.</div>
								
								<a class="btn btn-custom" href="panier.php"><span class="glyphicon glyphicon-shopping-cart"></span> Consulter mon panier</a>
							</div>
							
							<div class="modal-footer" style="text-align: center;">
								<button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Poursuivre sur le site <span class="glyphicon glyphicon-chevron-right"></span></span></button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
			<?php
			unset($_SESSION['commande_ajoutee']);
		}
		
		if($membre && $membre->ID == $objet->ID_proprio)
		{
		?>
			<div class="alert alert-info text-center" role="alert">
				Bienvenue sur votre annonce.
				<br /><br />
				<a class="btn btn-primary" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;reservations"><span class="glyphicon glyphicon-calendar"></span> Gérer les réservations<?php
				$nbr_notifs_objet = $transactions_manager->countNotifsTransactionsAsProprioByID_objet($objet->ID);
				
				if($nbr_notifs_objet > 0)
					echo ' <span class="badge badge-custom">' . $nbr_notifs_objet . '</span>';
				?></a>
				<span class="hidden-xs"> | </span>
				<a class="btn btn-custom" href="annonce.php?id=<?php echo $_GET['id']; ?>&amp;edit"><span class="glyphicon glyphicon-pencil"></span> Modifier l'annonce</a>
			</div>
		<?php
		}
		
		if($objet->actif == 0)
			echo '<div class="alert alert-danger text-center" role="alert">Vous seul pouvez voir cette annonce car vous l\'avez désactivée.</div>';
		?>
		
		<div class="row">
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-body" style="padding: 0;">
						<h2>Informations sur le bien<br /><span class="small"><?php
							$note = round($objets_manager->getNoteMean($objet->ID));
							
							if($note >= 0)
							{
								for($i=0; $i<5; $i++)
									echo ($i + 1 <= $note) ? '<span class="glyphicon glyphicon-star rose_custom"></span>' : '<span class="glyphicon glyphicon-star-empty rose_custom"></span>';
							}
							?></span></h2>
						
						<div class="col-md-12" style="margin-bottom: 20px;">
							<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom">Description</h4>
							
							<p class="text-justify">
							<?php echo nl2br(htmlspecialchars($objet->description)); ?>
							</p>
						</div>
						
						<?php
						if(trim($objet->conditions_location) != '')
						{
						?>
						<div class="col-md-12" style="margin-bottom: 20px;">
							<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom">Conditions de location</h4>
							
							<p class="text-justify">
							<?php echo nl2br(htmlspecialchars($objet->conditions_location)); ?>
							</p>
						</div>
						<?php
						}
						
						if(trim($objet->conditions_utilisation) != '')
						{
						?>
						<div class="col-md-12" style="margin-bottom: 20px;">
							<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom">Conditions d’utilisation</h4>
							
							<p class="text-justify">
							<?php echo nl2br(htmlspecialchars($objet->conditions_utilisation)); ?>
							</p>
						</div>
						<?php
						}
						?>
						
						<div class="col-md-6">
							<table class="table table-striped table-hover">
								<caption class="text-center">Informations complémentaires</caption>
								<tbody>
									<tr>
										<td>Marque</td>
										<td style="text-align: right;"><?php echo ($objet->marque == '') ? '<em>Non précisée</em>' : htmlspecialchars($objet->marque); ?></td>
									</tr>
									<tr>
										<td>Modèle</td>
										<td style="text-align: right;"><?php echo ($objet->modele == '') ? '<em>Non précisé</em>' : htmlspecialchars($objet->modele); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="col-md-6">
							<table class="table table-striped table-hover">
								<caption class="text-center">Tarifs</caption>
								<tbody>
									<tr>
										<td>Journée</td>
										<td style="text-align: right; font-weight: bold;" class="rose_custom"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> €</td>
									</tr>
									<?php
									if($objet->ID != 502 && $objet->ID != 503 && $objet->ID != 504)
									{
									if($objet->prix_weekend)
									{
									?>
									<tr>
										<td>Week-end</td>
										<td style="text-align: right; font-weight: bold;" class="rose_custom"><?php echo number_format($objet->prix_weekend, 2, ',', ' '); ?> €</td>
									</tr>
									<?php
									}
									if($objet->prix_semaine)
									{
									?>
									<tr>
										<td>Semaine</td>
										<td style="text-align: right; font-weight: bold;" class="rose_custom"><?php echo number_format($objet->prix_semaine, 2, ',', ' '); ?> €</td>
									</tr>
									<?php
									}
									if($objet->prix_mois)
									{
									?>
									<tr>
										<td>Mois</td>
										<td style="text-align: right; font-weight: bold;" class="rose_custom"><?php echo number_format($objet->prix_mois, 2, ',', ' '); ?> €</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td class="blue_custom"><strong>Caution</strong><?php if($objet->cheque_caution) echo '<small class="rose_custom"><br /><span class="glyphicon glyphicon-info-sign"></span> Chèque de caution exigé</small>'; ?></td>
										<td style="text-align: right; font-weight: bold; vertical-align: middle;" class="rose_custom"><?php echo number_format($objet->caution, 2, ',', ' '); ?> €</td>
									</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
						
						<div class="col-xs-12">
							<h4 style="border-bottom: 1px solid rgb(200, 200, 200);" class="rose_custom">Comment se passe une transaction ?</h4>
							
							<div class="col-xs-12 col-sm-offset-3 col-sm-6">
								<a class="video_link" data-video="TsO8cyBJwSo"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
<!-- Include dependencies -->
<!--script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js"></script-->
<script type="text/javascript" src="moment-with-locales.js"></script>

<!--script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="bootstrap-daterangepicker-master/daterangepicker.css"-->

<link rel="stylesheet" type="text/css" href="bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js"></script>
			
			<div class="col-md-4">
				<div class="panel panel-default">
					<form class="form-horizontal" role="form" action="" method="post">
						<?php
						if($objet->ID == 502)
						{
						?>
						<div class="panel-body text-center" style="padding-left: 30px; padding-right: 30px;">
							<h2>Le 10/12/2016 à 16h</h2>
							
							<input type="hidden" name="quantite_commande" id="quantite_commande" value="1" />
							<input type="hidden" name="dates_commande" id="dates_commande" value="10/12/2016 - 10/12/2016">
							
							<p style="font-size: 2em;"><span id="prix_span"><strong class="rose_custom"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> €</strong></span></p>
						</div>
						<?php
						}
						else if($objet->ID == 503 || $objet->ID == 504)
						{
						?>
						<div class="panel-body text-center" style="padding-left: 30px; padding-right: 30px;">
							<h2>Dates bientôt fixée...</h2>
							
							<input type="hidden" name="quantite_commande" id="quantite_commande" value="1" />
							<input type="hidden" name="dates_commande" id="dates_commande" value="10/12/2016 - 10/12/2016">
							
							<p style="font-size: 2em;"><span id="prix_span"><strong class="rose_custom"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> €</strong></span></p>
						</div>
						<?php
						}
						else
						{
						?>
						<div class="panel-body text-center" style="padding-left: 30px; padding-right: 30px;">
							<h2>Dates de location</h2>
							
							<?php
							if(true)
							{
							?>
							<div class="form-group input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="span_quantity">Quantité</span> <span class="caret"></span></button>
									<input type="hidden" name="quantite_commande" id="quantite_commande" autocomplete="off" />
									<ul class="dropdown-menu">
									<?php
									for($i=0; $i<$objet->nb_objets; $i++)
										echo '<li><a href="#" class="li_quantites" onclick="return false;">' . ($i + 1) . '</a></li>';
									?>
									</ul>
								</div>
								
								<div class="form-inline input-group">
									<input type="text" class="form-control" placeholder="Date début" id="date_picker2" required autocomplete="off" style="border-radius: 0;">
									<span class="input-group-addon"> - </span>
									<input type="text" class="form-control" placeholder="Date fin" id="date_picker3" required autocomplete="off">
								</div>
								
								<input type="hidden" name="dates_commande" id="dates_commande">
							</div>
							<?php
							}
							else
							{
							?>
							<div class="form-group input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="span_quantity">Quantité</span> <span class="caret"></span></button>
									<input type="hidden" name="quantite_commande" id="quantite_commande" autocomplete="off" />
									<ul class="dropdown-menu">
									<?php
									for($i=0; $i<$objet->nb_objets; $i++)
										echo '<li><a href="#" class="li_quantites" onclick="return false;">' . ($i + 1) . '</a></li>';
									?>
									</ul>
								</div>
								
								<div class="has-feedback">
									<input type="text" class="form-control" placeholder="Dates" id="date_picker" name="dates_commande" size="18" style="border-radius: 0;" required>
									<i class="glyphicon glyphicon-calendar form-control-feedback" style="position: absolute; right: 0;"></i>
								</div>
							</div>
							<?php
							}
							?>
							
							<p style="font-size: 2em;"><span id="prix_span"><strong class="rose_custom"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> €</strong> <small style="color: grey;">/ jour</small></span></p>
						</div>
						<?php
						}
						
						if($objet->ID != 503 && $objet->ID != 504)
						{
						?>
						<div class="panel-footer" style="padding-top: 30px; padding-bottom: 30px;">
							<input type="hidden" id="prix_journee" value="<?php echo number_format($objet->prix_journee, 2, ',', ' '); ?>">
							<input type="hidden" id="ID_objet" value="<?php echo $objet->ID; ?>">
							<button class="form-control input-lg btn-lg btn btn-custom" role="submit"><span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span> Ajouter au panier</button>
						</div>
						<?php
						}
						?>
					</form>
				</div>
				
				<div class="col-md-8" style="width: 100%; height: 200px; padding: 0;" id="map"></div>
				
				<div>
					<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>"></script>
				</div>
				
				<input type="hidden" id="lat_proprio" value="<?php echo $proprio->lat; ?>">
				<input type="hidden" id="lng_proprio" value="<?php echo $proprio->lng; ?>">
			</div>
		</div>
	</div>
</div>

<div class="row" style="padding: 0;">
	<div class="row_content">
		<h2>Le propriétaire</h2>
		
		<div class="panel panel-default" style="margin-top: 60px;">
			<div style="margin: auto; width: 100px; position: relative;">
				<?php
				if($proprio->avatar == '')
					echo '<span class="glyphicon glyphicon-user" style="position: absolute; top: -50px; font-size: 100px;"></span>';
				else
					echo '<img src="avatars/' . $proprio->avatar . '" class="img-circle" style="position: absolute; top: -50px; width: 100px; height: 100px;">';
				?>
			</div>
			
			<div class="panel-heading text-center" style="padding-top: 50px;">
				<h4 class="blue_custom"><a href="membre.php?id=<?php echo $proprio->ID; ?>"><?php echo htmlspecialchars($proprio->prenom) . ' ' . htmlspecialchars($proprio->nom); ?></a></h4>
				
				<p style="margin: 0;">
					<?php
					$pourcentage = $membres_manager->getPourcentageReponses($proprio->ID);
					
					if($proprio->TDR_nombre > 0)
						echo 'Répond en <strong>' . $proprio->encode_temps_reponse() . '</strong>';
					
					if($proprio->TDR_nombre > 0 && $pourcentage >= 0)
						echo '<br />';
					
					if($pourcentage >= 0)
						echo '<strong>' . round($pourcentage * 100) . ' %</strong> de taux de réponse';
					?>
					<br />
					<a href="perso.php?messages&amp;d1=<?php echo $proprio->ID; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> Envoyer un message</a>
				</p>
			</div>
			
			<div class="panel-body">
				<?php
				$commentaires = $transactions_manager->getCommentaires($proprio->ID);
				$membres_manager = new MembresMan($bdd);
				
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
										echo '<img class="media-object img-circle" src="avatars/' . $membre_commentaire->avatar . '" style="width: 50px; height: 50px;">';
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
	</div>
</div>