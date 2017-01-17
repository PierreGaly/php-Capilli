<div class="row" style="position: relative;">
	<div class="row row_index" style="background-image: url('sources/fond_index.jpg'); z-index: 1; filter: blur(8px); -webkit-filter: blur(8px); z-index: 5; position: absolute; height: 100%; width: 100%;">
	</div>
	
	<div class="row_content" style="position: relative; padding-bottom: 20px; z-index: 10;">
		<h2>Paiement sécurisé</h2>
		
		<?php
		$montant = $infos['montant'];
		$erreurs = $infos['erreurs'];
		
		if(!empty($erreurs))
		{
			if(!empty($erreurs['carte']))
				echo LemonWay::displayErrorMessage($erreurs['carte']);
			else if(!empty($erreurs['paiement']))
				echo LemonWay::displayErrorMessage($erreurs['paiement']);
			else if(!empty($erreurs['carte_numero']))
				echo '<div class="alert alert-danger text-center" role="alert"><strong><span class="glyphicon glyphicon-info-sign"></span> Le numéro de carte est incorrect.</div>';
			else if(!empty($erreurs['carte_date']))
				echo '<div class="alert alert-danger text-center" role="alert"><strong><span class="glyphicon glyphicon-info-sign"></span> La date d\'expiration est incorrecte.</div>';
			else if(!empty($erreurs['carte_crypto']))
				echo '<div class="alert alert-danger text-center" role="alert"><strong><span class="glyphicon glyphicon-info-sign"></span> Le cryptogramme de sécurité est incorrect.</div>';
		}
		?>
		
		<div class="row">
			<form class="form-horizontal col-md-offset-4 col-md-4" method="post" action="">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 style="margin: 0;"><?php echo number_format($montant, 2, ',', ' '); ?> €<br /><small><?php echo SITE_NOM; ?></small></h3>
					</div>
					
					<div class="panel-body" style="padding: 20px;">
						<fieldset>
							<div class="form-group">
								<div class="col-xs-12" style="padding: 0 5px 0 5px;">
									<label style="margin-bottom: 10px;" class="text-center center-block">Votre carte</label>
									<div class="input-group">
										<span class="input-group-addon"><span class="glyphicon glyphicon-credit-card"></span></span>
										<input required id="paiement_cb" type="text" class="form-control input-lg" name="paiement_cb" autofocus maxlength="22" placeholder="Numéro de carte bancaire">
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-xs-6" style="padding: 0 5px 0 5px;">
									<div class="input-group col-xs-12">
										<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
										<input required id="paiement_crypto" type="text" class="form-control input-lg" name="paiement_crypto" maxlength="3" placeholder="CVV">
									</div>
								</div>
								
								<div class="col-xs-6" style="padding: 0 5px 0 5px;">
									<div class="input-group col-xs-12">
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										<input required id="paiement_date" type="text" class="form-control input-lg" name="paiement_date" maxlength="7" placeholder="MM / AA">
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					
					<div class="panel-footer clearfix">
						<input type="hidden" name="paiements_return_path" value="<?php if(!empty($_POST['paiements_return_path'])) echo $_POST['paiements_return_path']; ?>">
						<input type="hidden" name="paiement_montant" value="<?php if(!empty($_POST['paiement_montant'])) echo $_POST['paiement_montant']; ?>">
						<button type="submit" class="col-xs-12 btn btn-custom btn-lg">Payer <?php echo number_format($montant, 2, ',', ' '); ?> €</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>