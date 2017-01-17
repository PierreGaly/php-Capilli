<?php

require_once('session.php');

if(isset($_GET['p']))
{
	$communautes_manager = new CommunautesMan($bdd);
	$proposition = $communautes_manager->getPropositionByID($_GET['p']);
}

if($membre)
{
	if($membre->administrateur && !empty($proposition))
	{
		$result = false;
		
		if(isset($_POST['communaute_nom']) && isset($_POST['communaute_description']) && isset($_POST['communaute_image']) && isset($_FILES['communaute_image_new']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			$result = $communautes_manager->addCommunaute($proposition, $_POST['communaute_nom'], $_POST['communaute_description'], $_POST['communaute_image'], $_FILES['communaute_image_new']);
			
			if(!is_array($result))
			{
				$_SESSION['communaute_created'] = true;
				redirect('club.php?id=' . $result);
			}
		}
		
		new Page('creer_club', $membre, $bdd, array('communaute_proposition' => $proposition, 'erreurs' => $result));
	}
	else
		new Page('page_incorrecte', $membre, $bdd);
	
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));
