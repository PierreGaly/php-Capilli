<?php
$membres_manager = new MembresMan($bdd);
$litiges_manager = new LitigesMan($bdd);
$objets_manager = new ObjetsMan($bdd);
$communautes_manager = new CommunautesMan($bdd);
$paiements_manager = new PaiementsMan($bdd);
$versements_reel_manager = new Versements_reelsMan($bdd);
$comptes_bancaires_manager = new ComptesBancairesMan($bdd);

/*
require_once($_SESSION['dossier_vue'] . '/php/MailInscription.class.php');
new MailInscription($membres_manager->getMembreByID(1));
*/

$propositions = $communautes_manager->getPropositions();
$litiges = $litiges_manager->getLitiges();
$annonces = $objets_manager->getAnnoncesAccueilTable();
$annonces_clubs = $communautes_manager->getClubsAccueilTable();
$paiements_a_venir = $paiements_manager->getPaiementsAVenir();
$versements_en_attente = $versements_reel_manager->getVersementsEnAttente();

$nbr_membres_valides = $membres_manager->countMembres(1);
$nbr_membres_en_attente = $membres_manager->countMembres(0);
$nbr_annonces_visibles = $objets_manager->countAnnonces(1);
$nbr_annonces_non_visibles = $objets_manager->countAnnonces(0);
$nbr_versements_reels_en_attente = $versements_reel_manager->countVersementsEnAttente();

$nbr_propositions = $communautes_manager->countNotifsPropositions();
$nbr_litiges = count($litiges);
$nbr_annonces_error = 0;
$nbr_paiements_a_venir = count($paiements_a_venir);

$montant_tirelire = $paiements_manager->getTotalPaiements(-1);
$montant_tirelire2 = $paiements_manager->getTotalPaiements(-2);
$montant_tirelire3 = - $paiements_manager->getTotalPaiements(-3);

foreach($annonces as $key => $annonce)
{
	$objets_annonces[$key] = $objets_manager->getByID($annonce['ID_objet']);
	
	if($objets_annonces[$key]->actif != 1)
		$nbr_annonces_error++;
}

$erreurs = $infos['erreurs'];
?>
<div class="row">
	<div class="row_content">
		<h2>Administration du site</h2>
		
		<?php
		if(isset($_SESSION['administration_tirelire_retire']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button> Vous avez transféré <strong>' . number_format($_SESSION['administration_tirelire_retire'], 2, ',', ' ') . ' €</strong> de la tirelire ' . SITE_NOM . ' vers votre compte bancaire.</div>';
			unset($_SESSION['administration_tirelire_retire']);
		}
		
		if(isset($_SESSION['administration_tirelire_lemon']))
		{
			echo LemonWay::displayErrorMessage($_SESSION['administration_tirelire_lemon']);
			unset($_SESSION['administration_tirelire_lemon']);
		}
		
		if(isset($_SESSION['administration_tirelire_compte_bancaire']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le compte bancaire de l\'entreprise n\'existe pas.</div>';
			unset($_SESSION['administration_tirelire_compte_bancaire']);
		}
		?>
		
		<div class="row">
			<div class="col-md-4">
				<h3>Tirelire de <strong><?php echo SITE_NOM; ?></strong></h3>
				
				<div style="margin: auto;" class="text-center">
					<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
						<img src="sources/cochon_<?php if($montant_tirelire >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
						<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire, 2, ',', ' '); ?> €</span>
					</p>
				</div>
			</div>
			
			<div class="col-md-4">
				<h3>En attente de redistribution</h3>
				
				<div style="margin: auto;" class="text-center">
					<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
						<img src="sources/cochon_<?php if($montant_tirelire2 >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire2 >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
						<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire2, 2, ',', ' '); ?> €</span>
					</p>
				</div>
			</div>
			
			<div class="col-md-4">
				<h3>En circulation</h3>
				
				<div style="margin: auto;" class="text-center">
					<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
						<img src="sources/cochon_<?php if($montant_tirelire3 >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire3 >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
						<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire3, 2, ',', ' '); ?> €</span>
					</p>
				</div>
			</div>
		</div>
		
		<form method="post" action="">
			<p class="text-center">
				<button class="btn btn-custom" name="administration_vider_tirelire_principale">Vider la tirelire de <strong><?php echo SITE_NOM; ?></strong> <span class="glyphicon glyphicon-chevron-right"></span></button>
			</p>
		</form>
		
		<hr />
		
		<div class="row">
			<div class="alert alert-info text-center" role="alert">
				<span class="glyphicon glyphicon-user"></span> Membres : <strong><?php echo $nbr_membres_valides; ?> validé<?php if($nbr_membres_valides > 1) echo 's'; ?></strong> + <?php echo $nbr_membres_en_attente; ?> désinscrits.
				<br />
				<span class="glyphicon glyphicon-list-alt"></span> Annonces : <strong><?php echo $nbr_annonces_visibles; ?> visible<?php if($nbr_annonces_visibles > 1 ) echo 's'; ?></strong> + <?php echo $nbr_annonces_non_visibles; ?> non visible<?php if($nbr_annonces_non_visibles > 1) echo 's'; ?>.
			</div>
		</div>
		
		<?php
		$paiements_errors = $paiements_manager->getPaiements_errors();
		
		foreach($paiements_errors as $paiement_error)
			echo '<div class="alert alert-danger text-center" role="alert"><h3>Erreur de paiement !</h3></div>';
		?>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Gérer les versements bancaires<?php if($nbr_versements_reels_en_attente > 0) echo ' <span class="badge badge-custom">' . $nbr_versements_reels_en_attente . '</span>'; ?></h3>
		
		<?php
		if(isset($_SESSION['administration_versement_already_invalide']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_already_invalide'] . '</strong> est déjà invalidé.</div>';
			unset($_SESSION['administration_versement_already_invalide']);
		}
		
		if(isset($_SESSION['administration_versement_invalide_not_exists']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_invalide_not_exists'] . '</strong> n\'existe pas.</div>';
			unset($_SESSION['administration_versement_invalide_not_exists']);
		}
		
		if(isset($_SESSION['administration_versement_valide_lemon']))
		{
			echo LemonWay::displayErrorMessage($_SESSION['administration_versement_valide_lemon']);
			unset($_SESSION['administration_versement_valide_lemon']);
		}
		
		if(isset($_SESSION['administration_versement_invalide']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_invalide'] . '</strong> a été invalidé.</div>';
			unset($_SESSION['administration_versement_invalide']);
		}
		?>
		
		<!--div class="row" style="margin-bottom: 20px;">
			<form class="form-inline text-center" method="post" action="" role="form">
				<div class="input-group">
					<span class="input-group-addon">#</span>
					<input type="number" class="form-control" name="administration_invalider_versement_ID" min="1" required autofocus>
					<span class="input-group-btn">
						<button class="btn btn-custom" role="submit" name="administration_invalider_versement" onclick="if(!confirm('Êtes-vous certain de vouloir invalider le versement ?')) return false;">Invalider un versement <span class="glyphicon glyphicon-chevron-right"></span></button>
					</span>
				</div>
			</form>
		</div-->
		
		<?php
		if(isset($_SESSION['administration_compte_bancaire_not_exists']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le compte bancaire du versement <strong>#' . $_SESSION['administration_compte_bancaire_not_exists'] . '</strong> n\'existe pas.</div>';
			unset($_SESSION['administration_compte_bancaire_not_exists']);
		}
		
		if(isset($_SESSION['administration_compte_bancaire_same']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le compte bancaire du versement <strong>#' . $_SESSION['administration_compte_bancaire_same'] . '</strong> n\'a pas changé.</div>';
			unset($_SESSION['administration_compte_bancaire_same']);
		}
		
		if(isset($_SESSION['administration_compte_bancaire_updated']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Le compte bancaire du versement <strong>#' . $_SESSION['administration_compte_bancaire_updated'] . '</strong> a été mis à jour.</div>';
			unset($_SESSION['administration_compte_bancaire_updated']);
		}
		
		if(isset($_SESSION['administration_versement_already_valide']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_already_valide'] . '</strong> est déjà validé.</div>';
			unset($_SESSION['administration_versement_already_valide']);
		}
		
		if(isset($_SESSION['administration_versement_valide_not_exists']))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_valide_not_exists'] . '</strong> n\'existe pas.</div>';
			unset($_SESSION['administration_versement_valide_not_exists']);
		}
		
		if(isset($_SESSION['administration_versement_valide']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Le versement <strong>#' . $_SESSION['administration_versement_valide'] . '</strong> a été validé.</div>';
			unset($_SESSION['administration_versement_valide']);
		}
		
		if(empty($versements_en_attente))
			echo '<div class="alert alert-info text-center" role="alert">Aucun versement en attente.</div>';
		else
		{
			echo '<script type="text/javascript" src="zeroclipboard-2.2.0/dist/ZeroClipboard.min.js"></script>';
			
			foreach($versements_en_attente as $versement_en_attente)
			{
				$paiement = $paiements_manager->getPaiementByID($versement_en_attente->ID_paiement);
				$membre_paiement = $membres_manager->getMembreByID($paiement->ID_membre_from);
				$compte_bancaire_membre = $comptes_bancaires_manager->getByID($versement_en_attente->ID_compte_bancaire);
				$montant_versement_reel = number_format($paiement->montant, 2, ',', ' ');
				$titulaire_versement_reel = htmlspecialchars($compte_bancaire_membre->titulaire);
				$iban_versement_reel = htmlspecialchars($comptes_bancaires_manager->getHumanReadableIBAN($compte_bancaire_membre->iban));
				$bic_versement_reel = htmlspecialchars($comptes_bancaires_manager->getHumanReadableBIC($compte_bancaire_membre->bic));
				?>
				<form method="post" class="form-inline" action="">
					<div class="panel panel-default">
						<div class="panel-heading text-center">
							<?php echo $membre_paiement->sePresenter(); ?><br />Le <?php echo (new DateTime($versement_en_attente->date_creation))->format('d/m/Y à H:m:s'); ?>
						</div>
						
						<div class="list-group">
							<a href="" id="versement_reel_<?php echo $versement_en_attente->ID; ?>_1" onclick="return false;" class="list-group-item copy_link" data-clipboard-text="#<?php echo $versement_en_attente->ID; ?>">
								<strong># :</strong>
								<?php echo $versement_en_attente->ID; ?>
							</a>
							<a href="" id="versement_reel_<?php echo $versement_en_attente->ID; ?>_2" onclick="return false;" class="list-group-item copy_link" data-clipboard-id="123" data-clipboard-text="<?php echo $montant_versement_reel; ?>">
								<strong>Somme :</strong>
								<?php echo $montant_versement_reel; ?> €
							</a>
							<a href="" id="versement_reel_<?php echo $versement_en_attente->ID; ?>_3" onclick="return false;" class="list-group-item copy_link" data-clipboard-text="<?php echo $titulaire_versement_reel; ?>">
								<strong>Titulaire :</strong>
								<?php echo $titulaire_versement_reel; ?>
							</a>
							<a href="" id="versement_reel_<?php echo $versement_en_attente->ID; ?>_4" onclick="return false;" class="list-group-item copy_link" data-clipboard-text="<?php echo $iban_versement_reel; ?>">
								<strong>IBAN :</strong>
								<?php echo $iban_versement_reel; ?>
							</a>
							<a href="" id="versement_reel_<?php echo $versement_en_attente->ID; ?>_5" onclick="return false;" class="list-group-item copy_link" data-clipboard-text="<?php echo $bic_versement_reel; ?>">
								<strong>BIC :</strong>
								<?php echo $bic_versement_reel; ?>
							</a>
						</div>
						
						<div class="panel-footer text-center">
							<input type="hidden" name="administration_versement_reel_ID" value="<?php echo $versement_en_attente->ID; ?>" />
							<button class="btn btn-default" role="submit" name="administration_versement_reel_actualiser" onclick="if(!confirm('Êtes-vous certain de vouloir actualiser les informations bancaires ?')) return false;">Actualiser les informations bancaires <span class="glyphicon glyphicon-chevron-right"></span></button>
							<br />
							<button class="btn btn-primary" role="submit" name="administration_versement_reel_valider" onclick="if(!confirm('Êtes-vous certain de vouloir valider les informations bancaires ?')) return false;">Valider le versement <span class="glyphicon glyphicon-chevron-right"></span></button>
						</div>
					</div>
				</form>
				<?php
			}
		}
		?>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Gérer les propositions de clubs<?php if($nbr_propositions > 0) echo ' <span class="badge badge-custom">' . $nbr_propositions . '</span>'; ?></h3>
		
		<?php
		if(isset($_SESSION['notif_communaute_suspendue']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><span class="glyphicon glyphicon-ok-sign"></span> La proposition <em>' . htmlspecialchars($_SESSION['notif_communaute_suspendue']) . '</em> a été masquée.</div>';
			unset($_SESSION['notif_communaute_suspendue']);
		}
		
		if(isset($_SESSION['notif_communaute_remise']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><span class="glyphicon glyphicon-ok-sign"></span> La proposition <em>' . htmlspecialchars($_SESSION['notif_communaute_remise']) . '</em> a été démasquée.</div>';
			unset($_SESSION['notif_communaute_remise']);
		}
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<thead>
					<tr>
						<th></th>
						<th class="text-center">Par</th>
						<th class="text-center">Nom</th>
						<th class="text-center">Date</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="4" class="text-center"><a href="club.php" class="btn btn-custom">Proposer un nouveau club <span class="glyphicon glyphicon-chevron-right"></span></a></th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				if(empty($propositions))
				{
				?>
					<tr>
						<td colspan="4" class="text-center"><em>Aucune proposition non traitée.</em></td>
					</tr>
				<?php
					
				}
				else
				{
					foreach($propositions as $proposition)
					{
					?>
						<tr>
							<td class="text-center"><a class="btn btn-default" href="administration.php?<?php echo $proposition->vu ? 'remettre' : 'suspendre'; ?>_notif_communaute=<?php echo $proposition->ID; ?>"><span class="glyphicon glyphicon-eye-<?php echo $proposition->vu ? 'open' : 'close'; ?>"></span></a></td>
							<td><?php echo $membres_manager->getMembreByID($proposition->ID_membre)->sePresenter(); ?></td>
							<td class="col-sm-6 col-md-8"><a href="creer_club.php?p=<?php echo $proposition->ID; ?>"><?php echo htmlspecialchars($proposition->nom); ?></a></td>
							<td class="text-center"><?php echo (new DateTime($proposition->date_proposition))->format('d/m/Y'); ?></td>
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

<div class="row">
	<div class="row_content">
		<h3>Gérer les litiges<?php if($nbr_litiges > 0) echo ' <span class="badge badge-custom">' . $nbr_litiges . '</span>'; ?></h3>
		
		<?php
		if(isset($_SESSION['administration_litiges_traites']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Nombre de litiges traités : ' . $_SESSION['administration_litiges_traites'] . '.</div>';
			unset($_SESSION['administration_litiges_traites']);
		}
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<thead>
					<tr>
						<th class="text-center"></th>
						<th class="text-center">Par</th>
						<th class="text-center">Réservation</th>
						<th class="text-center">Message</th>
						<th class="text-center">Date</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6" class="text-center"><button class="btn btn-custom" role="submit" name="submit_litiges">Traiter les litiges cochés <span class="glyphicon glyphicon-chevron-right"></button></td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				if(empty($litiges))
				{
				?>
					<tr>
						<td colspan="5" class="text-center"><em>Aucun litige non traité.</em></td>
					</tr>
				<?php
					
				}
				else
				{
					foreach($litiges as $litige)
					{
					?>
						<tr>
							<td class="text-center"><input type="checkbox" name="litige_traiter_<?php echo $litige->ID; ?>"></td>
							<td><?php echo $membres_manager->getMembreByID($litige->ID_membre)->sePresenter(); ?></td>
							<td class="text-center"><a href="reservation.php?id=<?php echo $litige->ID_transaction; ?>" onclick = "window.open(this.href); return false;">#<?php echo $litige->ID_transaction; ?></a></td>
							<td><?php echo nl2br(htmlspecialchars($litige->message)); ?></td>
							<td class="text-center"><?php echo (new DateTime($litige->date_creation))->format('d/m/Y'); ?></td>
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

<div class="row">
	<div class="row_content">
		<h3>Paiements automatiques à venir<?php if($nbr_paiements_a_venir > 0) echo ' (' . $nbr_paiements_a_venir . ')'; ?><br /><small>Exécution automatique tous les jours à 20H</small></h3>
		
		<?php
		if(isset($_SESSION['administration_paiements_annules']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Nombre de paiements annulés : ' . $_SESSION['administration_paiements_annules'] . '.</div>';
			unset($_SESSION['administration_paiements_annules']);
		}
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<thead>
					<tr>
						<th class="text-center"></th>
						<th class="text-center">Date prévue</th>
						<th class="text-center">État</th>
						<th class="text-center">Émetteur</th>
						<th class="text-center">Bénéficiaire</th>
						<th class="text-center">Réservation</th>
						<th class="text-center">Montant</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="7" class="text-center"><button class="btn btn-custom" role="submit" name="submit_paiements" onclick="if(!confirm('Êtes-vous certain de vouloir annuler ces paiements ?')) return false;">Annuler les paiements cochés <span class="glyphicon glyphicon-chevron-right"></button></td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				if(empty($paiements_a_venir))
				{
				?>
					<tr>
						<td colspan="7" class="text-center"><em>Aucun paiement à venir.</em></td>
					</tr>
				<?php
					
				}
				else
				{
					foreach($paiements_a_venir as $paiement_a_venir)
					{
					?>
						<tr>
							<td class="text-center"><input type="checkbox" <?php if($paiement_a_venir->paiement == 0) echo 'disabled'; ?> class="form-control" name="paiement_annuler_<?php echo $paiement_a_venir->ID_transaction; ?>"></td>
							<td class="text-center"><?php echo (new DateTime($paiement_a_venir->date_creation))->format('d/m/Y'); ?></td>
							<td class="text-center"><?php echo ($paiement_a_venir->paiement == 2) ? 'en attente' : '<span class="rose_custom">annulé</span>'; ?></td>
							<td><?php echo $membres_manager->getMembreByID($paiement_a_venir->ID_membre_from)->sePresenter(); ?></td>
							<td><?php echo $membres_manager->getMembreByID($paiement_a_venir->ID_membre_for)->sePresenter(); ?></td>
							<td class="text-center"><a href="reservation.php?id=<?php echo $paiement_a_venir->ID_transaction; ?>" onclick = "window.open(this.href); return false;">#<?php echo $paiement_a_venir->ID_transaction; ?></a></td>
							<td class="text-right rose_custom"><?php echo number_format($paiement_a_venir->montant, 2, ',', ' '); ?> €</td>
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

<div class="row">
	<div class="row_content">
		<h3>Gérer les clubs de l'accueil<?php if($nbr_annonces_error > 0) echo ' <span class="badge badge-custom">' . $nbr_annonces_error . '</span>'; ?><br /><small>Nombre max. de clubs affichés : <?php echo NOMBRE_CLUBS_ACCUEIL; ?>.</small></h3>
		
		<?php
		if(isset($_SESSION['administration_club_modified']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> L\'ordre du club a bien été modifié.</div>';
			unset($_SESSION['administration_club_modified']);
		}
		
		if(isset($_SESSION['administration_club_added']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Le club a été ajouté à la page d\'accueil.</div>';
			unset($_SESSION['administration_club_added']);
		}
		
		if(isset($_SESSION['administration_club_removed']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Le club a été supprimé de la page d\'accueil.</div>';
			unset($_SESSION['administration_club_removed']);
		}
		
		if(isset($_POST['administration_club_add']))
			echo '<div class="alert alert-danger text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-remove-sign"></span> Le club n\'a pas été ajouté car il n\'existe pas.</div>';
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<tfoot>
					<tr>
						<th colspan="2" class="text-center">
							<div class="input-group">
								<input class="form-control" type="text" name="administration_club_add" id="administration_club_add">
								<span class="input-group-btn">
									<button class="btn btn-custom" role="submit">Ajouter le club à l'accueil <span class="glyphicon glyphicon-chevron-right"></button>
								</span>
							</div>
						</th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				foreach($annonces_clubs as $key => $annonce_club)
				{
					$club = $communautes_manager->getCommunauteByID($annonce_club['ID_club']);
					?>
					<tr <?php if($key < NOMBRE_CLUBS_ACCUEIL) echo 'class="info"'; ?> onmouseover="this.children[1].children[1].style.display='inline'; this.children[1].children[2].style.display='inline';" onmouseout="this.children[1].children[1].style.display='none'; this.children[1].children[2].style.display='none';">
						<td class="text-center" style="width: 40px;"><?php echo (int) $annonce_club['ordre']; ?></td>
						<td>
							<a href="club.php?id=<?php echo $club->ID; ?>"><?php echo htmlspecialchars($club->nom); ?></a>
							<a style="display: none; float: right;" href="administration.php?ca=<?php echo $annonce_club['ID']; ?>" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer le club <?php echo htmlspecialchars($club->nom); ?> de l\'accueil ?')) return false;"><span class="glyphicon glyphicon-remove"></span></a>
							<a style="display: none; float: right;" href="" onclick="var name = prompt('Nouvel ordre du club :', '<?php echo $annonce_club['ordre']; ?>'); if(name != null &amp;&amp; name != '') window.location.href='administration.php?o=' + encodeURIComponent(name) + '&amp;c=<?php echo $annonce_club['ID']; ?>'; return false;"><span class="glyphicon glyphicon-pencil"></span></a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</form>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Gérer les annonces de l'accueil<?php if($nbr_annonces_error > 0) echo ' <span class="badge badge-custom">' . $nbr_annonces_error . '</span>'; ?><br /><small>Nombre max. d'annonces affichées : <?php echo NOMBRE_ANNONCES_ACCUEIL; ?>.</small></h3>
		
		<?php
		if(isset($_SESSION['administration_annonce_modified']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> L\'ordre de l\'annonce a bien été modifié.</div>';
			unset($_SESSION['administration_annonce_modified']);
		}
		
		if(isset($_SESSION['administration_annonce_added']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> L\'annonce a été ajoutée à la page d\'accueil.</div>';
			unset($_SESSION['administration_annonce_added']);
		}
		
		if(isset($_SESSION['administration_annonce_removed']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> L\'annonce a été supprimée de la page d\'accueil.</div>';
			unset($_SESSION['administration_annonce_removed']);
		}
		
		if(isset($_POST['administration_annonce_add']))
			echo '<div class="alert alert-danger text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-remove-sign"></span> L\'annonce n\'a pas été ajoutée car elle appartient à un club, est inactive, a été supprimée ou n\'existe pas.</div>';
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<tfoot>
					<tr>
						<th colspan="2" class="text-center">
							<div class="input-group">
								<input class="form-control" type="text" name="administration_annonce_add" id="administration_annonce_add">
								<span class="input-group-btn">
									<button class="btn btn-custom" role="submit">Ajouter l'annonce à l'accueil <span class="glyphicon glyphicon-chevron-right"></button>
								</span>
							</div>
						</th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$compteur = 0;
				
				foreach($annonces as $key => $annonce)
				{
					$objet = $objets_annonces[$key];
					?>
					<tr <?php if($objet->actif != 1) echo 'class="danger"'; else if($compteur++ < NOMBRE_ANNONCES_ACCUEIL) echo 'class="info"'; ?> onmouseover="this.children[1].children[1].style.display='inline'; this.children[1].children[2].style.display='inline';" onmouseout="this.children[1].children[1].style.display='none'; this.children[1].children[2].style.display='none';">
						<td class="text-center" style="width: 40px;"><?php echo (int) $annonce['ordre']; ?></td>
						<td>
							<a href="annonce.php?id=<?php echo $objet->ID; ?>"><?php echo htmlspecialchars($objet->nom); ?></a>
							<a style="display: none; float: right;" href="administration.php?a=<?php echo $annonce['ID']; ?>" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer l\'annonce <?php echo htmlspecialchars($objet->nom); ?> de l\'accueil ?')) return false;"><span class="glyphicon glyphicon-remove"></span></a>
							<a style="display: none; float: right;" href="" onclick="var name = prompt('Nouvel ordre de l\'annonce :', '<?php echo $annonce['ordre']; ?>'); if(name != null &amp;&amp; name != '') window.location.href='administration.php?o=' + encodeURIComponent(name) + '&amp;e=<?php echo $annonce['ID']; ?>'; return false;"><span class="glyphicon glyphicon-pencil"></span></a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</form>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Gérer les catégories</h3>
		
		<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_categorie">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<form role="form" method="post" action="" enctype="multipart/form-data" class="form-horizontal">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Modifier une catégorie</h4>
						</div>
						
						<div class="modal-body">
							<label for="modifier_cat_name">Nom de la catégorie</label>
							<input type="text" class="form-control" name="modifier_cat_name" id="modifier_cat_name" value="" required>
							<label for="modifier_cat_ordre">Ordre de la catégorie</label>
							<input type="number" class="form-control" name="modifier_cat_ordre" id="modifier_cat_ordre" value="" required>
							<input type="hidden" class="form-control" name="modifier_cat_id" id="modifier_cat_id" id="">
						</div>
						
						<div class="modal-footer" style="text-align: center;">
							<button class="form-control btn btn-custom" name="submit_modifier_cat" role="submit">Modifier la catégorie <span class="glyphicon glyphicon-chevron-right" style="margin-right: 5px;"></span></button>
						</div>
					</form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_sous_categorie">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<form role="form" method="post" action="" enctype="multipart/form-data" class="form-horizontal">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Modifier une sous-catégorie</h4>
						</div>
						
						<div class="modal-body">
							<div class="col-xs-12">
								<div style="position: relative; width: 100%; padding-bottom: 100%; margin-bottom:20px;">
									<a href="" class="thumbnail" style="position:absolute; width:100%; height:100%;" onclick="return false;">
										<img style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto; max-width: 100%; max-height: 100%;" src="" alt="" id="modifier_sous_cat_image">
									</a>
								</div>
							</div>
							<label for="modifier_sous_cat_name">Nom de la sous-catégorie</label>
							<input type="text" class="form-control" name="modifier_sous_cat_name" id="modifier_sous_cat_name" value="" required>
							<label for="modifier_sous_cat_ordre">Ordre de la catégorie</label>
							<input type="number" class="form-control" name="modifier_sous_cat_ordre" id="modifier_sous_cat_ordre" value="" required>
							<input type="hidden" class="form-control" name="modifier_sous_cat_id" id="modifier_sous_cat_id" id="">
							<label class="control-label" for="photos">Modifier la photo <small>(facultatif)</small></label>
							<input type="file" class="form-control" id="modifier_sous_cat_photo" name="modifier_sous_cat_photo" accept="image/*">
						</div>
						
						<div class="modal-footer" style="text-align: center;">
							<button class="form-control btn btn-custom" name="submit_modifier_sous_cat" role="submit">Modifier la sous-catégorie <span class="glyphicon glyphicon-chevron-right" style="margin-right: 5px;"></span></button>
						</div>
					</form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<?php
		if(isset($_SESSION['administration_cat_modified']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La catégorie <em>' . htmlspecialchars($_SESSION['administration_cat_modified']) . '</em> a été modifiée.</div>';
			unset($_SESSION['administration_cat_modified']);
		}
		
		if(isset($_SESSION['administration_sous_cat_modified']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La catégorie <em>' . htmlspecialchars($_SESSION['administration_sous_cat_modified']) . '</em> a été modifiée.</div>';
			unset($_SESSION['administration_sous_cat_modified']);
		}
		
		$categories_manager = new CategoriesMan($bdd);
		$sous_categories_manager = new SousCategoriesMan($bdd);
		
		$categories = $categories_manager->getCategories();
		
		if(!empty($erreurs))
		{
			echo '<div class="alert alert-danger text-center" role="alert">Erreur lors de l\'envoi de l\'image : ';
			
			foreach($erreurs['photos'] as $photo)
			{
				if($photo[1] == 'erreur_envoi')
					echo '<strong>erreur lors de l\'envoi</strong>';
				else if($photo[1] == 'extension')
					echo '<strong>extension invalide</strong>';
				else if($photo[1] == 'taille')
					echo '<strong>le fichier est trop volumineux (max : ' . SousCategoriesMan::TAILLE_MAX_PHOTO/1024/1024 . ' Mo)</strong>';
				else if($photo[1] == 'empty')
					echo '<strong>aucun fichier envoyé</strong>';
			}
			
			echo '.</div>';
		}
		
		foreach($categories as $categorie)
		{
		?>
			<div class="panel panel-default" style="margin-bottom: 0; border-radius: 0;">
				<div class="panel-heading categorie_modifier" data-id="<?php echo $categorie->ID; ?>" style="font-size: 1.3em;">
					<strong><?php echo $categorie->ordre; ?></strong> - <a href="" id="name_cat_<?php echo $categorie->ID; ?>"><?php echo htmlspecialchars($categorie->nom); ?></a>
					<input type="hidden" id="ordre_cat_<?php echo $categorie->ID; ?>" value="<?php echo $categorie->ordre; ?>">
					<a href="" class="pull-right bouton_slide_toggle" data-id="<?php echo $categorie->ID; ?>"><span class="glyphicon glyphicon-plus"></span></a>
				</div>
				
				<ul class="list-group" id="categorie_list_<?php echo $categorie->ID; ?>" style="display: none;">
					<?php
					$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($categorie->ID);
					
					foreach($sous_categories as $sous_categorie)
						echo '<input type="hidden" id="image_sous_cat_' . $sous_categorie->ID . '" value="' . IMAGES_SOUS_CAT . $sous_categorie->image . '"><a class="list-group-item sous_categorie_modifier" data-id="' . $sous_categorie->ID . '" href=""><strong id="ordre_sous_cat_' . $sous_categorie->ID . '">' . $sous_categorie->ordre . '</strong> - <span id="name_sous_cat_' . $sous_categorie->ID . '">' . htmlspecialchars($sous_categorie->nom) . '</span></a>';
					?>
				</ul>
			</div>
		<?php
		}
		
		if(isset($_SESSION['administration_cat_created']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La catégorie <em>' . htmlspecialchars($_SESSION['administration_cat_created']) . '</em> a été créée.</div>';
			unset($_SESSION['administration_cat_created']);
		}
		
		if(isset($_SESSION['administration_sous_cat_created']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La catégorie <em>' . htmlspecialchars($_SESSION['administration_sous_cat_created']) . '</em> a été créée.</div>';
			unset($_SESSION['administration_sous_cat_created']);
		}
		?>
		
		<form method="post" action="" style="margin-top: 20px;">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<caption class="text-center">Créer une catégorie</caption>
				<tr>
					<td><input type="text" class="form-control" name="new_cat_name" required placeholder="Nom de la catégorie"></td>
					<td><input type="number" class="form-control" name="new_cat_ordre" required placeholder="Ordre de la catégorie"></td>
					<td><button class="btn btn-custom" role="submit" name="submit_new_cat" style="width: 100%">Créer la catégorie <span class="glyphicon glyphicon-chevron-right"></span></button></td>
				</tr>
			</table>
		</form>
		
		<form method="post" action="" enctype="multipart/form-data" style="margin-top: 20px;">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<caption class="text-center">Créer une sous-catégorie</caption>
				<tr>
					<td><input type="text" class="form-control" name="new_sous_cat_id" required placeholder="ID de la catégorie"></td>
					<td><input type="text" class="form-control" name="new_sous_cat_name" required placeholder="Nom de la sous catégorie"></td>
					<td><input type="number" class="form-control" name="new_sous_cat_ordre" required placeholder="Ordre de la sous catégorie"></td>
					<td><input type="file" class="form-control" name="new_sous_cat_photo" required accept="image/*"></td>
					<td><button class="btn btn-custom" role="submit" name="submit_new_sous_cat" style="width: 100%">Créer la sous-catégorie <span class="glyphicon glyphicon-chevron-right"></span></button></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<div class="row">
	<div class="row_content">
		<h3>Gérer les sources</h3>
		
		<?php
		if(isset($_SESSION['administration_source_added']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La source <em>' . htmlspecialchars($_SESSION['administration_source_added']) . '</em> a bien été ajoutée.</div>';
			unset($_SESSION['administration_source_added']);
		}
		
		if(isset($_SESSION['administration_source_removed']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> La source a bien été supprimée.</div>';
			unset($_SESSION['administration_source_removed']);
		}
		?>
		
		<form method="post" class="form-inline" action="">
			<table class="table table-striped table-bordered table-hover table-condensed" style="background-color: white;">
				<tfoot>
					<tr>
						<th class="text-center"><label class="control-label" for="administration_source_add"><input class="form-control" type="text" name="administration_source_add" id="administration_source_add"> <button class="btn btn-custom" role="submit">Créer la source <span class="glyphicon glyphicon-chevron-right"></button></th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$sources = $membres_manager->getSources();
				
				foreach($sources as $source)
				{
				?>
					<tr onmouseover="this.children[1].children[0].style.display='inline'; this.children[1].children[1].style.display='inline';" onmouseout="this.children[1].children[0].style.display='none'; this.children[1].children[1].style.display='none';">
						<td style="width: 45px; display: none;"></td>
						<td>
							<?php echo htmlspecialchars($source->source); ?>
							<a style="display: none; float: right;" href="administration.php?d=<?php echo $source->ID; ?>" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer la source <?php echo htmlspecialchars($source->source); ?> ?')) return false;"><span class="glyphicon glyphicon-remove"></span></a>
							<a style="display: none; float: right;" href="" onclick="var name = prompt('Nouveau nom de la source :', '<?php echo htmlspecialchars($source->source); ?>'); if(name != null &amp;&amp; name != '') window.location.href='administration.php?n=' + encodeURIComponent(name) + '&amp;c=<?php echo $source->ID; ?>'; return false;"><span class="glyphicon glyphicon-pencil"></span></a>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</form>
	</div>
</div>