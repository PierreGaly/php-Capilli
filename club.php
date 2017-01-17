<?php

require_once('session.php');

if(isset($_GET['id']))
{
	$communautes_manager = new CommunautesMan($bdd);
	$communaute = $communautes_manager->getCommunauteByID($_GET['id']);
}

if(!empty($communaute))
{
	if($membre && ($membre->administrateur || $communautes_manager->isMembre($communaute->ID, $membre->ID)) && isset($_POST['id_membre_accept']))
	{
		$_SESSION['demande_accepted'] = $communautes_manager->accept($communaute->ID, $_POST['id_membre_accept']);
		redirect('club.php?id=' . $communaute->ID . '&demandes');
	}
	
	if(!empty($_POST['message']))
	{
		$communautes_manager->addMessage($communaute->ID, $membre->ID, $_POST['message']);
		redirect('club.php?id=' . $communaute->ID . '&forum');
	}
	
	$isNotif = false;
	
	if($membre && $communautes_manager->isNotifClubByMembre($membre->ID, $communaute->ID))
	{
		$isNotif = true;
		$communautes_manager->delNotifClubByMembre($membre->ID, $communaute->ID);
	}
	
	new Page('club', $membre, $bdd, array('club' => $communaute, 'isNotif' => $isNotif), $communaute->nom);
}
else if($membre)
{
	$result = false;
	
	if(isset($_POST['communaute_nom']) && isset($_POST['communaute_description']) && isset($_FILES['communaute_image']))
	{
		$communautes_manager = new CommunautesMan($bdd);
		$result = $communautes_manager->proposerCommunaute($membre->ID, $_POST['communaute_nom'], $_POST['communaute_description'], $_FILES['communaute_image']);
		
		if(!is_array($result))
		{
			$_SESSION['communaute_proposed'] = $_POST['communaute_nom'];
			redirect('club.php');
		}
	}
	
	new Page('club_proposer', $membre, $bdd, array('erreurs' => $result));
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));
