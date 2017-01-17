<?php
if(new DateTime($transaction->date_fin_loc) < $today)
{
?>
	<div class="alert alert-info text-center" role="alert">
		Location terminée.
		<br /><br />
		<?php
		$commentaire = $transactions_manager->getCommentaireByTransaction($transaction->ID, $membre->ID);
		
		if(empty($commentaire))
		{
		?>
			<form method="post" action="">
				<div class="row">
					<div class="col-sm-offset-3 col-sm-3" style="font-size: 1.2em;">
						<label class="control-label">Noter le propriétaire</label>
						<br />
						<?php
						$note = $membres_manager->getNote($transaction->ID, $membre->ID);
						
						if($note === false)
						{
						?>
							<a style="font-size: 1.2em;" href="annonce.php?id=<?php echo $_GET['id']; ?>" data-id="<?php echo $_GET['id']; ?>">
							<?php
							for($i=0; $i<5; $i++)
								echo '<span class="glyphicon glyphicon-star-empty star_note_proprio" data-number="' . ($i+1) . '"></span>';
							?>
							</a>
						<?php
						}
						else
						{
							for($i=0; $i<5; $i++)
								echo ($i + 1 <= round($note)) ? '<span class="glyphicon glyphicon-star"></span>' : '<span class="glyphicon glyphicon-star-empty"></span>';
						}
						?>
					</div>
					
					<div class="col-sm-3" style="font-size: 1.2em;">
						<label class="control-label">Noter le bien</label>
						<br />
						<?php
						$note = $objets_manager->getNote($transaction->ID, $membre->ID);
						
						if($note === false)
						{
						?>
							<a href="annonce.php?id=<?php echo $_GET['id']; ?>" data-id="<?php echo $_GET['id']; ?>">
							<?php
							for($i=0; $i<5; $i++)
								echo '<span class="glyphicon glyphicon-star-empty star_note_bien" data-number="' . ($i+1) . '"></span>';
							?>
							</a>
						<?php
						}
						else
						{
							for($i=0; $i<5; $i++)
								echo ($i + 1 <= round($note)) ? '<span class="glyphicon glyphicon-star"></span>' : '<span class="glyphicon glyphicon-star-empty"></span>';
						}
						?>
					</div>
				</div>
				
				<div class="col-sm-12">
					<textarea class="form-control" name="reservation_commentaire" placeholder="Laisser un commentaire sur la transaction et/ou le propriétaire" required></textarea>
					<small class="help-block">Votre commentaire sera susceptible d'être vu par d'autres utilisateurs.</small>
				</div>
				
				<button class="btn btn-custom" type="submit">Envoyer le commentaire <span class="glyphicon glyphicon-chevron-right"></span></button>
			</form>
		<?php
		}
		else
			echo '<table style="margin: auto; border-spacing: 15px; border-collapse: separate;" class="text-left"><tr><td style="vertical-align: top;"><strong>Votre commentaire </strong></td><td><em>' . nl2br(htmlspecialchars($commentaire->commentaire)) . '</em></td></tr></table>';
		?>
	</div>
<?php
}
else
{
?>
	<div class="row" style="font-size: 1.2em; margin-top: 40px; margin-bottom: 20px; text-align: center;">
		<p class="text-center">
		Vous pouvez désormais <strong>prendre contact</strong> avec <em><a href="membre.php?id=<?php echo $proprio->ID; ?>"><?php echo htmlspecialchars($proprio->prenom . ' ' . $proprio->nom); ?></a></em> :
		</p>
		
		<ul style="display: inline-block; text-align: left;">
			<li>par messages via la messagerie ci-dessous,</li>
			<li>par mail à l'adresse <strong><a href="mailto:<?php echo $proprio->email; ?>"><?php echo $proprio->email; ?></a></strong>,</li>
			<li>par téléphone/SMS au <strong>+<?php echo substr($proprio->tel_portable, 2, 2) . '.' . substr($proprio->tel_portable, 4); ?></strong><?php if($locataire->tel_fixe != '') echo 'ou au <strong>+' . substr($locataire->tel_fixe, 2, 2) . '.' . substr($locataire->tel_fixe, 4) . '</strong>'; ?>.</li>
		</ul>
	</div>
	
	<div class="alert alert-info text-center" role="alert">
		Veuillez également <strong>télécharger et imprimer le contrat de location</strong> avant de rencontrer le propriétaire.
		<br /><br />
		<?php
		if($transaction->cheque_caution)
			echo 'Veuillez également préparer le <strong>chèque de caution</strong> d\'un montant de <strong>' . number_format($objet->caution, 2, ',', ' ') . ' €</strong> demandé par le propriétaire.<br /><br />';
		?>
		<a class="btn btn-custom" href="contrat_de_location.pdf" onclick="window.open(this.href); return false;"><span class="glyphicon glyphicon-download-alt"></span> Télécharger le contrat de location (pdf)</a>
	</div>
<?php
}

if($etapes_transactions[2])
{
?>
	<div class="panel panel-default text-center">
		<div class="panel-body form-inline">
			<div class="row">
				<p style="font-size: 2em;" class="text-center">
					Code de réservation : <span class="rose_custom"><?php echo $transaction_code->code_reservation; ?></span>
				</p>
				
				<p>
					Veuillez donner ce code de réservation au propriétaire lorsque vous aurez reçu le<?php if($objet->quantite > 1) echo 's'; ?> bien<?php if($objet->quantite > 1) echo 's'; ?> réservé<?php if($objet->quantite > 1) echo 's'; ?>.
				</p>
			</div>
		</div>
	</div>
<?php
}
?>