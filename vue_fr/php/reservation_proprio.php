<?php
if(new DateTime($transaction->date_fin_loc) < $today)
{
?>
	<div class="alert alert-info text-center" role="alert">
		Location terminée.
	</div>
<?php
}
else
{
?>
	<div class="row" style="font-size: 1.2em; margin-top: 40px; margin-bottom: 20px; text-align: center;">
		<p class="text-center">
		Vous pouvez désormais <strong>prendre contact</strong> avec <em><a href="membre.php?id=<?php echo $locataire->ID; ?>"><?php echo htmlspecialchars($locataire->prenom . ' ' . $locataire->nom); ?></a></em> :
		</p>
		
		<ul style="display: inline-block; text-align: left;">
			<li>par messages via la messagerie ci-dessous,</li>
			<li>par mail à l'adresse <strong><a href="mailto:<?php echo $locataire->email; ?>"><?php echo $locataire->email; ?></a></strong>,</li>
			<li>par téléphone/SMS au <strong>+<?php echo substr($locataire->tel_portable, 2, 2) . '.' . substr($locataire->tel_portable, 4); ?></strong><?php if($locataire->tel_fixe != '') echo 'ou au <strong>+' . substr($locataire->tel_fixe, 2, 2) . '.' . substr($locataire->tel_fixe, 4) . '</strong>'; ?>.</li>
		</ul>
	</div>
	
	<div class="alert alert-info text-center" role="alert">
		Veuillez également <strong>télécharger et imprimer le contrat de location</strong> avant de rencontrer le locataire.
		<br /><br />
		<a class="btn btn-custom" href="contrat_de_location.pdf" onclick="window.open(this.href); return false;"><span class="glyphicon glyphicon-download-alt"></span> Télécharger le contrat de location (pdf)</a>
	</div>
<?php
}

if($etapes_transactions[3] == 2)
{
?>
	<form method="post" action="">
		<div class="panel panel-default text-center">
			<div class="panel-heading">
				<h4 class="media-heading">
					Code de réservation
					<br />
					<small>Veuillez demander le code de réservation du locataire.</small>
				</h4>
			</div>
			
			<div class="panel-body form-inline">
				<div class="row">
					<?php
					if(isset($_POST['reservation_code']))
						echo '<div class="alert alert-danger text-center" role="alert">Le code de réservation que vous avez entré est incorrect.</div>';
					?>
					
					<div class="input-group input-group-lg">
						<input type="text" class="form-control" placeholder="Code de réservation" name="reservation_code" autofocus>
						<span class="input-group-btn">
							<button class="btn btn-custom" role="submit">Valider <span class="glyphicon glyphicon-chevron-right"></span></button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</form>
<?php
}
?>