<div class="row">
	<div class="row_content">
		<h2>Documentation officielle de <?php echo SITE_NOM; ?></h2>
		
		<?php
		$documentation_manager = new DocumentationMan($bdd);
		$sections = $documentation_manager->getSections();
		
		foreach($sections as $section)
		{
		?>
			<div class="panel panel-default" style="margin-bottom: 0; border-radius: 0;">
				<div class="panel-heading" style="font-size: 1.3em;">
					<a href="documentation.php?<?php echo $section->url; ?>"><?php echo htmlspecialchars($section->titre); ?></a>
					<a href="" class="pull-right bouton_slide_toggle" data-url="<?php echo $section->url; ?>"><span class="glyphicon glyphicon-plus"></span></a>
				</div>
				
				<ul class="list-group" id="<?php echo $section->url; ?>" style="display: none;">
					<?php
					$sous_sections = $documentation_manager->getSousSectionsBySection($section->ID);
					
					foreach($sous_sections as $sous_section)
						echo '<a class="list-group-item" href="documentation.php?' . $section->url . '#' . $sous_section->url . '">' . htmlspecialchars($sous_section->titre) . '</a>';
					?>
				</ul>
			</div>
		<?php
		}
		?>
		
		<div class="panel panel-default" style="border-radius: 0;">
			<div class="panel-heading" style="font-size: 1.3em;">
				Restons en contact !
				<a href="" class="pull-right bouton_slide_toggle" data-url="contact"><span class="glyphicon glyphicon-plus"></span></a>
			</div>
			
			<div class="alert alert-info text-center" role="alert">N'hésitez pas à nous contacter pour devenir partenaire de <?php echo SITE_NOM; ?>.</div>
			
			<ul class="list-group" id="contact" style="display: none;">
				<div class="panel-body">
					<?php Page::getFormulaireContact(); ?>
				</div>
				<a class="list-group-item" href="<?php echo LINK_FACEBOOK; ?>" onclick="window.open(this.href); return false;">Retrouvez-nous sur <strong>Facebook</strong><span class="pull-right glyphicon glyphicon-new-window"></span></a>
				<a class="list-group-item" href="<?php echo LINK_TWITTER; ?>" onclick="window.open(this.href); return false;">Retrouvez-nous sur <strong>Twitter</strong><span class="pull-right glyphicon glyphicon-new-window"></span></a>
				<a class="list-group-item" href="<?php echo LINK_GOOGLE; ?>" onclick="window.open(this.href); return false;">Retrouvez-nous sur <strong>Google+</strong><span class="pull-right glyphicon glyphicon-new-window"></span></a>
			</ul>
		</div>
	</div>
</div>