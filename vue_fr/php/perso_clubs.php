<?php
$communautes_manager = new CommunautesMan($bdd);
$communautes = $communautes_manager->getCommunautesByMembre($membre->ID);
?>
<div class="row_content">
	<div class="col-md-12">
		<h2>Mes clubs</h2>
	
		<hr />
		
		<p class="text-center">
			<a href="clubs.php" class="btn btn-custom">Découvrir tous les clubs <span class="glyphicon glyphicon-chevron-right"></span></a>
			|
			<a href="club.php" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Proposer un club</a>
		</p>
		
		<hr />
		
		<div class="row">
			<div class="col-xs-12 col-sm-offset-3 col-sm-6">
				<a class="video_link" data-video="EEgH2YB5iQs"></a>
			</div>
		</div>
		
		<br />
		
		<?php
		if(isset($_SESSION['annonce_removed']))
		{
			echo '<div class="alert alert-info text-center" role="alert">Votre annonce a bien été supprimée.</div>';
			unset($_SESSION['annonce_removed']);
		}
		?>
		
		<div class="row" style="text-align: center; padding: 2px;">
			<?php
			foreach($communautes as $communaute)
			{
			$isMember = $communautes_manager->isMemberOrPendingMember($communaute->ID, $membre->ID);
			$isNotif = $communautes_manager->isNotifClubByMembre($membre->ID, $communaute->ID);
			
			if($isNotif)
				$communautes_manager->delNotifClubByMembre($membre->ID, $communaute->ID);
			?>
				<div class="col-sm-12 col-sm-6 col-md-4 col-md-3<?php if($isNotif) echo ' clignoter'; ?>" style="display: inline-block; float: none; margin: -2px;">
					<div class="thumbnail" style="padding: 0; overflow: hidden;">
						<div style="position: relative; width: 100%; padding-bottom: 100%;">
							<a href="club.php?id=<?php echo $communaute->ID; ?>" class="thumbnail" style="border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo IMAGES_COMMUNAUTES . $communaute->image; ?>') no-repeat; background-position: center; background-size: cover;"></a>
						</div>
					
						<div class="caption">
							<h3 style="height: 50px; margin-top: 10px;"><span class="glyphicon glyphicon-flag"></span> <span class="blue_custom" id="club_<?php echo $communaute->ID; ?>"><?php echo htmlspecialchars($communaute->nom); ?></span></h3>
							
							<p style="height: 100px; overflow: hidden;" class="text-center"><?php echo nl2br(htmlspecialchars($communaute->description)); ?></p>
							
							<hr />
							
							<p class="text-center">
								<?php
								if($isMember == 1)
									echo '<a href="" class="btn btn-default" onclick="return false;"><span class="glyphicon glyphicon-hourglass"></span> En attente d\'acceptation</a>';
								else
									echo '<a href="clubs.php?id=' . $communaute->ID . '" class="btn btn-default"><span class="glyphicon glyphicon-ok"></span> Vous êtes membre</a>';
								?>
							</p>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</div>