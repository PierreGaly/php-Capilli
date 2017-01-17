<?php
$membres_manager = new MembresMan($bdd);
$transactions_manager = new TransactionsMan($bdd);
$objets_manager = new ObjetsMan($this->bdd);

$transaction = $infos['transaction'];
$objet = $infos['objet'];

$proprio = $objets_manager->getProprio($objet->ID_proprio);
$locataire = $membres_manager->getMembreByID($transaction->ID_locataire);

$today = new DateTime((new DateTime('now'))->format('Y-m-d'));
$etapes_transactions = array(0, 0, 0, 0, 0, 0);

$transaction_dates = $transactions_manager->getTransactions_dates($transaction->ID);
$transaction_code = $transactions_manager->getTransaction_codeByID_transaction($transaction->ID);
$transaction_messages = $transactions_manager->getTransactions_messages($transaction->ID);

$membre_last_proposition = $membres_manager->getMembreByID($transaction_dates[0]->ID_membre);
$membre_not_last_proposition = ($membre_last_proposition->ID == $proprio->ID) ? $locataire : $proprio;

if($transaction->annulation == 1)
{
	$transaction_annulation = $transactions_manager->getTransaction_annulationByID_transaction($transaction->ID);
	$membre_annulation = $membres_manager->getMembreByID($transaction_annulation->ID_membre);
}

if($transaction->annulation == 0 && (($transaction->reponse == 2 && new DateTime($transaction->date_debut_loc) >= $today) || $transaction->reponse == 1))
{
	$etapes_transactions[0] = 1;
	
	if($transaction->reponse == 1)
	{
		$etapes_transactions[1] = 1;
		
		if(new DateTime($transaction->date_debut_loc) > $today)
			$etapes_transactions[2] = 2;
		else
		{
			$etapes_transactions[2] = 1;
			
			if($transaction_code->date_validation == 0)
				$etapes_transactions[3] = 2;
			else
				$etapes_transactions[3] = 1;
			
			if(new DateTime($transaction->date_fin_loc) > $today)
				$etapes_transactions[4] = 2;
			else if(new DateTime($transaction->date_fin_loc) == $today)
			{
				$etapes_transactions[4] = 2;
				$etapes_transactions[5] = 2;
			}
			else
			{
				$etapes_transactions[4] = 1;
				$etapes_transactions[5] = 1;
			}
		}
	}
	else
		$etapes_transactions[1] = 2;
}
?>
<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_annuler_reservation">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<form role="form" method="post" action="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">Annulation de la réservation</h4>
				</div>
				
				<div class="modal-body">
					Êtes-vous certain de vouloir annuler cette réservation ?
					
					<?php
					if($transaction->reponse == 1)
						echo '<br /><br /><div class="alert alert-info text-center" role="alert"><span class="glyphicon glyphicon-exclamation-sign"></span> Une commission à hauteur de 20% du prix de la transaction vous sera prélevée car la réservation a été acceptée par les deux parties.</div>';
					?>
				</div>
				
				<div class="modal-footer">
					<button class="btn btn-custom" name="reservation_annuler" role="submit">Annuler la réservation <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_reservation" style="z-index: 99;">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<form role="form" method="post" action="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modal_reservation_titre"></h4>
				</div>
				
				<div class="modal-body" id="modal_reservation_body">
				</div>
				
				<div class="modal-footer">
					<input type="hidden" name="reservation_choix" id="reservation_choix">
					<input type="hidden" name="reservation_proposer" id="reservation_proposer_hidden">
					<button class="btn btn-custom" role="submit"><span id="modal_reservation_button"></span> <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row" style="padding: 0;">
	<div class="row_content">
		<div class="row">
			<div class="panel panel-default" style="margin: 0;">
				<div class="panel-body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 style="margin: 0;">Détails de la réservation</h3>
						</div>
						
						<div class="panel-body" style="padding: 10px;">
							<p class="list-group-item-text pull-right">
								<span class="badge badge-custom" style="font-size: 1.2em;"><?php echo number_format($transaction->quantite * (($proprio->ID == $membre->ID) ? $transaction->prix_unitaire_proprio : $transaction->prix_unitaire_locataire), 2, ',', ' '); ?> €</span>
							</p>
							
							<span class="pull-left">
								<?php
								echo ($locataire->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 42px; margin-right: 10px;"></span>' : '<img src="avatars/' . $locataire->avatar . '" class="img-circle" style="width: 50px; height: 50px; margin-right: 10px;"/>';
								?>
							</span>
							
							<h4 class="list-group-item-heading">
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
								<?php echo $locataire->sePresenter(); ?> a réservé : <a href="annonce.php?id=<?php echo $objet->ID; ?>"><?php echo htmlspecialchars($objet->nom); ?></a> <strong>x <?php echo $transaction->quantite; ?></strong>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" style="padding: 0;">
	<div class="row_content">
		<div class="row">
			<div class="panel panel-default" style="margin: 0;">
				<div class="panel-body">
					<h2>Les <span class="rose_custom">6</span> étapes de la transaction<br /><small>Passez la souris sur une étape pour plus d'informations</small></h2>
					
					<div class="row text-center" style="margin-bottom: 20px;">
						<?php
						$etapes = array(array('share', 'Le locataire demande une réservation'),
										array('check', 'Les deux parties se mettent d\'accord sur les termes du contrat'),
										array('phone-alt', 'Mise en relation des deux parties'),
										array('qrcode', 'Le locataire donne son code de réservation au propriétaire et les deux parties remplissent les contrats de location'),
										array('time', 'Le propriétaire saisit le code pour recevoir son argent tandis que locataire utilise le bien'),
										array('duplicate', 'Locataire rend l’objet à son propriétaire et les deux parties remplissent la partie du contrat « retour de l’objet »'));
						
						foreach($etapes as $key => $etape)
						{
							echo '<div data-toggle="tooltip" data-placement="top" title="' . $etape[1] . '" class="col-xs-4 col-sm-2';
							
							if($etapes_transactions[$key] == 1)
								echo ' blue_custom';
							else if($etapes_transactions[$key] == 2)
								echo ' rose_custom';// tooltip_keep_shown" data-trigger="manual
							
							echo '"><div style="margin: auto; width: 88px; height: 88px; border: 4px solid grey; border-radius: 44px; position: relative; font-size: 40px;"><span style="position: absolute; top: 22px; left: 20px;" class="glyphicon glyphicon-' . $etape[0] . '"></span>';
							
							if($etapes_transactions[$key] == 1)
								echo '<span style="font-size: 17px; position: absolute; top: 60px; right: -15px;" class="glyphicon glyphicon-ok"></span>';
							else if($etapes_transactions[$key] == 2)
								echo '<span style="font-size: 17px; position: absolute; top: 60px; right: -15px;" class="glyphicon glyphicon-hourglass"></span>';
							
							echo '</div></div>';
						}
						?>
					</div>
					
					<?php
					if($transaction->annulation == 1)
					{
						if($membre_annulation->ID == $membre->ID)
							echo '<div class="alert alert-danger text-center" role="alert">Vous avez annulé la réservation.</div>';
						else
							echo '<div class="alert alert-danger text-center" role="alert">' . $membre_annulation->sePresenter() . ' a annulé la réservation.</div>';
					}
					else if($transaction->reponse == 0)
					{
						if($transaction_dates[0]->ID_membre != $membre->ID)
							echo '<div class="alert alert-danger text-center" role="alert">Vous avez refusé la réservation.</div>';
						else
							echo '<div class="alert alert-danger text-center" role="alert">' . $membre_last_proposition->sePresenter() . ' a refusé la réservation.</div>';
					}
					else if($transaction->reponse == 2)
					{
						if(new DateTime($transaction->date_debut_loc) < $today)
						{
							if($transaction_dates[0]->ID_membre != $membre->ID)
								echo '<div class="alert alert-danger text-center" role="alert">Vous n\'avez pas répondu à temps.</div>';
							else
								echo '<div class="alert alert-danger text-center" role="alert">' . $membre_not_last_proposition->sePresenter() . ' n\'a pas répondu à temps.</div>';
						}
						else
						{
							if($transaction_dates[0]->ID_membre == $membre->ID)
								echo '<div class="alert alert-info text-center" role="alert">En attente de la réponse de ' . $membre_not_last_proposition->sePresenter() . '.</div>';
							else
							{
							?>
								<!-- Include dependencies -->
								<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js"></script>
								<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
								<link rel="stylesheet" type="text/css" href="bootstrap-daterangepicker-master/daterangepicker.css">
								<form method="post" action="">
									<div class="panel panel-default text-center">
										<div class="panel-heading">
											<h4 class="media-heading">Que voulez-vous faire ?</h4>
										</div>
										
										<div class="panel-body">
											<div class="btn-group">
												<button type="button" class="btn btn-custom" id="reservation_accepter"><span class="glyphicon glyphicon-ok"></span> Accepter</button>
											</div>
											|
											<div class="btn-group has-feedback form-inline">
												<input type="hidden" id="minDate" value="<?php echo date_create($transaction->date_debut_loc)->format('d/m/Y'); ?>">
												<input type="hidden" id="maxDate" value="<?php echo date_create($transaction->date_fin_loc)->format('d/m/Y'); ?>">
												<input type="text" class="form-control" placeholder="Proposer d'autres dates" id="reservation_proposer" name="reservation_proposer" size="20">
												<i class="glyphicon glyphicon-calendar form-control-feedback" style="position: absolute; right: 0;"></i>
											</div>
											|
											<div class="btn-group">
												<button type="button" class="btn btn-primary" id="reservation_refuser"><span class="glyphicon glyphicon-remove"></span> Refuser</button>
											</div>
										</div>
									</div>
								</form>
							<?php
							}
						}
					}
					else
					{
						if($membre->ID == $locataire->ID || $membre->administrateur)
							require_once('reservation_locataire.php');
						
						if($membre->ID == $proprio->ID || $membre->administrateur)
							require_once('reservation_proprio.php');
					}
					
					if(isset($_SESSION['reservation_annuler_tranasction_lemon']))
					{
						echo LemonWay::displayErrorMessage($_SESSION['reservation_annuler_tranasction_lemon']);
						unset($_SESSION['reservation_annuler_tranasction_lemon']);
					}
					?>
					
					<div class="row text-center">
						<?php
						if(($membre->ID == $locataire->ID || $membre->ID == $proprio->ID) && $transaction->reponse != 0 && $transaction->annulation == 0)
						{
							if(new DateTime($transaction->date_debut_loc) > $today || (new DateTime($transaction->date_debut_loc) == $today && ($transaction->reponse != 1 || $transaction_code->date_validation == 0)))
							{
								echo '<button data-toggle="modal" data-target="#modal_annuler_reservation" class="btn btn-default"><span class="glyphicon glyphicon-info-sign"></span> Annuler la réservation</button>';
								
								if(new DateTime($transaction->date_debut_loc) == $today)
									echo ' | ';
							}
							
							if(new DateTime($transaction->date_debut_loc) <= $today)
								echo '<a href="litige.php?r=' . $transaction->ID . '" class="btn btn-primary"><span class="glyphicon glyphicon-info-sign"></span> Déclarer un litige</a>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" style="padding: 0;">
	<div class="row_content">
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<h2>Détails de la transaction</h2>
					
					<?php
					$activites = array();
					
					if($transaction->annulation == 1)
					{
						$activites[] = array('ID_membre' => $membre_annulation->ID,
											'titre' => array('Réservation annulée', 'rose'),
											'date' => $transaction_annulation->date_creation,
											'message' => array('<em>Réservation annulée par ' . $membre_annulation->sePresenter() . '.</em>',
																'<em>Vous avez annulé la réservation.</em>'));
					}
					
					if($transaction->reponse == 0)
					{
						$activites[] = array('ID_membre' => $proprio->ID,
											'titre' => array('Réservation refusée', 'rose'),
											'date' => $transaction->date_reponse,
											'message' => array('<em>Réservation refusée par ' . $proprio->sePresenter() . '.</em>',
																'<em>Vous avez refusé la réservation.</em>'));
					}
					else if($transaction->reponse == 1)
					{
						if($transaction->annulation == 0)
						{
							$message = '<form class="text-center" method="post">';
							
							if(isset($_POST['reservation_message']))
								$message .= '<div class="alert alert-danger text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Vous ne pouvez pas envoyer un message vide.</div>';
							
							$message .= '<textarea class="form-control" name="reservation_message"></textarea><button class="btn btn-custom" role="submit" name="reservation_submit_message">Envoyer le message <span class="glyphicon glyphicon-chevron-right"></span></button></form>';
							
							$activites[] = array('ID_membre' => $membre->ID,
												'titre' => array('Envoyer un message', 'bleu'),
												'date' => 0,
												'message' => array($message,
																	$message));
						}
						
						for($i=0; $i<count($transaction_messages); $i++)
						{
							if($transaction_code->date_validation != 0 && new DateTime($transaction_messages[$i]->date_creation) < new DateTime($transaction_code->date_validation))
								break;
							
							$message = nl2br(htmlspecialchars($transaction_messages[$i]->message));
							
							$activites[] = array('ID_membre' => $transaction_messages[$i]->ID_membre,
												'titre' => array('Message', 'bleu'),
												'date' => $transaction_messages[$i]->date_creation,
												'message' => array($message,
																	$message));
						}
						
						if($transaction_code->date_validation != 0)
						{
							$activites[] = array('ID_membre' => $proprio->ID,
												'titre' => array('Validation du code de réservation', 'rose'),
												'date' => $transaction->date_acceptation,
												'message' => array('<em>Code de réservation validé par ' . $proprio->sePresenter() . '.</em>',
																	'<em>Vous avez validé le code de réservation.</em>'));
						}
						
						for(; $i<count($transaction_messages); $i++)
						{
							$activites[] = array('ID_membre' => $transaction_messages[$i]->ID_membre,
												'titre' => array('Message', 'bleu'),
												'date' => $transaction_messages[$i]->date_creation,
												'message' => nl2br(htmlspecialchars($transaction_messages[$i]->message)));
						}
						
						$activites[] = array('ID_membre' => $membre_not_last_proposition->ID,
											'titre' => array('Acceptation de la réservation', 'rose'),
											'date' => $transaction->date_reponse,
											'message' => array('<em>Réservation acceptée par ' . $membre_not_last_proposition->sePresenter() . '.</em>',
																'<em>Vous avez accepté la réservation.</em>'));
					}
					
					foreach($transaction_dates as $key => $transaction_date)
					{
						if($key == count($transaction_dates) - 1)
						{
							$activites[] = array('ID_membre' => $locataire->ID,
												 'titre' => array('Réservation effectuée', 'rose'),
												 'date' => $transaction->date_transaction,
												 'message' => array('<em>Réservation effectuée par ' . $locataire->sePresenter() . ' pour la période du ' . (new DateTime($transaction_date->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction_date->date_fin_loc))->format('d/m/Y') . '.</em>',
																	'<em>Vous avez effectué la réservation pour la période du ' . (new DateTime($transaction_date->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction_date->date_fin_loc))->format('d/m/Y') . '.</em>'));
						}
						else
						{
							$membre_proposition = $membres_manager->getMembreByID($transaction_date->ID_membre);
							
							$activites[] = array('ID_membre' => $membre_proposition->ID,
												 'titre' => array('Modification des dates', 'rose'),
												 'date' => $transaction_date->date_proposition,
												 'message' => array('<em>Modification des dates par ' . $membre_proposition->sePresenter() . ' pour la période du ' . (new DateTime($transaction_date->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction_date->date_fin_loc))->format('d/m/Y') . '.</em>',
																	'<em>Vous avez modifié les dates pour la période du ' . (new DateTime($transaction_date->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction_date->date_fin_loc))->format('d/m/Y') . '.</em>'));
						}
					}
					
					foreach($activites as $key => $activite)
					{
					?>
						<hr />
						
						<div class="media">
							<?php
							$membre_activite = $membres_manager->getMembreByID($activite['ID_membre']);
							?>
							<div class="media-left">
								<a href="membre.php?id=<?php echo $membre_activite->ID; ?>">
									<?php
									if($membre_activite->avatar == '')
										echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 70px;"></span>';
									else
										echo '<img class="media-object" src="avatars/' . $membre_activite->avatar . '" style="max-width: 70px; max-height: 70px;">';
									?>
								</a>
							</div>
							
							<div class="media-body">
								<h4 class="media-heading">
									<strong<?php
									if($activite['titre'][1] == 'rose')
										echo ' class="rose_custom"';
									else if($activite['titre'][1] == 'bleu')
										echo ' class="blue_custom"';
									?>><?php echo $activite['titre'][0]; ?></strong>
									<?php
									if($activite['date'])
										echo '<br /><small>Le ' . (new DateTime($activite['date']))->format('d/m/Y à H:i') . '</small>';
									?>
								</h4>
								
								<?php echo ($activite['ID_membre'] != $membre->ID) ? $activite['message'][0] : $activite['message'][1]; ?>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>