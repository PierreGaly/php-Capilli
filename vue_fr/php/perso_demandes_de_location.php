<?php
$transactions_manager = new TransactionsMan($this->bdd);
$objets_manager = new ObjetsMan($this->bdd);
$transactions = $transactions_manager->getByLocataire($membre->ID);
$communautes_manager = new CommunautesMan($bdd);
$membres_manager = new MembresMan($bdd);
?>
<div class="row_content">
	<div class="row" style="text-align: center; padding: 2px;">
		<?php
		if(isset($_SESSION['commande_success']))
		{
			$commandes_result = array();
			
			foreach($_SESSION['commande_success'] as $key => $commande)
			{
				if(is_array($commande['result']))
				{
					$objet = $objets_manager->getByID($commande['objet']['ID_objet']);
					echo LemonWay::displayErrorMessage($commande['result'], 'Commande #' . ($key+1) . ' : <strong>' . $commande['objet']['quantite'] . ' x</strong> <a href="annonce.php?id=' . $objet->ID . '">' . htmlspecialchars($objet->nom) . '</a> <em>du ' . $commande['objet']['date_debut'] . ' au ' .  $commande['objet']['date_fin'] . '</em>');
				}
				else
					$commandes_result[] = $commande['objet'];
			}
			
			if(!empty($commandes_result))
			{
				echo '<div class="alert alert-info text-center" role="alert">Les commandes suivantes ont été passées :<br /><ul>';
				
				foreach($commandes_result as $commande_result)
				{
					$objet = $objets_manager->getByID($commande_result['ID_objet']);
					echo '<li><strong>' . $commande_result['quantite'] . ' x</strong> <a href="annonce.php?id=' . $objet->ID . '">' . htmlspecialchars($objet->nom) . '</a> <em>du ' . $commande_result['date_debut'] . ' au ' .  $commande_result['date_fin'] . '</em></li>';
				}
				
				echo '</ul></div>';
			}
			
			unset($_SESSION['commande_success']);
		}
		?>
		<h2>Mes demandes de location</h2>
		
		<p class="text-center">Comment se passe une transaction sur </strong><?php echo SITE_NOM; ?> ?</p>
		
		<div class="row">
			<div class="col-xs-12 col-sm-offset-3 col-sm-6">
				<a class="video_link" data-video="TsO8cyBJwSo"></a>
			</div>
		</div>
		
		<br />
		
		<?php
		$transactions_no_notifs = array();
		$today = new DateTime((new DateTime('now'))->format('Y-m-d'));
		
		foreach($transactions as $transaction)
		{
			$nbr_notifs_transaction = $transactions_manager->countNotifsTransactionsAsLocataireByID_transation($transaction->ID);
			
			if($nbr_notifs_transaction > 0)
			{
				$objet = $objets_manager->getByID($transaction->ID_objet, 2);
				$membre_annonce = $membres_manager->getMembreByID($objet->ID_proprio);
			
				$calcul_date = (new DateTime($transaction->date_debut_loc))->diff($today, false);
				$nbr_jours = ($calcul_date->invert) ? $calcul_date->d : -($calcul_date->d);
				?>
				<div class="col-xs-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
					<h3><?php
					if($nbr_jours < -2)
						echo 'Il y a ' . -$nbr_jours . ' jours';
					else if($nbr_jours == -2)
						echo 'Avant-hier';
					else if($nbr_jours == -1)
						echo 'Hier';
					else if($nbr_jours == 0)
						echo 'Aujourd\'hui';
					else if($nbr_jours == 1)
						echo 'Demain';
					else if($nbr_jours == 2)
						echo 'Après-demain';
					else
						echo 'Dans ' . $nbr_jours . ' jours';
					?></h3>
					
					<div class="thumbnail clignoter" style="padding: 0; overflow: hidden; border-radius: 12px 12px 0 0; background-color: rgb(250, 250, 250); border-bottom: 3px solid rgb(200, 200, 200);">
						<div style="position: relative; width: 100%; padding-bottom: 80%;">
							<a href="reservation.php?id=<?php echo $transaction->ID; ?>" class="thumbnail" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) inset; border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover;"></a>
						</div>
					
						<div class="caption">
							<h4 style="margin: 10px 0 10px 0; text-align: left;"><?php echo htmlspecialchars($objet->nom); ?> <span class="badge badge-custom"><?php echo $nbr_notifs_transaction; ?></span></h4>
							
							<p style="text-align: left;">
								<?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?>
								<a href="membre.php?id=<?php echo $membre_annonce->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre_annonce->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : '<img class="img-circle" alt="' . $membre_annonce->prenom . ' ' . $membre_annonce->nom . '" style="width: 40px; height: 40px;" src="avatars/' . $membre_annonce->avatar . '">'; ?></span></a>
							</p>
						</div>
					</div>
				</div>
				<?php
			}
			else
				$transactions_no_notifs[] = $transaction;
		}
		
		foreach($transactions_no_notifs as $transaction)
		{
			$objet = $objets_manager->getByID($transaction->ID_objet, 2);
			
			$calcul_date = (new DateTime($transaction->date_debut_loc))->diff($today, false);
			$nbr_jours = ($calcul_date->invert) ? $calcul_date->d : -($calcul_date->d);
			$membre_annonce = $membres_manager->getMembreByID($objet->ID_proprio);
			?>
			<div class="col-xs-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
				<h3><?php
				if($nbr_jours < -2)
					echo 'Il y a ' . -$nbr_jours . ' jours';
				else if($nbr_jours == -2)
					echo 'Avant-hier';
				else if($nbr_jours == -1)
					echo 'Hier';
				else if($nbr_jours == 0)
					echo 'Aujourd\'hui';
				else if($nbr_jours == 1)
					echo 'Demain';
				else if($nbr_jours == 2)
					echo 'Après-demain';
				else
					echo 'Dans ' . $nbr_jours . ' jours';
				?></h3>
				
				<div class="thumbnail" style="padding: 0; overflow: hidden; border-radius: 12px 12px 0 0; background-color: rgb(250, 250, 250); border-bottom: 3px solid rgb(200, 200, 200);">
					<div style="position: relative; width: 100%; padding-bottom: 80%;">
						<a href="reservation.php?id=<?php echo $transaction->ID; ?>" class="thumbnail" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) inset; border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover;"></a>
					</div>
				
					<div class="caption">
						<h4 style="margin: 10px 0 10px 0; text-align: left;"><?php echo htmlspecialchars($objet->nom); ?></h4>
						
						<p style="text-align: left;">
							<?php echo ($objet->ID_club == -1) ? '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span> Tout le monde</span>' : '<span class="label label-custom"><span class="glyphicon glyphicon-flag"></span> ' . htmlspecialchars($communautes_manager->getCommunauteByID($objet->ID_club)->nom) . '</span>'; ?>
							<a href="membre.php?id=<?php echo $membre_annonce->ID; ?>" class="pull-right" style="position: relative; top: -10px;"><span><?php echo ($membre_annonce->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 40px;"></span>' : ('<img class="img-circle" alt="' . $membre_annonce->prenom . ' ' . $membre_annonce->nom . '" style="width: 40px; height: 40px;" src="avatars/' . $membre_annonce->avatar . '">'); ?></span></a>
						</p>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>