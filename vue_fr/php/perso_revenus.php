<?php
$membres_manager = new MembresMan($bdd);
$paiements_manager = new PaiementsMan($bdd);
$objets_manager = new ObjetsMan($bdd);
$transactions_manager = new TransactionsMan($bdd);
$parrainages_manager = new ParrainagesMan($bdd);

$montant_tirelire = $paiements_manager->getTotalPaiements($membre->ID);
$paiements_a_venir = $paiements_manager->getPaiementsAVenirFor($membre->ID);
$paiements_passes = $paiements_manager->getPaiementsByMembre($membre->ID);
$total = 0;
?>
<div class="row">
	<div class="row_content">
		<h2>Détail de mes revenus</h2>
		
		<?php
		if(isset($_SESSION['revenus_valide_paiement_reel']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Votre tirelire a été rechargée de <strong>' . number_format($_SESSION['revenus_valide_paiement_reel'], 2, ',', ' ') . ' €</strong>.</div>';
			unset($_SESSION['revenus_valide_paiement_reel']);
		}
		
		/*if(isset($_SESSION['revenus_erreur_paiement_reel']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">';
			
			switch($_SESSION['revenus_erreur_paiement_reel'])
			{
				case 'processing_error':
					echo 'Un problème est survenu lors du de traitement de la transaction bancaire.';
					break;
				
				case 'card_declined':
					echo 'La carte bancaire a été rejetée.';
					break;
				
				case 'insufficient_funds':
					echo 'Vous n\'avez pas assez de fonds pour procéder au paiement.';
					break;
				
				case '3ds_declined':
					echo 'Le code 3-D secure a été rejeté.';
					break;
				
				case 'incorrect_number':
					echo 'Le numéro de la carte bancaire est incorrect.';
					break;
				
				case 'fraud_suspected':
					echo 'Une tentative de fraude a été détectée.';
					break;
				
				case 'aborted':
					echo 'Le paiement a été annulé.';
					break;
				
				default:
					echo 'Un problème est survenu lors du rechargement de votre tirelire.';
			}
			
			echo '<br /><strong>Si vous n\'êtes pas à l\'origine de cette erreur, veuillez <a href="mailto:' . SITE_EMAIL . '">prendre contact avec nous</a>.</strong></div>';
			
			unset($_SESSION['revenus_erreur_paiement_reel']);
		}
		
		if(!empty($infos['erreurs']['prix_invalide']))
			echo '<div class="alert alert-danger text-center" role="alert">La somme que vous avez entrée est incorrecte.<br /><strong>Elle doit être comprise entre ' . number_format(ObjetsMan::PRIX_MIN, 2, ',', ' ') . ' € et ' . number_format(ObjetsMan::PRIX_MAX, 2, ',', ' ') . ' €.</strong></div>';
		
		if(!empty($infos['erreurs']['paiement_impossible']))
			echo '<div class="alert alert-danger text-center" role="alert">Le système de paiement est indisponible.<br />Si cette erreur survient à nouveau, veuillez <a href="mailto:' . SITE_EMAIL . '">prendre contact avec nous</a>.</div>';
		
		if(!empty($infos['erreurs']['paiement_annule']))
				echo '<div class="alert alert-danger text-center" role="alert">Un problème est survenu lors de votre paiement.<br /><strong>Votre compte bancaire n\'a pas été débité.</strong><br />Si cette erreur survient à nouveau, veuillez <a href="mailto:' . SITE_EMAIL . '">prendre contact avec nous</a>.</div>';
		*/?>
		
		<div class="row text-center">
			<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
				<img src="sources/cochon_<?php if($montant_tirelire >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
				<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire, 2, ',', ' '); ?> €</span>
			</p>
		</div>
		
		<div class="row">
			<form class="form-inline text-center" method="post" action="paiement.php" role="form">
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-eur"></span></span>
					<input type="text" class="form-control input_prix" name="paiement_montant" placeholder="1,00" required autofocus>
					<span class="input-group-btn">
						<button class="btn btn-custom" role="submit">Recharger ma tirelire <span class="glyphicon glyphicon-chevron-right"></span></button>
					</span>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Transactions passées</h3>
		
		<?php
		if(isset($_POST['revenus_vider_tirelire']))
			echo '<div class="alert alert-danger text-center" role="alert">Vous ne pouvez pas retirer votre argent car votre solde actuel est nul.</div>';
		
		if(isset($_SESSION['paiement_montant_retire']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button> Vous avez transféré <strong>' . number_format($_SESSION['paiement_montant_retire'], 2, ',', ' ') . ' €</strong> de votre tirelire ' . SITE_NOM . ' vers votre compte bancaire.</div>';
			unset($_SESSION['paiement_montant_retire']);
		}
		
		if(isset($_SESSION['paiement_montant_lemon']))
		{
			echo LemonWay::displayErrorMessage($_SESSION['paiement_montant_lemon']);
			unset($_SESSION['paiement_montant_lemon']);
		}
		?>
		
		<form method="post" action="" class="form-inline">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<thead>
					<tr>
						<th class="text-center hidden-xs">Date</th>
						<th class="text-center">Réservation</th>
						<th class="text-center hidden-xs">Type</th>
						<th class="text-center">Montant</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="text-left hidden-xs" colspan="3">Solde actuel</th>
						<th class="text-left visible-xs" colspan="1">Solde actuel</th>
						<th class="text-right rose_custom"><?php echo number_format($montant_tirelire, 2, ',', ' '); ?> €</th>
					</tr>
					<tr>
						<th class="text-center" colspan="4">
							<button class="btn btn-custom" role="submit" name="revenus_vider_tirelire"<?php if($montant_tirelire <= 0) echo ' disabled'; ?> onclick="if(!confirm('Êtes-vous certain de vouloir verser le montant de votre tirelire <?php echo SITE_NOM; ?> vers votre compte bancaire ?')) return false;">Vider ma tirelire <span class="glyphicon glyphicon-chevron-right"></span></button>
							<span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Cette action transfèrera votre solde actuel vers votre compte bancaire."></span>
						</th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				if(empty($paiements_passes))
				{
				?>
					<tr>
						<td colspan="4" class="text-center"><em>Vous n'avez aucun revenu passé.</em></td>
					</tr>
				<?php
				}
				else
				{
					foreach($paiements_passes as $paiement)
					{
						$transaction = ($paiement->ID_transaction < 0) ? false : $transactions_manager->getTransactionByID($paiement->ID_transaction);
						$ID_proprio = ($paiement->ID_transaction < 0) ? false : $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
						$isProprio = $paiement->ID_transaction >= 0 && $ID_proprio == $membre->ID;
						$isLocataire = $paiement->ID_transaction >= 0 && $transaction->ID_locataire == $membre->ID;
						?>
						<tr>
							<td class="text-center hidden-xs"><?php echo (new DateTime($paiement->date_creation))->format('d/m/Y'); ?></td>
							<td class="text-center"><?php
							if($paiement->ID_transaction < 0)
								echo '-';
							else
							{
								if($isProprio || $isLocataire)
									echo '<a href="reservation.php?id=' . $paiement->ID_transaction . '">';
								
								echo '#' . $paiement->ID_transaction;
								
								if($isProprio)
									echo '</a>';
							}
							?></td>
							<td class="text-center hidden-xs" style="font-style: italic"><?php
								if($paiement->ID_membre_from == $membre->ID && $paiement->ID_membre_for == -3)
									echo 'Versement vers votre compte bancaire';
								else if($paiement->ID_membre_from == -3 && $paiement->ID_membre_for == $membre->ID)
									echo 'Versement vers votre tirelire';
								else if($paiement->ID_membre_from == $membre->ID && $paiement->ID_membre_for == -2)
									echo 'Location en tant que locataire';
								else if($paiement->ID_membre_from == -2 && $isProprio)
								{
									echo 'Location en tant que propriétaire';
									$total += $paiement->montant;
								}
								else if($paiement->ID_membre_from == -2 && $isLocataire)
									echo 'Remboursement après annulation';
								else if($paiement->ID_membre_from == -2)
								{
									echo 'Commission par parrainage';
									$total += $paiement->montant;
								}
								?></td>
							<td class="text-right rose_custom"><?php echo number_format(($paiement->ID_membre_from == $membre->ID) ? -$paiement->montant : $paiement->montant, 2, ',', ' '); ?> €</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</form>
		
		<p class="text-center" style="font-style: italic; font-size: 1.2em;">
			Au total, vous avez gagné <strong class="rose_custom"><?php echo number_format($total, 2, ',', ' '); ?> €</strong> avec notre plateforme.
		</p>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Revenus à venir</h3>
		
		<form method="post" action="" class="form-inline">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<caption class="text-center">Seuls sont affichés les revenus dont vous êtes le propriétaire des locations,<br />sous réserve que la réservation se passe comme prévu.</caption>
				<thead>
					<tr>
						<th class="text-center hidden-xs">Date</th>
						<th class="text-center">Réservation</th>
						<th class="text-center hidden-xs">De</th>
						<th class="text-center hidden-xs">Type</th>
						<th class="text-center">Montant</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if(empty($paiements_a_venir))
				{
				?>
					<tr>
						<td colspan="5" class="text-center"><em>Vous n'avez aucun revenu à venir.</em></td>
					</tr>
				<?php
				}
				else
				{
					foreach($paiements_a_venir as $paiement)
					{
						$ID_proprio = ($paiement->ID_transaction < 0) ? false : $objets_manager->getByID($transactions_manager->getTransactionByID($paiement->ID_transaction)->ID_objet, 2)->ID_proprio;
						
						$isProprio = $paiement->ID_transaction >= 0 && $ID_proprio == $membre->ID;
						?>
						<tr>
							<td class="text-center hidden-xs"><?php echo ($paiement->paiement == 2) ? (new DateTime($paiement->date_creation))->format('d/m/Y') : '<em class="rose_custom">annulé</em>'; ?></td>
							<td class="text-center"><?php
							if($paiement->ID_transaction < 0)
								echo '-';
							else
							{
								if($isProprio)
									echo '<a href="reservation.php?id=' . $paiement->ID_transaction . '">';
								
								echo '#' . $paiement->ID_transaction;
								
								if($isProprio)
									echo '</a>';
							}
							?></td>
							<td class="text-center hidden-xs"><?php echo $membres_manager->getMembreByID($paiement->ID_membre_from)->sePresenter(); ?></td>
							<td class="text-center hidden-xs" style="font-style: italic"><?php
								if($paiement->ID_transaction < 0)
									echo 'Retrait';
								else if($isProprio)
									echo 'Location';
								else
									echo 'Commission par parrainage';
									?></td>
							<td class="text-right rose_custom"><?php echo number_format($paiement->montant, 2, ',', ' '); ?> €</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</form>
	</div>
</div>