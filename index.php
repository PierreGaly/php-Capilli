<?php

require_once('session.php');

if(!empty($_GET['p']))
{
	$membres_manager = new MembresMan($bdd);
	$membre_parrainage = $membres_manager->getMembreByID($_GET['p']);
	
	if(!empty($membre_parrainage))
	{
		
		if(!$membre)
		{
			$_SESSION['inscription_parrain'] = $membre_parrainage;
			$_SESSION['parrainage_valide'] = $membre_parrainage;
		}
		else
		{
			$_SESSION['parrainage_invalide_inscrit'] = $membre_parrainage;
		}
	}
	else
		$_SESSION['parrainage_invalide_existe'] = true;
	
	redirect('/');
}

new Page('index', $membre, $bdd);
