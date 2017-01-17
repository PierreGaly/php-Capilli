<?php
$categorie = $infos['categorie'];
?>
<div class="row">
	<div class="row_content">
		<div class="row" style="text-align: center; margin-top: 20px; padding: 2px;">

			<!--p class="text-center">
				<a class="btn btn-custom" href="recherche.php?c=<?php echo $categorie->ID; ?>">Afficher toutes les annonces dans « <em><?php echo htmlspecialchars($categorie->nom); ?></em> » <span class="glyphicon glyphicon-chevron-right"></span></a>
			</p>

			<hr /-->

			<h2>Affiner la recherche</h2>

			<form method="get" action="recherche.php" class="form-inline text-center">
				<div class="row">
					<div class="form-group input-group-lg" style="font-size: 0;">
						<div class="form-group">
							<input autofocus autocomplete="off" type="text" class="form-control input-lg" style="box-shadow: 0 0 10px rgb(220, 220, 220);" name="mc" id="mc_index" placeholder="Quoi ?">
						</div>

						<div class="form-group has-feedback">
							<input type="text" class="form-control input-lg" style="border-radius: 0; box-shadow: 0 0 10px rgb(220, 220, 220);" name="localisation" id="localisation" placeholder="Où ?">
							<span class="glyphicon glyphicon-map-marker form-control-feedback" style="font-size: 15px;"></span>
							<input type="hidden" id="s_lat" name="s_lat" value="">
							<input type="hidden" id="s_lng" name="s_lng" value="">
						</div>

						<div class="form-group">
							<input type="hidden" name="c" value="<?php echo $categorie->ID; ?>">
							<button class="form-control input-lg btn-lg btn btn-custom" style="box-shadow: 0 0 10px rgb(220, 220, 220);" id="submit_index"><span class="glyphicon glyphicon-search"></span> Trouver</button>
						</div>
					</div>
				</div>
			</form>

			<script src="//maps.googleapis.com/maps/api/js?key=<?php echo API_KEY_MAP; ?>&amp;libraries=places&amp;language=fr&amp;v=3.exp&amp;"></script>

			<br />

			<?php
			$sous_categories_manager = new SousCategoriesMan($bdd);
			$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($categorie->ID);

			foreach($sous_categories as $sous_categorie)
			{
				?>
					<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" style="display: inline-block; float: none; margin: -2px;">
						<div class="thumbnail">
							<div class="caption text-center" style="display: inline-block;">
								<h4 style="display: table-cell; vertical-align: middle; height: 40px; overflow: hidden; font-weight: 1.2em;"><?php echo htmlspecialchars($sous_categorie->nom); ?></h4>
							</div>

							<div style="position: relative; width: 100%; padding-bottom: 100%;">
								<a href="recherche.php?c=<?php echo $categorie->ID; ?>&amp;sc=<?php echo $sous_categorie->ID; ?>" class="thumbnail" style="position:absolute; width:100%; height:100%;">
									<img style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto; max-width: 100%; max-height: 100%;" src="<?php echo IMAGES_SOUS_CAT . $sous_categorie->image; ?>" alt="Location de <?php echo htmlspecialchars($sous_categorie->nom); ?>">
								</a>
							</div>
						</div>
					</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
