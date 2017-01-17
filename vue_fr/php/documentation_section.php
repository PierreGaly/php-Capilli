<?php
$documentation_manager = new DocumentationMan($bdd);
$section = $infos['section'];
$sous_sections = $documentation_manager->getSousSectionsBySection($section->ID);
?><div class="row">
	<div class="row_content">
		<h2><ol class="breadcrumb">
			<li><a href="documentation.php">Documentation</a></li>
			<li class="active"><?php echo htmlspecialchars($section->titre); ?></li>
		</ol>
		</h2>
		
		<form method="post" action="" class="form-horizontal">
			<?php
			$documentation_manager = new DocumentationMan($bdd);
			$sections = $documentation_manager->getSections();
			
			foreach($sous_sections as $sous_section)
			{
			?>
				<div class="panel panel-default" style="margin-bottom: 0; border-radius: 0;">
					<div class="panel-heading" style="font-size: 1.3em;">
						<?php echo htmlspecialchars($sous_section->titre); ?>
						<a href="" class="pull-right bouton_slide_toggle" data-url="<?php echo $sous_section->url; ?>"><span class="glyphicon glyphicon-plus"></span></a>
						<?php if($membre && $membre->edit_doc) echo '<a class="btn btn-primary" href="documentation.php?' . $section->url . '&amp;edit=' . $sous_section->url . '#' . $sous_section->url . '"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>'; ?>
					</div>
					
					<div class="list-group clearfix" id="<?php echo $sous_section->url; ?>" style="display: none; padding: 10px; text-align: justify;">
						<?php
						if($membre && $membre->edit_doc && isset($_GET['edit']) && $_GET['edit'] == $sous_section->url)
						{
						?>
							<textarea class="form-control" name="edit_<?php echo $sous_section->url; ?>" style="min-height: 100%;"><?php echo htmlspecialchars($sous_section->texte); ?></textarea>
							<br />
							<button type="reset" role="reset" class="btn btn-default center-block"><span class="glyphicon glyphicon-floppy-remove"></span> Réinitialiser le champ</button>
							<br />
							<button type="submit" role="submit" class="btn btn-custom center-block">Modifier le champ <span class="glyphicon glyphicon-chevron-right"></span></button>
						<?php
						}
						else
							echo $sous_section->texte;
						?>
					</div>
				</div>
			<?php
			}
			?>
		</form>
	</div>
</div>