<div class="row">
	<div class="row_content">
		<h2>Les clubs de <?php echo SITE_NOM; ?></h2>
		
		<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal_club">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<form role="form" method="post" action="" class="form-horizontal">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Adhérer à un club</h4>
						</div>
						
						<div class="modal-body">
							<div class="alert alert-info text-center" role="alert">Voulez-vous faire partie du club <strong><span id="nom_club"><?php if(!empty($infos['demande_club']) && !$infos['is_membre_demande_club']) echo htmlspecialchars($infos['demande_club']->nom); ?></span></strong> ?<br />Un membre quelconque du club se chargera d'accepter ou non votre profil.</div>
						</div>
						
						<div class="modal-footer form-inline" style="text-align: center;">
							<input type="hidden" name="id_club_adhesion" id="id_club_adhesion" value="<?php if(!empty($infos['demande_club']) && !$infos['is_membre_demande_club']) echo $infos['demande_club']->ID; ?>">
							<button class="form-control btn btn-custom" role="submit">Demander l'adhésion <span class="glyphicon glyphicon-chevron-right" style="margin-right: 5px;"></span></button>
						</div>
					</form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<div class="alert alert-info text-center" role="alert">
			Les <strong>clubs </strong> sont des lieux d'échange et de partage qui regroupent des biens et des personnes ayant une caractéristique en commun (goût, lieu d'étude, passion, etc...).
			<br />
			N'hésitez pas à les découvrir en adhérant à certains d'entre eux ou à proposer votre club !
		</div>

		<div class="row">
			<p class="text-center">
				<a href="club.php" class="btn btn-custom" role="button"><span class="glyphicon glyphicon-plus"></span> Proposer un club</a>
			</p>
		</div>
		
		<hr />
		
		<?php
		if(isset($_SESSION['club_adhesion']))
		{
			if($_SESSION['club_adhesion'])
				echo '<div class="alert alert-danger text-center" role="alert">Vous ne pouvez pas </div>';
			else
				echo '<div class="alert alert-info text-center" role="alert">Votre demande d\'adhésion a été prise en compte.</div>';
			
			unset($_SESSION['club_adhesion']);
		}
		
		if(!empty($infos['demande_club']) && $infos['is_membre_demande_club'])
			echo '<div class="alert alert-danger text-center" role="alert">Vous avez déjà fait une demande pour ce club.</div>';
		?>
		
		<div class="row" style="text-align: center; padding: 2px;">
			<?php
			$communautes_manager = new CommunautesMan($bdd);
			$communautes = $communautes_manager->getCommunautes();
			
			foreach($communautes as $communaute)
			{
				$isMember = $membre ? $communautes_manager->isMemberOrPendingMember($communaute->ID, $membre->ID) : 0;
				?>
					<div class="col-sm-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
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
									if($isMember == 0)
										echo '<button class="btn btn-primary button_club" data-toggle="modal" data-target="#modal_club" data-id_club="' . $communaute->ID . '"><span class="glyphicon glyphicon-ok"></span> Je souhaite en faire partie</button>';
									else if($isMember == 1)
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