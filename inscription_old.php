<?php

require_once('session.php');

if(!$membre)
{
	if(INSCRIPTION_CLOSED)
	{
		new Page('inscription_closed', $membre, $bdd);
		exit(0);
	}
	
	$result = array();
	
	if(isset($_POST['inscription_type'])
		&& isset($_POST['inscription_civilite'])
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
		&& isset($_POST['inscription_tel_fixe'])
		&& isset($_POST['inscription_tel_portable'])
		&& isset($_FILES['inscription_avatar'])
		&& isset($_POST['inscription_mdp'])
		&& isset($_POST['inscription_mdp2'])
		&& isset($_POST['inscription_date_naissance'])
		&& isset($_POST['inscription_source']))
	{
		$membres_manager = new MembresMan($bdd);
		$result = $membres_manager->inscription(isset($_POST['inscription_charte']) ? true : false,
												$_POST['inscription_type'] == 'professionnel' ? true : false,
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
												$_POST['inscription_tel_fixe'],
												$_POST['inscription_tel_portable'],
												$_FILES['inscription_avatar'],
												$_POST['inscription_mdp'],
												$_POST['inscription_mdp2'],
												$_POST['inscription_date_naissance'],
												$_POST['inscription_source'],
												isset($_SESSION['inscription_parrain']) ? $_SESSION['inscription_parrain'] : false);
		
		if($result === true)
		{
			if($_POST['inscription_type'] == 'professionnel')
				$membres_manager->connect($_POST['inscription_email'], $_POST['inscription_mdp']);
			
			redirect('/');
		}
	}
	else
		$result = false;
	
	new Page('inscription', $membre, $bdd, array('erreurs' => $result));
}
else if(isset($just_connected))
	redirect('/');
else
	new Page('page_incorrecte', $membre, $bdd);