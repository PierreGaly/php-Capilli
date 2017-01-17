<div class="modal fade modal_new_message" role="dialog" aria-labelledby="gridSystemModalLabel" id="new_message">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form role="form" method="post" action="" id="form_new_message">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">Nouvelle conversation</h4>
				</div>
				
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label" for="new_message_destinataires">Destinataire(s)</label>
						<select data-placeholder="Destinataires" id="destinataires" class="chosen-select form-control" multiple tabindex="4" name="new_message_destinataires[]">
							<?php
							$membres_manager = new MembresMan($bdd);
							$i = 1;
							
							while(!empty($_GET['d' . $i]))
							{
								$membre_tmp = $membres_manager->getMembreByID($_GET['d' . $i]);
								
								if(empty($membre_tmp))
									break;
								
								echo '<option value="' . $membre_tmp->prenom . ' ' . $membre_tmp->nom . '" selected>' . $membre_tmp->prenom . ' ' . $membre_tmp->nom . '</option>';
								$i++;
							}
							?>
						</select>
					</div>
					
					<div class="form-group">
						<label class="control-label" for="new_message_objet">Objet</label>
						<input name="new_message_objet" id="new_message_objet" type="text" class="form-control" placeholder="Objet" required>
					</div>
					
					<div class="form-group">
						<label class="control-label" for="new_message_message">Message</label>
						<div class="alert alert-danger text-center" role="alert" style="display: none;" id="alert_message">
							Il est interdit d’échanger les numéros de téléphone via notre système de messagerie. Si vous voulez le numéro de téléphone de la personne, vous devez effectuer une réservation et il faut que celle ci soit acceptée par le propriétaire.
						</div>
						<textarea class="form-control" placeholder="Entrez votre message" id="new_message_message" name="new_message_message" required></textarea>
					</div>
				</div>
				
				<div class="modal-footer">
					<?php
					$i = 1;
					
					while(!empty($_GET['d' . $i]))
					{
						if(isset($_GET['d' . $i]))
							echo '<input type="hidden" id="destinataires_default_' . $i . '" value="' . $_GET['d' . $i] . '" />';
						
						$i++;
					}
					?>
					<button class="btn btn-custom" role="submit" id="new_message_envoyer" disabled>Envoyer <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$messagerie_manager = new MessagerieMan($this->bdd);
$conversations = $messagerie_manager->getConversationsByMembre($membre->ID);

if(isset($_SESSION['perso_message_del_participant']))
{
	echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button> Vous avez quitté la conversation.</div>';
	unset($_SESSION['perso_message_del_participant']);
}

if(empty($conversations))
{
?>
<p class="text-center blue_custom">
	<span class="glyphicon glyphicon-envelope" style="font-size: 100px;"></span>
	<br />
	Pas de nouveaux messages
	<br />
	<br />
	<button class="btn btn-custom center-block" data-toggle="modal" data-target=".modal_new_message"><span class="glyphicon glyphicon-plus"></span> Nouvelle conversation</button>
</p>
<?php
}
else
{
?>
	<div class="row_content">
		<h2>Mes conversations</h2>
		
					<form method="post" action="?messages">
		<div class="list-group">
			<?php
			foreach($conversations as $conversation)
			{
			?>
				<a href="perso.php?messages&amp;c=<?php echo $conversation->ID; ?>" class="list-group-item" onmouseover="this.children[0].style.display='inline';" onmouseout="this.children[0].style.display='none';">
					<button style="display: none;" onclick="if(!confirm('Êtes-vous certain de vouloir quitter cette conversation ?')) return false;" class="btn btn-default pull-right" name="del_participant" value="<?php echo $conversation->ID; ?>" role="subtmit"><span class="glyphicon glyphicon-remove"></span></button>
					
					<h4 class="list-group-item-heading"><?php
					if($messagerie_manager->hasNouveauMessage($conversation->ID, $membre->ID))
						echo '<span class="glyphicon glyphicon-envelope rose_custom"></span><strong> ';
					
					echo htmlspecialchars($conversation->objet);
					
					if($messagerie_manager->hasNouveauMessage($conversation->ID, $membre->ID))
						echo '</strong>';
					?></h4>
					
					<p class="list-group-item-text blue_custom">
						<?php
						$participants = $messagerie_manager->getParticipants($conversation->ID);
						
						foreach($participants as $key => $participant)
						{
							if($key)
								echo ', ';
							
							echo htmlspecialchars($participant->prenom) . ' ' . htmlspecialchars($participant->nom);
						}
						?>
					</p>
				</a>
			<?php
			}
			?>
					</form>
			
			<a class="list-group-item disabled" href="" style="cursor: default;" onclick="return false;">
				<button class="btn btn-custom center-block" data-toggle="modal" data-target=".modal_new_message"><span class="glyphicon glyphicon-plus"></span> Nouvelle conversation</button>
			</a>
		</div>
	</div>
<?php
}
?>