<?php

require_once('session.php');

if(!$membre)
{
	if(isset($_GET['etudiant']))
	{
		$result = array();
		
		if(isset($_POST['inscription_civilite'])
			&& isset($_POST['inscription_email'])
			&& isset($_POST['inscription_prenom'])
			&& isset($_POST['inscription_nom'])
			&& isset($_POST['inscription_adresse_complete'])
			&& isset($_POST['inscription_street_number'])
			&& isset($_POST['inscription_route'])
			&& isset($_POST['inscription_locality'])
			&& isset($_POST['inscription_administrative_area_level_1'])
			&& isset($_POST['inscription_country'])
			&& isset($_POST['inscription_postal_code'])
			&& isset($_POST['inscription_lat'])
			&& isset($_POST['inscription_lng'])
			&& isset($_POST['inscription_tel_portable'])
			&& isset($_FILES['inscription_avatar'])
			&& isset($_POST['inscription_mdp'])
			&& isset($_POST['inscription_mdp2'])
			&& isset($_POST['inscription_date_naissance']))
		{
			$membres_manager = new MembresMan($bdd);
			$result = $membres_manager->inscription2(isset($_POST['inscription_charte']) ? true : false,
													'student',
													$_POST['inscription_civilite'] == 'femme' ? true : false,
													$_POST['inscription_email'],
													$_POST['inscription_prenom'],
													$_POST['inscription_nom'],
													$_POST['inscription_adresse_complete'],
													$_POST['inscription_street_number'],
													$_POST['inscription_route'],
													$_POST['inscription_locality'],
													$_POST['inscription_administrative_area_level_1'],
													$_POST['inscription_country'],
													$_POST['inscription_postal_code'],
													$_POST['inscription_lat'],
													$_POST['inscription_lng'],
													isset($_POST['inscription_ecole']) ? $_POST['inscription_ecole'] : -1,
													'',
													$_POST['inscription_tel_portable'],
													$_FILES['inscription_avatar'],
													$_POST['inscription_mdp'],
													$_POST['inscription_mdp2'],
													$_POST['inscription_date_naissance'],
													isset($_SESSION['inscription_parrain']) ? $_SESSION['inscription_parrain'] : false);
			
			if($result === true)
				redirect('/');
		}
		else
			$result = false;
			
		new Page('inscription_etudiant', $membre, $bdd, array('erreurs' => $result));
	}
	else if(isset($_GET['association']))
	{
		$result = array();
		
		if(isset($_POST['inscription_email'])
			&& isset($_POST['inscription_nom'])
			&& isset($_POST['inscription_adresse_complete'])
			&& isset($_POST['inscription_street_number'])
			&& isset($_POST['inscription_route'])
			&& isset($_POST['inscription_locality'])
			&& isset($_POST['inscription_administrative_area_level_1'])
			&& isset($_POST['inscription_country'])
			&& isset($_POST['inscription_postal_code'])
			&& isset($_POST['inscription_lat'])
			&& isset($_POST['inscription_lng'])
			&& isset($_POST['inscription_tel_fixe'])
			&& isset($_POST['inscription_tel_portable'])
			&& isset($_FILES['inscription_avatar'])
			&& isset($_POST['inscription_mdp'])
			&& isset($_POST['inscription_mdp2']))
		{
			$membres_manager = new MembresMan($bdd);
			$result = $membres_manager->inscription2(isset($_POST['inscription_charte']) ? true : false,
													'association',
													false,
													$_POST['inscription_email'],
													'Association',
													$_POST['inscription_nom'],
													$_POST['inscription_adresse_complete'],
													$_POST['inscription_street_number'],
													$_POST['inscription_route'],
													$_POST['inscription_locality'],
													$_POST['inscription_administrative_area_level_1'],
													$_POST['inscription_country'],
													$_POST['inscription_postal_code'],
													$_POST['inscription_lat'],
													$_POST['inscription_lng'],
													isset($_POST['inscription_ecole']) ? $_POST['inscription_ecole'] : -1,
													$_POST['inscription_tel_fixe'],
													$_POST['inscription_tel_portable'],
													$_FILES['inscription_avatar'],
													$_POST['inscription_mdp'],
													$_POST['inscription_mdp2'],
													'',
													isset($_SESSION['inscription_parrain']) ? $_SESSION['inscription_parrain'] : false);
			
			if($result === true)
			{
				$membres_manager->connect($_POST['inscription_email'], $_POST['inscription_mdp']);
				redirect('/');
			}
		}
		else
			$result = false;
			
		new Page('inscription_association', $membre, $bdd, array('erreurs' => $result));
	}
	else if(isset($_GET['professionnel']))
	{
		$result = array();
		
		if(isset($_POST['inscription_email'])
			&& isset($_POST['inscription_nom'])
			&& isset($_POST['inscription_adresse_complete'])
			&& isset($_POST['inscription_street_number'])
			&& isset($_POST['inscription_route'])
			&& isset($_POST['inscription_locality'])
			&& isset($_POST['inscription_administrative_area_level_1'])
			&& isset($_POST['inscription_country'])
			&& isset($_POST['inscription_postal_code'])
			&& isset($_POST['inscription_lat'])
			&& isset($_POST['inscription_lng'])
			&& isset($_POST['inscription_tel_fixe'])
			&& isset($_POST['inscription_tel_portable'])
			&& isset($_FILES['inscription_avatar'])
			&& isset($_POST['inscription_mdp'])
			&& isset($_POST['inscription_mdp2']))
		{
			$membres_manager = new MembresMan($bdd);
			$result = $membres_manager->inscription2(isset($_POST['inscription_charte']) ? true : false,
													'professionnel',
													false,
													$_POST['inscription_email'],
													'Professionnel',
													$_POST['inscription_nom'],
													$_POST['inscription_adresse_complete'],
													$_POST['inscription_street_number'],
													$_POST['inscription_route'],
													$_POST['inscription_locality'],
													$_POST['inscription_administrative_area_level_1'],
													$_POST['inscription_country'],
													$_POST['inscription_postal_code'],
													$_POST['inscription_lat'],
													$_POST['inscription_lng'],
													-1,
													$_POST['inscription_tel_fixe'],
													$_POST['inscription_tel_portable'],
													$_FILES['inscription_avatar'],
													$_POST['inscription_mdp'],
													$_POST['inscription_mdp2'],
													'',
													isset($_SESSION['inscription_parrain']) ? $_SESSION['inscription_parrain'] : false);
			
			if($result === true)
			{
				$membres_manager->connect($_POST['inscription_email'], $_POST['inscription_mdp']);
				redirect('/');
			}
		}
		else
			$result = false;
			
		new Page('inscription_professionnel', $membre, $bdd, array('erreurs' => $result));
	}
	else
		new Page('inscription_liste', $membre, $bdd);
}
else if(isset($just_connected))
	redirect('/');
else
	new Page('page_incorrecte', $membre, $bdd);