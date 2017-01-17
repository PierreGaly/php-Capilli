<?php

require_once('session.php');

foreach($_GET as $key => $value)
{
	$documentation_manager = new DocumentationMan($bdd);
	$section = $documentation_manager->getSectionByUrl($key);
	
	if(!empty($section))
	{
		if($membre && $membre->edit_doc)
		{
			$sous_sections = $documentation_manager->getSousSectionsBySection($section->ID);
			
			foreach($sous_sections as $sous_section)
			{
				if(isset($_POST['edit_' . $sous_section->url]))
				{
					$documentation_manager->updateSousSection($sous_section->ID, $_POST['edit_' . $sous_section->url]);
					redirect('documentation.php?' . $section->url . '#' . $sous_section->url);
				}
			}
		}
		
		new Page('documentation_section', $membre, $bdd, array('section' => $section), $section->titre);
		exit(0);
	}
}

new Page('documentation', $membre, $bdd);
