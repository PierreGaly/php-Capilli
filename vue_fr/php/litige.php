<?php
$transactions_manager = new TransactionsMan($bdd);
$objets_manager = new ObjetsMan($bdd);
$transactions = $transactions_manager->getTransactionsEnCours($membre->ID);
?>
<div class="row">
	<div class="row_content">
		<h2>Déclarer un litige</h2>
		
		<?php
		if(isset($_SESSION['litige_declared']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Nous avons bien pris en compte votre demande, nous vous contacterons dans les plus brefs délais.</div>';
			unset($_SESSION['litige_declared']);
		}
		else
		{
			if(isset($_POST['li_description']))
				echo '<div class="alert alert-danger text-center" role="alert">Veuillez compléter la description du litige.</div>';
			?>
		<div class="row">
			<form class="form-horizontal" method="post" action="">
				<div class="col-md-offset-2 col-md-8 formulaire">
					<div class="form-group">
						<label class="col-xs-12 col-md-4 control-label" for="li_reservation">Réservation concernée</label>
						<div class="col-xs-12 col-md-8">
							<?php
							if(empty($transactions))
								echo '<em>Aucune réservation en cours.</em>';
							else
							{
							?>
							<select class="form-control" id="li_reservation" name="li_reservation">
								<?php
								foreach($transactions as $transaction)
								{
									$objet = $objets_manager->getByID($transaction->ID_objet);
									
									echo '<option value="' . $transaction->ID . '"';
									
									if((isset($_POST['li_reservation']) && $_POST['li_reservation'] == $transaction->ID) || (!isset($_POST['li_reservation']) && isset($_GET['r']) && $_GET['r'] == $transaction->ID))
										echo ' selected';
									
									echo '>' . $transaction->quantite . ' x ' . htmlspecialchars($objet->nom) . ' du ' . $transaction->date_debut_loc . ' au ' . $transaction->date_fin_loc . '</option>';
								}
								?>
							</select>
							<?php
							}
							?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-md-4 control-label" for="li_description">Description du litige</label>
						<div class="col-xs-12 col-md-8">
							<textarea class="form-control" id="li_description" name="li_description"></textarea>
						</div>
					</div>
					
					<hr style="margin: 5px 0 15px 0;" />
					
					<div class="form-group">
						<div class="col-xs-12 col-md-12">
							<button type="submit" id="submit_button" class="btn btn-custom center-block">Déclarer le litige <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="row_content">
			
			<h2>Pour toute autre question</h2>
			
			<?php Page::getFormulaireContact(); ?>
		<?php
		}
		?>
	</div>
</div>