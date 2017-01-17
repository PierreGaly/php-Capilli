<?php
$paiements_manager = new PaiementsMan($bdd);

$montant_tirelire = $membre ? $paiements_manager->getTotalPaiements($membre->ID) : 0;
$erreurs = $infos['erreurs'];
$erreurs2 = $infos['erreurs2'];
$objets_manager = new ObjetsMan($bdd);
$objets = array();
$prix = array();
$prix_total = 0;

foreach($_SESSION['panier'] as $key => $produit)
{
	$objets[] = $objets_manager->getByID($produit['ID_objet']);
	$prix[] = $_SESSION['panier'][$key]['prix_unitaire']*$produit['quantite'];
	$prix_total += $prix[$key];
}
?>
<div class="modal fade <?php if(empty($erreurs) && !empty($_SESSION['panier']) && (isset($_GET['t']) || isset($erreurs2['pas_assez_d_argent']))) echo 'show_modal'; ?>" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_remplir_tirelire">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Passer la commande : <strong><?php echo number_format($prix_total, 2, ',', ' '); ?> €</strong></h4>
			</div>
			
			<div class="modal-body">
				<div class="form-group">
					<?php
					if(isset($_SESSION['revenus_valide_paiement_reel']))
						echo '<div class="alert alert-info text-center" role="alert">Votre tirelire a été rechargée de <strong>' . number_format($_SESSION['revenus_valide_paiement_reel'], 2, ',', ' ') . ' €</strong>.<br /><strong>Vous pouvez maintenant passer la commande de ' . number_format($prix_total, 2, ',', ' ') . ' €.</strong></div>';
					?>
					
					<div class="row text-center">
						<p style="position: relative; width: 200px; margin: auto; padding-bottom: 15px;">
							<img src="sources/cochon_<?php if($montant_tirelire >= MONTANT_TIRELIRE_GROS) echo 'gros';  else if($montant_tirelire >= MONTANT_TIRELIRE_MOYEN) echo 'moyen'; else echo 'petit'; ?>.png" style="width: 200px;" alt="">
							<span style="position: absolute; top: 130px; left: 70px; font-size: 25px; font-weight: bold; color: white; text-shadow: 0 0 8px white; width: 120px;"><?php echo number_format($montant_tirelire, 2, ',', ' '); ?> €</span>
						</p>
					</div>
					
					<?php
					if($prix_total > $montant_tirelire)
					{
						$diff = $prix_total - $montant_tirelire;
						
						if($diff < ObjetsMan::PRIX_MIN)
							$recharge = ObjetsMan::PRIX_MIN;
						else if($diff > ObjetsMan::PRIX_MAX)
							$recharge = ObjetsMan::PRIX_MAX;
						else
							$recharge = $diff;
						?>
						<form role="form" method="post" action="paiement.php">
							<div class="alert alert-warning text-center" role="alert">
								Votre tirelire n'est pas assez remplie.
								<br /><br />
								<input type="hidden" name="paiement_return_path" value="panier.php?t" />
								<input type="hidden" name="paiement_montant" value="<?php echo number_format($recharge, 2, ',', ' '); ?>" />
								<button class="btn btn-custom" name="tr_recharger" role="submit">Recharger ma tirelire de <strong><?php echo number_format($recharge, 2, ',', ' '); ?> €</strong> <span class="glyphicon glyphicon-chevron-right"></span></button>
							</div>
						</form>
						<?php
					}
					else if(!isset($_SESSION['revenus_valide_paiement_reel']))
						echo '<div class="alert alert-info text-center" role="alert">Votre tirelire va être prélevée de <strong>' . number_format($prix_total, 2, ',', ' ') . ' €</strong>.</div>';
					
					if(isset($_SESSION['revenus_valide_paiement_reel']))
						unset($_SESSION['revenus_valide_paiement_reel']);
					?>
				</div>
			</div>
			
			<form role="form" method="post" action="">
				<div class="modal-footer">
					<button class="btn btn-custom" role="submit" name="panier_commande" <?php if($prix_total > $montant_tirelire) echo 'disabled'; ?>>Passer la commande <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
	<div class="row_content">
		<h2>Mon panier</h2>
		
		<div class="row">
			<div class="col-md-offset-3 col-md-6">
				<a class="video_link" data-video="TsO8cyBJwSo"></a>
			</div>
		</div>
		
		<br />
		
		<?php
		if(!empty($erreurs))
		{
			$erreurs_0 = array();
			$erreurs_1 = array();
			
			foreach($erreurs as $erreur)
			{
				if($erreur['type'] == 0)
					$erreurs_0[] = $erreur;
				else
					$erreurs_1[] = $erreur;
			}
			
			if(!empty($erreurs_0))
			{
				echo '<div class="alert alert-info text-center" role="alert">Les commandes ';
				
				foreach($erreurs_0 as $key => $erreur_0)
				{
					if($key)
						echo ', ';
					
					echo '#' . ($erreur_0['key'] + 1);
				}
				
				echo ' ne peuvent être passées car les produits demandés ne sont pas disponibles en quantités suffisantes.<br />Peut-être devriez-vous modifier les quantités ou vérifier que les dates de deux commandes différentes ne se chevauchent pas.</div>';
			}
			
			if(!empty($erreurs_1))
			{
				foreach($erreurs_1 as $key => $erreur_1)
				{
					$periodes = array();
					$dates = '';
					$date_debut = '';
					$date_fin = '';
					
					for($i = 0; $i < count($erreur_1['dates']); $i++)
					{
						$jour = date_create_from_format('d/m/Y', $erreur_1['dates'][$i]);
						
						for($j = $i + 1; $j < count($erreur_1['dates']); $j++)
						{
							if($jour->modify('+1 day') != date_create_from_format('d/m/Y', $erreur_1['dates'][$j]))
								break;
						}
						
						if($j == $i + 1)
							$periodes[] = 'le ' . $erreur_1['dates'][$i];
						else
						{
							$periodes[] = 'du ' . $erreur_1['dates'][$i] . ' au ' . $erreur_1['dates'][$j - 1];
							$i = $j - 1;
						}
					}
					
					echo '<div class="alert alert-info text-center" role="alert">La <strong>commande #' . ($erreur_1['key'] + 1) . '</strong> ne peut être passée car les produits demandés ne sont pas disponibles en quantités suffisantes pour les jours suivants :<strong><br />- ' . implode(',<br />- ', $periodes) . '.</strong><br /><br />Peut-être devriez-vous modifier les quantités, vérifier que les dates de deux commandes différentes ne se chevauchent pas ou déplacer la période de location.</div>';
				}
				
			}
		}
		?>
		
		<!--form method="post" action=""-->
		<?php
		if(!$membre)
		{
		?>
		<form method="post" action="connexion.php">
			<input type="hidden" name="redirect_connec" value="panier.php?t" />
		<?php
		}
		?>
			<table class="table table-striped table-hover table-bordered" style="background-color: white;">
				<thead>
					<tr>
						<td style="width: 50px; text-align: center;">#</td>
						<td>Produit</td>
						<td style="width: 220px; text-align: center;">Dates</td>
						<td style="width: 50px; text-align: center;">Q<span class="hidden-xs">uanti</span>té</td>
						<td style="width: 100px; text-align: center;" class="hidden-xs">Prix unitaire</td>
						<td style="width: 110px; text-align: center;">Prix total</td>
					</tr>
				</thead>
				<?php
				if(!empty($_SESSION['panier']))
				{
				?>
				<tfoot>
					<tr style="font-weight: bold;">
						<td colspan="100%" class="rose_custom">Total<span class="pull-right"><?php echo number_format($prix_total, 2, ',', ' '); ?> €</span></td>
					</tr>
					<tr>
						<td colspan="100%" class="text-center"><button class="btn btn-custom" <?php
						if(!empty($erreurs))
							echo ' disabled ';
						
						if($membre)
							echo ' data-toggle="modal" data-target="#modal_remplir_tirelire" ';
						else
							echo 'role="submit" ';
						?>>Passer la commande <span class="glyphicon glyphicon-chevron-right"></span></button></td>
					</tr>
				</tfoot>
				<?php
				}
				?>
				<tbody>
				<?php
				if(empty($_SESSION['panier']))
				{
					?>
						<tr>
							<td colspan="6" class="text-center"><em>Votre panier est vide.</em></td>
						</tr>
					<?php
				}
				else
				{
					$objets_manager = new ObjetsMan($bdd);
					
					foreach($_SESSION['panier'] as $key => $produit)
					{
						?>
						<tr>
							<td><a href="panier.php?r=<?php echo $key; ?>" onclick="if(!confirm('Êtes-vous certain de vouloir supprimer ce bien de votre panier ?')) return false;"><span class="glyphicon glyphicon-remove"></span></a><span class="hidden-xs"> <?php echo $key + 1; ?></span></td>
							<td><a href="annonce.php?id=<?php echo htmlspecialchars($produit['ID_objet']); ?>"><?php echo htmlspecialchars($objets[$key]->nom); ?></a></td>
							<td class="blue_custom">Du <?php echo $produit['date_debut']; ?> au <?php echo $produit['date_fin']; ?></td>
							<td class="blue_custom" style="text-align: right;"><?php echo $produit['quantite']; ?></td>
							<td class="blue_custom hidden-xs" style="text-align: right;"><?php echo number_format($_SESSION['panier'][$key]['prix_unitaire'], 2, ',', ' '); ?> €</td>
							<td class="rose_custom" style="text-align: right;"><?php echo number_format($prix[$key], 2, ',', ' '); ?> €</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		<?php
		if(!$membre)
		{
		?>
		</form>
		<?php
		}
		?>
	</div>
</div>