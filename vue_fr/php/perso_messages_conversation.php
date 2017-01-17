<?php
$membres_manager = new MembresMan($bdd);
?>
<div class="modal fade modal_new_participant" role="dialog" aria-labelledby="gridSystemModalLabel" id="new_message">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form role="form" method="post" action="" id="form_new_message">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">Ajouter des destinataires</h4>
				</div>
				
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" id="ID_conversation" value="<?php echo $conversation->ID; ?>">
						<label class="control-label" for="new_message_participants">Destinataire(s)</label>
						<select data-placeholder="Destinataires" id="participants" class="chosen-select form-control" multiple tabindex="4" name="new_message_participants[]">
							<option value=""></option>
						</select>
					</div>
				</div>
				
				<div class="modal-footer">
					<button class="btn btn-custom" role="submit">Ajouter <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row_content">
	<h2><?php echo htmlspecialchars($conversation->objet); ?></h2>
	
	<p class="text-center">
		<?php
		$participants = $messagerie_manager->getParticipants($conversation->ID);
		
		foreach($participants as $key => $participant)
		{
			if($key)
				echo ' ';
			
			echo '<span class="label label-default">' . htmlspecialchars($participant->prenom) . ' ' . htmlspecialchars($participant->nom) . '</span>';
		}
		?>
		<span class="label label-custom"><a href="" style="color: white;" data-toggle="modal" data-target=".modal_new_participant">Ajouter <span class="glyphicon glyphicon-plus"></span></a></span>
	</p>
	
	<div class="row" style="background-color: white; border: 1px solid rgb(200, 200, 200); border-radius: 20px; overflow: hidden; margin: 0 20px 20px 20px;">
		<?php
		foreach($messages as $message)
		{
		?>
			<div class="media" style="padding: 15px;">
				<?php
				if($message->ID_membre != $membre->ID)
				{
					$membre_message = $membres_manager->getMembreByID($message->ID_membre);
					?>
					<div class="media-left">
						<a href="#">
							<?php
							if($membre_message->avatar == '')
								echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
							else
								echo '<img class="media-object" src="avatars/' . $membre_message->avatar . '" style="max-width: 100px; max-height: 100px;">';
							?>
						</a>
					</div>
					<?php
				}
				?>
				
				<div class="media-body">
					<h4 class="media-heading">Le <?php echo (new DateTime($message->date_creation))->format('d/m/Y à H:i'); ?></h4>
					
					<?php echo nl2br(htmlspecialchars($message->message)); ?>
				</div>
				
				<?php
				if($message->ID_membre == $membre->ID)
				{
					?>
					<div class="media-right">
						<a href="#">
							<?php
							if($membre->avatar == '')
								echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
							else
								echo '<img class="media-object" src="avatars/' . $membre->avatar . '" style="max-width: 100px; max-height: 100px;">';
							?>
						</a>
					</div>
					<?php
				}
				?>
			</div>
			
			<hr />
		<?php
		}
		?>
		
		<div class="media" style="padding: 15px; background-color: rgb(240, 240, 240);">
			<div class="media-body">
				<h4 class="media-heading">Votre réponse</h4>
				
				<div class="alert alert-danger text-center" role="alert" style="display: none;" id="alert_message">
					Il est interdit d’échanger les numéros de téléphone via notre système de messagerie. Si vous voulez le numéro de téléphone de la personne, vous devez effectuer une réservation et il faut que celle ci soit acceptée par le propriétaire.
				</div>
				
				<form method="post" action="" class="text-center">
					<textarea name="message" id="message" class="form-control" placeholder="Entrez votre réponse" autofocus></textarea>
					<button class="btn btn-custom" role="submit" id="messagerie_envoyer" disabled>Envoyer <span class="glyphicon glyphicon-chevron-right"></span></button>
				</form>
			</div>
			
			<div class="media-right hidden-xs">
				<a href="#">
					<?php
					if($membre->avatar == '')
						echo '<span class="media-object glyphicon glyphicon-user" style="font-size: 100px;"></span>';
					else
						echo '<img class="media-object" src="avatars/' . $membre->avatar . '" style="max-width: 100px; max-height: 100px;">';
					?>
				</a>
			</div>
		</div>
	</div>
</div>