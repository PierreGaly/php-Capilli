<div class="row row_index" style="background-image: url('sources/haircut1.jpg');">
	<div class="row_content">
		<?php
		$objets_manager = new ObjetsMan($bdd);

		if(isset($_SESSION['parrainage_valide']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Vous avez suivi le lien de parrainage de <a href="membre.php?id=' . $_SESSION['parrainage_valide']->ID . '"><em>' . htmlspecialchars($_SESSION['parrainage_valide']->prenom) . ' ' . htmlspecialchars($_SESSION['parrainage_valide']->nom) . '</em></a>.<br />Il deviendra votre parrain si vous vous inscrivez lors de la session actuelle.<hr style="margin: 5px;"/><a href="documentation.php?tout_sur_club_de_lok#devenir_parrain">Qu\'est-ce que le parrainage ?</a></div>';
			unset($_SESSION['parrainage_valide']);
		}
		else if(isset($_SESSION['parrainage_invalide_inscrit']))
		{
			echo '<div class="alert alert-danger text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-info-sign"></span> Vous ne pouvez pas être parrainé par <a href="membre.php?id=' . $_SESSION['parrainage_invalide_inscrit']->ID . '"><em>' . htmlspecialchars($_SESSION['parrainage_invalide_inscrit']->prenom) . ' ' . htmlspecialchars($_SESSION['parrainage_invalide_inscrit']->nom) . '</em></a> car vous êtes déjà inscrit.<hr style="margin: 5px;"/><a href="documentation.php?tout_sur_club_de_lok#devenir_parrain">Qu\'est-ce que le parrainage ?</a></div>';
			unset($_SESSION['parrainage_invalide_inscrit']);
		}
		else if(isset($_SESSION['parrainage_invalide_existe']))
		{
			echo '<div class="alert alert-danger text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-remove-sign"></span> Le lien de parrainage que vous avez suivi n\'est plus valide.<hr style="margin: 5px;"/><a href="documentation.php?tout_sur_club_de_lok#devenir_parrain">Qu\'est-ce que le parrainage ?</a></div>';
			unset($_SESSION['parrainage_invalide_existe']);
		}

		if(isset($_SESSION['membre_just_inscrit']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Votre demande d\'inscription à ' . SITE_NOM . ' a été prise en compte.';

			if(!$membre)
				echo '<br />Pour vous connecter, merci de suivre le lien de validation envoyé par mail à  l\'adresse : <em>' . htmlspecialchars($_SESSION['membre_just_inscrit']) . '</em>.';

			echo '</div>';
			unset($_SESSION['membre_just_inscrit']);
		}

		if(isset($_SESSION['desinscription_valide']))
		{
			echo '<div class="alert alert-info text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button><span class="glyphicon glyphicon-ok-sign"></span> Votre demande de désinscription à ' . SITE_NOM . ' a été prise en compte.<br />Nous sommes désolés de vous voir partir et espérons vous revoir bientôt pour de nouvelles coupe !</div>';
			unset($_SESSION['desinscription_valide']);
		}
		?>

		<div class="row" style="display: none;">
			<div class="col-md-offset-3 col-md-6" style="margin-top: 20px; margin-bottom: 20px;">
				<a class="video_link" data-video="ZQCxXObxEZA"></a>
			</div>
		</div>



		<form method="get" action="recherche.php" class="form-inline text-center" style="margin: 130px 0 100px 0;">
			<div class="row">
				<div class="form-group input-group-lg" style="font-size: 0;">

					<!--			Barre de recherche "Où" et "Quand"


					<div class="form-group">
						<input autofocus autocomplete="off" type="text" class="form-control input-lg" style="box-shadow: 0 0 10px rgb(220, 220, 220);" name="mc" id="mc_index" placeholder="Quoi ?">
					</div>

					<div class="form-group has-feedback">
						<input type="text" class="form-control input-lg" style="border-radius: 0; box-shadow: 0 0 10px rgb(220, 220, 220);" name="localisation" id="localisation" placeholder="Où ?">
						<span class="glyphicon glyphicon-map-marker form-control-feedback" style="font-size: 15px;"></span>
						<input type="hidden" id="s_lat" name="s_lat" value="">
						<input type="hidden" id="s_lng" name="s_lng" value="">
					</div>
				-->


					<div class="form-group">
						<button class="form-control input-lg btn-lg btn btn-custom" style="box-shadow: 0 0 10px rgb(220, 220, 220);" id="submit_index"><span class="glyphicon glyphicon-scissors"></span>  Prendre rendez-vous</button>
					</div>
				</div>
			</div>
		</form>



		<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp&amp;"></script>
	</div>
</div>

<div class="row hidden-xs">
	<div class="row_content" style="padding-bottom: 20px;">
		<h2 style="padding-top: 20px; padding-bottom: 20px;"><strong><?php echo SITE_NOM; ?></strong>, Votre coiffeur à domicile</h2>

		<div class="row">
			<?php
			$messages = array();

			if($membre)
				$messages[] = array('glyphicon' => 'edit', 'titre' => 'Déposez une annonce', 'message1' => 'Déposez vos annonces en 2 minutes', 'message2' => 'Adhérez à vos clubs favoris', 'link' => 'annonce.php');
			else
				$messages[] = array('glyphicon' => 'user', 'titre' => 'Créer un compte', 'message1' => 'Inscrivez-vous en 2 minutes', 'message2' => '', 'link' => 'inscription.php');

			$messages[] = array('glyphicon' => 'map-marker', 'titre' => 'Se localiser', 'message1' => 'Localisez-vous pour trouver des coiffeurs à côté de chez vous', 'message2' => '');
			$messages[] = array('glyphicon' => 'search', 'titre' => 'Consulter les prestations', 'message1' => 'Un large choix de prestations de coiffure pour tout genre', 'message2' => '');
			$messages[] = array('glyphicon' => 'ok', 'titre' => 'Sélectionner un coiffeur', 'message1' => 'Parmis de nombreux coiffeurs adhérent à Capilli-home', 'message2' => '');

			foreach($messages as $message)
			{
				if(empty($message['link']))
					echo '<div class="col-xs-12 col-sm-6 col-md-3" style="text-align: center;">';
				else
					echo '<a href="' . $message['link'] . '" class="col-xs-12 col-sm-6 col-md-3 " style="text-decoration: none; text-align: center; display: block">';
				?>
				<p class="row_index center-block" style="background-image: url('sources/haircut1.jpg'); border-radius: 50%; width: 100px; height: 100px; padding: 25px;">
					<span class="glyphicon glyphicon-<?php echo $message['glyphicon']; ?>" style="font-size: 50px; color: #FF0040; text-shadow: 5px 5px 8px black;"></span>
				</p>

				<p>
					<strong style="color: red"><?php echo $message['titre']; ?></strong>
				</p>

				<p>
					<?php echo $message['message1']; ?>
					<br />
					<?php echo $message['message2']; ?>
				</p>
				<?php
				if(empty($message['link']))
					echo '</div>';
				else
					echo '</a>';
			}
			?>
		</div>
	</div>
</div>

<div class="row">
	<div class="row_content" style="padding-bottom: 20px;">
		<h2 style="padding-top: 20px; padding-bottom: 20px;"><span class="glyphicon glyphicon-info-sign" style="font-size: 50px; color: #FF0040; text-shadow: 0px 0px 8px white;"></span> Découvrez nos packs<br /><small style="color: white">Des coiffeurs engagés près a se rendre dans votre salon</small></h2>

		<div class="row" style="text-align: center; padding: 2px;">
			<?php
			$communautes_manager = new CommunautesMan($bdd);
			$communautes = $communautes_manager->getClubsAccueil();

			foreach($communautes as $communaute)
			{
				$isMember = $membre ? $communautes_manager->isMemberOrPendingMember($communaute->ID, $membre->ID) : 0;
				?>

<!--       Remplacé Communauté Par 3 Packs Coiffure   (juste en dessous)          -->
<!--       Remplacé Communauté Par 3 Packs Coiffure             -->
<!--       Remplacé Communauté Par 3 Packs Coiffure             -->
<!--       Remplacé Communauté Par 3 Packs Coiffure             -->



					<div class="col-sm-12 col-sm-6 col-md-4 col-md-3" style="display: inline-block; float: none; margin: -2px;">
						<div class="thumbnail" style="padding: 0; overflow: hidden;">
							<div style="position: relative; width: 100%; padding-bottom: 100%;">
								<a href="club.php?id=<?php echo $communaute->ID; ?>" class="thumbnail" style="border: 0; border-radius: 0; position:absolute; width: 100%; height: 100%; background: url('<?php echo IMAGES_COMMUNAUTES . $communaute->image; ?>') no-repeat; background-position: center; background-size: cover;"></a>
							</div>

							<div class="caption">
								<h3 style="height: 50px; margin-top: 10px;"><span style="color: red" class="glyphicon glyphicon-scissors"></span> <span class="blue_custom" id="club_<?php echo $communaute->ID; ?>"><?php echo htmlspecialchars($communaute->nom); ?></span></h3>

								<p style="height: 100px; overflow: hidden;" class="text-center"><?php echo nl2br(htmlspecialchars($communaute->description)); ?></p>

								<hr />

								<p class="text-center">
									<?php
									if($isMember == 0)
										echo '<a href="clubs.php?id=' . $communaute->ID . '" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Consulter</a>';
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

		<hr/>
		<div class="blog">
			<p class="blogConcept">
				<center><a style="color: #FF0040;">Consultez notre blog pour être au courant des tendances</a>
					<h1>Capille-Blog</h1>
					<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  					<ol class="carousel-indicators">
    					<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
    					<li data-target="#carousel-example-generic" data-slide-to="1"></li>
    					<li data-target="#carousel-example-generic" data-slide-to="2"></li>
  					</ol>

  <!-- Wrapper for slides -->
  					<div class="carousel-inner" role="listbox">
    					<div class="item active">
      					<img style="width: 50%; height: 100%;" src="slide/header.jpg" alt="Coiffure femme">
      					<div class="carousel-caption">
        					Coiffure femme
      					</div>
    					</div>
    						<div class="item">
      						<img style="width: 50%;" src="slide/haircut.jpg" alt="Coiffeur homme">
      						<div class="carousel-caption">
        						Coiffure homme
      						</div>
    					</div>
    							Coiffure blog
  				</div>

					  <!-- Controls -->
					  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
					    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					    <span class="sr-only">Previous</span>
					  </a>
					  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
					    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					    <span class="sr-only">Next</span>
					  </a>
					</div>

								</p>
							</div>
							<div>
							<p class="text-center">
								<a href="clubs.php" class="btn btn-custom btn-lg">Prendre rendez-vous <span class="glyphicon glyphicon-chevron-right"></span></a>
							</p>
						</div>
					</div>

						</div>
					</div>
