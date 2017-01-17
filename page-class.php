<?php

class Page
{
	protected $nom_fichier;
	protected $title;
	protected $membre;
	protected $bdd;
	protected $infos;
	protected $notWithConnexion = array('connexion', 'inscription');

	public function __construct($nom_fichier = '', $membre = false, $bdd = false, $infos = false, $title = false)
	{
		$this->nom_fichier = $nom_fichier;
		$this->membre = $membre;
		$this->bdd = $bdd;
		$this->infos = $infos;
		$this->title = $title;

		$this->afficher();
	}

	public function page2title()
	{
		if($this->title)
		{
			$tableauTitle['annonce_afficher'] = 'annonce : ' . htmlspecialchars($this->title);
			$tableauTitle['membre_apercu'] = 'membre : ' . htmlspecialchars($this->title);
			$tableauTitle['club'] = 'club : ' . htmlspecialchars($this->title);
			$tableauTitle['categorie'] = 'catégorie : ' . htmlspecialchars($this->title);
			$tableauTitle['documentation_section'] = htmlspecialchars($this->title);
		}

		$tableauTitle['administration'] = 'page d\'administration';
		$tableauTitle['annonce_inactive'] = 'annonce désactivée';
		$tableauTitle['annonce_modifier'] = 'modifier mon annonce';
		$tableauTitle['annonce_nouvelle'] = 'déposer une annonce';
		$tableauTitle['annonce_reservations'] = 'gérer mes réservations';
		$tableauTitle['annonce_supprimee'] = 'annonce inexistante';
		$tableauTitle['connexion'] = 'page de connexion';
		$tableauTitle['inscription'] = 'page d\'inscription';
		$tableauTitle['litige'] = 'déclarer un litige';
		$tableauTitle['page_incorrecte'] = 'page incorrecte';
		$tableauTitle['panier'] = 'mon panier';
		$tableauTitle['perso'] = 'mon compte personnel';
		$tableauTitle['recherche'] = 'page de recherche';
		$tableauTitle['reservation'] = 'gérer la réservation';
		$tableauTitle['membre_apercu_invalide'] = 'compte supprimé';
		$tableauTitle['connexion_email_invalide'] = 'renvoyer le mail d\'inscription';
		$tableauTitle['clubs'] = 'les clubs de ' . SITE_NOM;
		$tableauTitle['club_proposer'] = 'proposer un nouveau club';
		$tableauTitle['annonce_interdite'] = 'accès interdit';
		$tableauTitle['creer_club'] = 'créer un club';
		$tableauTitle['oubli_mdp'] = 'mot de passe oublié';
		$tableauTitle['documentation'] = 'documentation officielle de ' . SITE_NOM;
		$tableauTitle['paiement'] = 'Paiement sécurisé';

		$titre = '';

		if($this->nom_fichier != 'index' && !empty($tableauTitle[$this->nom_fichier]))
			$titre = ucfirst($tableauTitle[$this->nom_fichier]) . ' | ';

		return $titre . SITE_NOM . ' - Coiffure à domicile';
	}

	public static function getFormulaireContact()
	{
	?>
		<div class="row" style="background: rgb(235, 235, 235); padding: 0 0 20px 0; position: relative;">
			<h4 class="text-center" style="margin-bottom: 30px;">Formulaire de contact</h4>

			<ul style="width: 100%; list-style-type: none; margin: 0;">
				<li class="col-xs-12 col-sm-4 text-center-sm">
					<table style="width: 100%; margin: 0 0 30px 0;">
						<tr>
							<td style="display: inline-block;"><span class="glyphicon glyphicon-envelope blue_custom" style="font-size: 60px;"></span></td>
							<td class="text-left" style="display: inline-block; padding-left: 10px;">
								<h4 class="rose_custom" style="font-weight: bold; margin: 0 0 5px 0;">Par mail</h4>

								<a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
							</td>
						</tr>
					</table>
				</li>
				<li class="col-xs-12 col-sm-4 text-center-sm">
					<table style="width: 100%; margin: 0 0 30px 0;">
						<tr>
							<td style="display: inline-block;"><span class="glyphicon glyphicon-phone-alt blue_custom" style="font-size: 60px; height: 100%;"></span></td>
							<td class="text-left" style="display: inline-block; padding-left: 10px;">
								<h4 class="rose_custom" style="font-weight: bold; margin: 0 0 5px 0;">Par téléphone</h4>

								<a href="tel:<?php echo SITE_TEL; ?>"><?php echo SITE_TEL_FORMATED; ?></a>
							</td>
						</tr>
					</table>
				</li>
				<li class="col-xs-12 col-sm-4 text-center-sm">
					<table style="width: 100%; margin: 0 0 30px 0;">
						<tr>
							<td style="display: inline-block;"><span class="glyphicon glyphicon-map-marker blue_custom" style="font-size: 60px; height: 100%;"></span></td>
							<td class="text-left" style="display: inline-block; padding-left: 10px;">
								<h4 class="rose_custom" style="font-weight: bold; margin: 0 0 5px 0;">Par courrier</h4>

								<address style="margin: 0;"><strong><?php echo SITE_NOM; ?> - Service client</strong><br /><?php echo SITE_ADRESSE; ?></address>
							</td>
						</tr>
					</table>
				</li>
			</ul>
		</div>
	<?php
	}

	protected function afficher()
	{
		$title = $this->page2title();
		$infos = $this->infos;
		$membre = $this->membre;
		$bdd = $this->bdd;

		if(file_exists($_SESSION['dossier_vue'] . '/php/' . $this->nom_fichier . '.php'))
		{
		?><!DOCTYPE html>
		<html lang="fr">
			<head>
				<meta charset="utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
				<meta name="createdBy" content="https://www.raidghost.com/">
				<meta name="msapplication-TileColor" content="<?php echo SITE_COLOR; ?>">
				<meta name="theme-color" content="<?php echo SITE_COLOR; ?>">
				<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
				<link rel="stylesheet" href="style.css" />
				<link rel="stylesheet" href="bootstrap-chosen-master/bootstrap-chosen.css" />
				<link rel="icon" type="image/png" sizes="96x96" href="sources/favicon-96x96.png">
				<meta name="keywords" content="coiffure, coiffeur, service, particulier, échange, plateforme, annonces" />
				<?php
				if($this->nom_fichier == 'index')
					echo '<meta name="description" content="' . SITE_NOM . ' La coiffure à domicile" />';
				?><title><?php echo $title; ?></title>
				<?php
				if(!$membre || $membre->ID != 2)
				{
				?>
				<script>
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					ga('create', 'UA-71851839-1', 'auto');
					ga('send', 'pageview');
				</script>
				<?php
				}
				?>
			</head>

			<body>
				<script src="<?php echo JQUERY_URL; ?>"></script>
				<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
				<script type="text/javascript" src="<?php echo $_SESSION['dossier_vue']; ?>/js/script.js"></script>

				<?php
				if(file_exists($_SESSION['dossier_vue'] . '/js/' . $this->nom_fichier . '.js'))
					echo '<script type="text/javascript" src="' . $_SESSION['dossier_vue'] . '/js/' . $this->nom_fichier . '.js"></script>';
				?>

				<nav class="navbar navbar-default" style="position: relative;">
					<div class="container-fluid" style="background-color: rgb(250, 250, 250); padding: 0;">
						<div class="navbar-header" style="width: 100%;">
							<!-- BOUTTON NAVBAR -->
							<a style="margin: 11px;" href="annonce.php" class="btn btn-custom pull-right" role="button"><span class="glyphicon glyphicon-edit"></span> Prendre un rendez-vous</a>
							<a style="margin: 11px;" href="annonce.php" class="btn btn-custom pull-right" role="button"><span class="glyphicon glyphicon-scissors"></span> Espace coiffeur</a>


							<ul id="navbar-header" class="pull-right">
								<li>

									<!--<a href="panier.php" role="button"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><span class="hidden-xs hidden-sm"> Mon panier</span>--><?php
									$quantite_panier = 0;

									foreach($_SESSION['panier'] as $value)
										$quantite_panier += $value['quantite'];

									if($quantite_panier)
										echo ' <span class="badge badge-custom">' . $quantite_panier . '</span>';


									?></a>
								</li>

								<li class="dropdown" id="dropdown_membre">



								<?php
								if($membre)
								{
									$messagerie_manager = new MessagerieMan($bdd);
									$transactions_manager = new TransactionsMan($bdd);
									$parrainage_manager = new ParrainagesMan($bdd);
									$communautes_manager = new CommunautesMan($bdd);

									$nbr_nouveaux_parrainages = $parrainage_manager->countNouveauxParrainages($membre->ID);
									$nbr_messages_non_lus = $messagerie_manager->countNouveauxMessages($membre->ID);
									$nbr_notifs_transactions_proprio = $transactions_manager->countNotifsTransactionsAsProprio($membre->ID);
									$nbr_notifs_transactions_locataire = $transactions_manager->countNotifsTransactionsAsLocataire($membre->ID);
									$nbr_notifs_clubs = $communautes_manager->countNotificationsMembre($membre->ID);

									if($membre->administrateur)
									{
										$litiges_manager = new LitigesMan($bdd);
										$objets_manager = new ObjetsMan($bdd);
										$versements_reel_manager = new Versements_reelsMan($bdd);

										$nbr_notifs_admin = $communautes_manager->countNotifsPropositions() + $litiges_manager->countLitiges() + $objets_manager->countAnnoncesErrors() + $versements_reel_manager->countVersementsEnAttente();
									}
									else
										$nbr_notifs_admin = 0;

									$nbr_notifs = $nbr_notifs_clubs + $nbr_nouveaux_parrainages + $nbr_messages_non_lus + $nbr_notifs_transactions_proprio + $nbr_notifs_transactions_locataire + $nbr_notifs_admin;
									?>
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo ($membre->avatar == '') ? '<span class="glyphicon glyphicon-user" style="font-size: 18px;"></span>' : ('<img class="img-circle" style="width: 27px; height: 27px;" alt="' . $membre->prenom . ' ' . $membre->nom . '" src="avatars/' . $membre->avatar . '">'); ?> <?php echo '<span class="hidden-xs hidden-sm">' . htmlspecialchars($membre->prenom) . ' ' . htmlspecialchars($membre->nom) . '</span>'; if($nbr_notifs) echo ' <span class="badge badge-custom">' . $nbr_notifs . '</span>'; ?> <span class="caret"></span></a>


									<ul class="dropdown-menu dropdown-menu-left" role="menu">
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?dashboard"><span class="glyphicon glyphicon-dashboard" style="margin-right: 5px;"></span> Mon tableau de bord<?php if($nbr_nouveaux_parrainages) echo ' <span class="badge badge-custom">' . $nbr_nouveaux_parrainages . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?clubs"><span class="glyphicon glyphicon-flag" style="margin-right: 5px;"></span> Mes clubs<?php if($nbr_notifs_clubs) echo ' <span class="badge badge-custom">' . $nbr_notifs_clubs . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?messages"><span class="glyphicon glyphicon-envelope" style="margin-right: 5px;"></span> Mes messages<?php if($nbr_messages_non_lus) echo ' <span class="badge badge-custom">' . $nbr_messages_non_lus . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?demandes_de_location"><span class="glyphicon glyphicon-check" style="margin-right: 5px;"></span> Mes demandes de location<?php if($nbr_notifs_transactions_locataire) echo ' <span class="badge badge-custom">' . $nbr_notifs_transactions_locataire . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?annonces"><span class="glyphicon glyphicon-list-alt" style="margin-right: 5px;"></span> Mes annonces<?php if($nbr_notifs_transactions_proprio) echo ' <span class="badge badge-custom">' . $nbr_notifs_transactions_proprio . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?revenus"><span class="glyphicon glyphicon-stats" style="margin-right: 5px;"></span> Mes revenus</a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="perso.php?compte"><span class="glyphicon glyphicon-wrench" style="margin-right: 5px;"></span> Mon compte</a></li>
										<li role="presentation" class="divider"></li>

										<?php
										if($membre->administrateur)
										{
										?>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="administration.php"><span class="glyphicon glyphicon-cog" style="margin-right: 5px;"></span> Administration<?php if($nbr_notifs_admin) echo ' <span class="badge badge-custom">' . $nbr_notifs_admin . '</span>'; ?></a></li>
										<li role="presentation"><a role="menuitem" tabindex="-1" href="membre.php"><span class="glyphicon glyphicon-user" style="margin-right: 5px;"></span> Liste des membres</a></li>
										<li role="presentation" class="divider"></li>
										<?php
										}
										?>
										<li class="text-center"><form style="border: 0; margin: 0;" class="navbar-form" method="post" action="documentation.php#contact"><button class="btn btn-custom center-block"><span class="glyphicon glyphicon-star" style="margin-right: 5px;"></span> Devenir partenaire</button></form></li>
										<li role="presentation" class="divider"></li>
										<li role="presentation" class="text-center"><a role="menuitem" tabindex="-1" href="deconnexion.php"><span class="glyphicon glyphicon-off" style="margin-right: 5px;"></span> Se déconnecter</a></li>
									</ul>
									<?php
								}
								else
								{
								?>

								<!-- Connection Pop-up à remettre -->
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><span class="hidden-xs hidden-sm"> <span id="sidentifier" style="width: 86px; display: inline-block;">Se connecter</span><span class="caret"></span></a>

									<ul class="dropdown-menu dropdown-menu-left" role="menu">
										<li role="presentation">
											<form style="width: 240px;" method="post" action="connexion.php">
												<div class="form-group text-center">
													<div class="input-group" style="margin: 10px;">
														<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
														<input type="text" id="email_connec" name="email_connec" class="form-control" placeholder="Email" required>
													</div>
													<div class="input-group" style="margin: 10px;">
														<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														<input type="password" name="mdp_connec" class="form-control" placeholder="Mot de passe" required>
													</div>
													<input type="checkbox" name="cookies_connec" id="cookies_connec" /><label for="cookies_connec" style="padding-left: 10px;"> Connection auto</label>
													<input type="hidden" name="redirect_connec" value="<?php echo in_array($this->nom_fichier, $this->notWithConnexion) ? '/' : htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
												</div>

												<button style="margin-top: 5px;" type="submit" class="btn btn-default center-block">Se connecter <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
												<hr style="margin: 10px;">
											</form>

											<form style="border: 0; margin: 0;" class="navbar-form" method="post" action="inscription.php"><button class="btn btn-custom center-block"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Créer un compte</button></form>

											<p class="text-center" style="margin: 0; padding: 0;"><a href="oubli_mdp.php">Mot de passe oublié</a></p>
										</li>
									</ul>
								<?php
								}
								?>
								</li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</div><!-- /.container-fluid -->
				</nav>

				<nav class="navbar navbar-default" style="border-bottom: 1px solid #EEE; z-index: 20; height: 68px;">
					<div class="container-fluid" style="padding: 0; height: 100%;">
						<div class="navbar-header" style="width: 100%; margin: 0;">
							<h1 style="text-align: center; margin: 0;">
								<a href="/" style="display: block; text-decoration: none; width: 100%; padding-top: 10px; outline: none;">
									<span style="position: relative;">
										<img src="sources/originaux/logo.png" style="width: 500px; position: absolute; top: -64px; left: -215px;" alt="" class="hidden-xs" />
										<img src="sources/originaux/logo.png" style="width: 55px; position: absolute; left: 0;" alt="" class="visible-xs" />
										<span style="font-size: 1.05em; padding-left: 60px;"></span>
									</span>

									<span style="font-size: 0.75em; margin-left: 10px;" class="hidden-xs rose_custom">



									</span>
								</a>
							</h1>
						</div><!-- /.navbar-collapse -->
					</div><!-- /.container-fluid -->
				</nav>

				<?php
				$hide_sous_categories_pages = array('clubs',
													'perso',
													//'panier',

													'documentation',
													'documentation_section',
													'reservation',
													'administration',
													'club',
													'membre_apercu',
													'annonce_afficher',
													'annonce_inactive',
													'annonce_interdite',
													'annonce_modifier',
													'annonce_supprimee',
													'annonce_reservations',
													'annonce_nouvelle',
													'paiement');


				if(!in_array($this->nom_fichier, $hide_sous_categories_pages))
				{
				?>

				<!--    NAVBAR relié à la base de donnée



				<nav class="navbar navbar-default hidden-xs" id="navbar_categories">
					<div class="container-fluid">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" style="margin: 10px;">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div id="navbar" class="navbar-collapse collapse">
							<ul class="nav nav-pills nav-justified">

								<?php
								$categories_manager = new CategoriesMan($bdd);
								$categories = $categories_manager->getCategories();

								foreach($categories as $categorie)
								{
									echo '<li role="presentation"><a ';

									if(!empty($_GET['c']) && $_GET['c'] == $categorie->ID)
										echo 'class="active"';

									echo ' href="categorie.php?c=' . $categorie->ID . '"><span style="position: relative; top: -2px; vertical-align:middle; display:inline-block; line-height:normal;">' . htmlspecialchars($categorie->nom) . '</span></a></li>';
								}
								?>
							</ul>
						</div>
					</div>
				</nav>
			-->
				<?php
				}
				?>

				<div id="content">
					<div class="modal fade" role="dialog" id="modal_video">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content embed-responsive embed-responsive-16by9" style="border: 0; border-radius: 0; margin-top: 60px; background-color: black;">
								<iframe id="video_iframe" style="width: 100%; height: 100%; border: none;" src="" allowfullscreen></iframe>
								<script src="//www.youtube.com/player_api"></script>
							</div>
						</div>
					</div>

					<?php require_once($_SESSION['dossier_vue'] . '/php/' . $this->nom_fichier . '.php'); ?>
				</div>

				<footer style="padding: 20px 0 20px 0; position: relative;">
					<img src="sources/originaux/logo.png" style="position: absolute; right: 10px; top: 20; height: 50%;" class="hidden-xs hidden-sm hidden-md">

					<div class="container">
						<div class="row">
							<div class="col-sm-4" style="padding-left: 35px;">
								<h4>Découvrir</h4>

								<p>
									<a href="documentation.php">Documentation</a>
									<br />
									<a href="documentation.php?tout_sur_club_de_lok#a_propos">À propos</a>
									<br />
									<a href="documentation.php?tout_sur_club_de_lok#cgu">Conditions Générales d'Utilisation</a>
								</p>
							</div>

							<div class="col-sm-4" style="border-left: 1px solid grey; padding-left: 35px;">
								<h4>Aide</h4>

								<p>
									<a href="documentation.php?aide#aide_aux_locataires">Aide aux locataires</a>
									<br />
									<a href="documentation.php?aide#aide_aux_proprietaires">Aide aux propriétaires</a>
									<br />
									<a href="documentation.php#contact">Contact</a>
								</p>
							</div>

							<div class="col-sm-4" style="border-left: 1px solid grey; padding-left: 35px;">
								<h4>Suivez-nous</h4>

								<p style="margin: 15px 0 15px 0;">
									<a href="<?php echo LINK_FACEBOOK; ?>" onclick="window.open(this.href); return false;" style="margin-right: 15px;"><img src="sources/link_facebook.png" alt="Facebook"></a>
									<a href="<?php echo LINK_TWITTER; ?>" onclick="window.open(this.href); return false;" style="margin-right: 15px;"><img src="sources/link_twitter.png" alt="Twitter"></a>
									<a href="<?php echo LINK_GOOGLE; ?>" onclick="window.open(this.href); return false;"><img src="sources/link_google.png" alt="Google+"></a>
								</p>
							</div>
						</div>

						<!--hr style="margin-bottom: 0;" />

						<p class="text-center"><?php //echo SITE_NOM; ?> - © 2016</p-->
					</div>
				</footer>
			</body>
		</html>
		<?php
		}
		/*
		else
			new Page('erreurs', $membre, $bdd);*/
	}
}
