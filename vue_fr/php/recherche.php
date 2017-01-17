<?php
if($membre)
{
	$clubs_manager = new CommunautesMan($bdd);
	$clubs = $clubs_manager->getCommunautesByMembre($membre->ID);
}

$current_lat = (!empty($_GET['s_lat']) && !empty($_GET['localisation'])) ? $_GET['s_lat'] : false;
$current_lng = (!empty($_GET['s_lng']) && !empty($_GET['localisation'])) ? $_GET['s_lng'] : false;
$current_categorie = isset($_GET['c']) ? $_GET['c'] : -1;
$current_sous_categorie = isset($_GET['sc']) ? $_GET['sc'] : -1;
$current_club = (isset($_GET['cl']) && $membre && $clubs_manager->isMembre($_GET['cl'], $membre->ID)) ? $_GET['cl'] : -1;
$current_mot_cle = !empty($_GET['mc']) ? $_GET['mc'] : '';
?>
<div class="row" style="padding-bottom: 0; margin: 0;">
	<div class="col-xs-12 col-sm-6 col-lg-4" id="annonces" style="padding: 0;">
		<div style="width: 100%; padding: 20px; padding-top: 0; padding-bottom: 0; margin: 0;">
			<h3>Effectuer une recherche</h3>
			
			<img style="display: none" src="sources/logo_marker_gris.png">
			
			<form method="get" action="" class="form-horizontal" role="form">
				<div class="form-group has-feedback">
					<input type="text" autofocus name="mc" id="mc" placeholder="Quoi ?" class="form-control" value="<?php echo $current_mot_cle; ?>">
				</div>
				
				<div class="form-group has-feedback">
					<input type="text" name="localisation" id="localisation" placeholder="Où ?" class="form-control"<?php if(!empty($_GET['localisation'])) echo ' value="' . $_GET['localisation'] . '"'; ?>>
					<input type="hidden" id="s_lat" name="s_lat" value="<?php if(!empty($_GET['s_lat'])) echo $_GET['s_lat']; ?>">
					<input type="hidden" id="s_lng" name="s_lng" value="<?php if(!empty($_GET['s_lng'])) echo $_GET['s_lng']; ?>">
					<i class="glyphicon glyphicon-map-marker form-control-feedback"></i>
				</div>
				
				<div class="form-group has-feedback has-feedback">
					<select name="c" id="c" class="form-control">
						<option value="-1">Toutes les catégories</option>
						<?php
						$categories_manager = new CategoriesMan($bdd);
						$categories = $categories_manager->getCategories();
						
						foreach($categories as $categorie)
						{
							echo '<option value="' . $categorie->ID . '"';
							
							if(isset($_GET['c']) && $_GET['c'] == $categorie->ID)
								echo ' selected';
							
							echo '>' . htmlspecialchars($categorie->nom) . '</option>';
						}
						?>
					</select>
				</div>
				
				<div class="form-group has-feedback has-feedback"<?php if($current_categorie == -1) echo ' style="display: none;"'; ?> id="bloc_sous_categories">
					<select name="sc" id="sc" class="form-control">
						<option value="-1">Toutes les sous-catégories</option>
						<?php
						$sous_categories_manager = new SousCategoriesMan($bdd);
						$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($current_categorie);
						
						foreach($sous_categories as $sous_categorie)
						{
							echo '<option value="' . $sous_categorie->ID . '"';
							
							if(isset($_GET['sc']) && $_GET['sc'] == $sous_categorie->ID)
								echo ' selected';
							
							echo '>' . htmlspecialchars($sous_categorie->nom) . '</option>';
						}
						?>
					</select>
				</div>
				
				<?php
				if(!empty($clubs))
				{
				?>
					<div class="form-group has-feedback has-feedback">
						<label for="cl">Filtrer par club</label>
						<select name="cl" id="cl" class="form-control">
							<optgroup label="Ne pas filtrer :">
								<option value="-1">Annonces publiques</option>
							</optgroup>
							<optgroup label="Mes clubs :">
								<?php
								
								foreach($clubs as $club)
								{
									echo '<option value="' . $club->ID . '"';
									
									if(isset($_GET['cl']) && $_GET['cl'] == $club->ID)
										echo ' selected';
									
									echo '>' . htmlspecialchars($club->nom) . '</option>';
								}
								?>
							</optgroup>
						</select>
					</div>
				<?php
				}
				?>
				
				<div class="form-group text-center center">
					<button type="submit" class="btn btn-custom">Rechercher <span class="glyphicon glyphicon-chevron-right"></span></button>
				</div>
			</form>
			
			<input type="hidden" id="default_s_lat" name="default_s_lat" value="<?php echo ObjetsMan::DEFAULT_MAP_LAT; ?>">
			<input type="hidden" id="default_s_lng" name="default_s_lng" value="<?php echo ObjetsMan::DEFAULT_MAP_LNG; ?>">
		</div>
		
		<?php
		$objets_manager = new ObjetsMan($this->bdd);
		$objets = $objets_manager->searchObjets($current_lat, $current_lng, $current_categorie, $current_sous_categorie, $current_club, $current_mot_cle);
		
		$nbr_resultats = 10;
		$page_number = !empty($_GET['p']) ? $_GET['p'] : 1;
		$nbr_pages = max(1, ceil(count($objets)/$nbr_resultats));
		
		if(empty($objets))
		{
			?>
				<div class="alert alert-warning text-center" role="alert">
					<strong><span class="glyphicon glyphicon-exclamation-sign"></span> Aucun résultat</strong>
					<br />
					<hr />
					Vous êtes en possession d'un bien qui correspond à ces critères ?
					<br />
					<a href="annonce.php" class="btn btn-custom"><span class="glyphicon glyphicon-edit"></span> Déposer une annonce</a>
				</div>
			<?php
		}
		else
		{
			for($i=$nbr_resultats*($page_number - 1); $i<min($nbr_resultats*$page_number, count($objets)); $i++)
			{
				$objet = $objets[$i];
				$proprio = $objets_manager->getProprio($objet->ID_proprio);
				?>
				<div id="div_content_objet_<?php echo $objet->ID; ?>">
					<div style="width: 100%; height: 122px; border-top: 2px solid rgb(230, 230, 230); position: relative; background-color: white;" class="ligne_objet" data-id="<?php echo $objet->ID; ?>">
						<div style="width: 120px; height: 150px; text-align: center; position: absolute;">
							<div style="position: relative; width: 100%; padding-bottom: 100%; margin: 0;">
								<div class="thumbnail" style="position:absolute; width:100%; height:100%; border: 0; background: url('<?php echo  $objet->getPhotoPrincipale(); ?>') no-repeat; background-position: center; background-size: cover; border-radius: 0;"></div>
							</div>
						</div>
						
						<div style="height: 100%; padding: 10px 10px 0 130px; position: relative;">
							<div style="position:relative; height: 100%;">
								<h4 style="font-size: 1.5em; font-weight: bold; margin: 0; padding-left: 5px; padding-bottom: 5px;"><a href="annonce.php?id=<?php echo $objet->ID; ?>"><?php echo htmlspecialchars($objet->nom); ?></a></h4>
								
								<p style="margin-left: 5px;">
									<?php
									$note = round($objets_manager->getNoteMean($objet->ID));
									
									if($note >= 0)
									{
										for($j=0; $j<5; $j++)
											echo ($j + 1 <= $note) ? '<span class="glyphicon glyphicon-star rose_custom"></span>' : '<span class="glyphicon glyphicon-star-empty rose_custom"></span>';
										
										echo '<br />';
									}
									?>
									<?php
									if(!empty($_GET['s_lat']) && !empty($_GET['s_lng']))
									{
										$dist = $objet->dist;
										
										if($dist < 5)
											echo '0 m';
										else if($dist < 1000)
											echo round($dist) . ' m';
										else
											echo number_format(round($dist / 1000), 0, ',', ' ') . ' km';
									}
									else
										echo '<span class="glyphicon glyphicon-map-marker"></span> ' . htmlspecialchars($proprio->locality) . '<small class="hidden-xs"> (' . $proprio->postal_code . ')</small>';
									?>
								</p>
								
								<p style="position: absolute; bottom: 0; left: 5px;">
									<a class="hidden-xs" href="membre.php?id=<?php echo $proprio->ID; ?>"><span class="glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($proprio->prenom) . ' ' . htmlspecialchars($proprio->nom); ?></a>
									<input type="hidden" name="lat_<?php echo $objet->ID; ?>" value="<?php echo $proprio->lat; ?>">
									<input type="hidden" name="lng_<?php echo $objet->ID; ?>" value="<?php echo $proprio->lng; ?>">
									<input type="hidden" id="proprio_objet_<?php echo $objet->ID; ?>" value="<?php echo $proprio->ID; ?>">
								</p>
							</div>
							
							<div style="position: absolute; top: <?php if($membre && ($membre->ID == 1 || $membre->ID == 2)) echo '70%'; else echo '50%'; ?>; right: 10px;" class="hidden-xs">
								<div style="position: relative; bottom: 20px;"
									<?php
									if($membre && ($membre->ID == 1 || $membre->ID == 2) && ($objet->prix_weekend || $objet->prix_semaine || $objet->prix_mois))
									{
										echo ' class="tooltip_keep_shown" data-trigger="manual data-toggle="tooltip" data-placement="top" data-container="#annonces" data-html="true" title="';
										
										if($objet->prix_weekend)
											echo number_format($objet->prix_weekend, 2, ',', ' ') . '&nbsp;€&nbsp;/&nbsp;weekend<br />';
										if($objet->prix_semaine)
											echo number_format($objet->prix_semaine, 2, ',', ' ') . '&nbsp;€&nbsp;/&nbsp;semaine<br />';
										if($objet->prix_mois)
											echo number_format($objet->prix_mois, 2, ',', ' ') . '&nbsp;€&nbsp;/&nbsp;mois';
										
										echo '"';
									}
									?>>
									<strong class="rose_custom" style="font-size: 1.4em;"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> € </strong>/ jour
									
								</div>
							</div>
							
							<div style="position: absolute; bottom: -10px; right: 10px;" class="visible-xs">
								<div style="position: relative; bottom: 20px;">
									<strong class="rose_custom" style="font-size: 1.4em;"><?php echo number_format($objet->prix_journee, 2, ',', ' '); ?> € </strong>/ jour
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}
		?>
		
		<div class="row" style="margin: 0; background-color: white;">
			<div style="width: 100%;">
				<div class="text-center">
					<ul class="pagination">
						<?php
						$url = 'recherche.php?';
						$gets = array('c', 'localisation', 's_lat', 's_lng', 'sc', 'cl', 'mc');
						$tab = array();
						
						foreach($gets as $get)
						{
							if(isset($_GET[$get]))
								$tab[] = $get . '=' . urlencode($_GET[$get]);
						}
						
						if(!empty($tab))
							$url .= implode('&amp;', $tab) . '&amp;';
						?>
						<li class="hidden-xs hidden-sm<?php if($page_number == 1) echo ' disabled'; ?>"><a href="<?php echo $url; ?>p=1" aria-label="Début"><span aria-hidden="true">&laquo;</span></a></li>
						<li class="hidden-xs<?php if($page_number == 1) echo ' disabled'; ?>"><a href="<?php echo $url; ?>p=<?php echo max(1, $page_number-1); ?>" aria-label="Précédant"><span aria-hidden="true">&lsaquo;</span></a></li>
						<?php
						$displayed_pages = array(1);
						
						if(end($displayed_pages) < $page_number - 1 && $page_number - 1 <= $nbr_pages)
							$displayed_pages[] = $page_number - 1;
						
						if(end($displayed_pages) < $page_number && $page_number <= $nbr_pages)
							$displayed_pages[] = $page_number;
						
						if(end($displayed_pages) < $page_number + 1 && $page_number + 1 <= $nbr_pages)
							$displayed_pages[] = $page_number + 1;
						
						if(end($displayed_pages) < $nbr_pages)
							$displayed_pages[] = $nbr_pages;
						
						foreach($displayed_pages as $key => $displayed_page)
						{
							if($key && $displayed_pages[$key - 1] < $displayed_page - 1)
								echo '<li class="disabled"><a>...</a></li>';
							
							echo '<li';
							
							if($displayed_page == $page_number)
								echo ' class="active"';
							
							echo '><a href="' . $url . 'p=' . $displayed_page . '">' . $displayed_page . '</a></li>';
						}
						?>
						<li class="hidden-xs<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="<?php echo $url; ?>p=<?php echo min($nbr_pages, $page_number+1); ?>" aria-label="Suivant"><span aria-hidden="true">&rsaquo;</span></a></li>
						<li class="hidden-xs hidden-sm<?php if($page_number == $nbr_pages) echo ' disabled'; ?>"><a href="<?php echo $url; ?>p=<?php echo $nbr_pages; ?>" aria-label="Fin"><span aria-hidden="true">&raquo;</span></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-8 col-sm-6 hidden-xs" style="height: 100%; padding: 0;" id="map"></div>
	<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp&amp;"></script>
</div>