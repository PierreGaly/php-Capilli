<?php

require_once('session.php');

if(isset($_POST['id_club_adhesion']))
{
	if($membre)
	{
		$communautes_manager = new CommunautesMan($bdd);
		$_SESSION['club_adhesion'] = $communautes_manager->addMembre($_POST['id_club_adhesion'], $membre->ID);
		redirect('clubs.php');
	}
	else
		new Page('connexion', $membre, $bdd, array('need_connec' => true));
}

$demande_club = false;
$is_membre_demande_club = false;

if(isset($_GET['id']))
{
	$communautes_manager = new CommunautesMan($bdd);
	$demande_club = $communautes_manager->getCommunauteByID($_GET['id']);
}

if(!empty($demande_club))
{
	if(!$membre)
	{
		new Page('connexion', $membre, $bdd, array('need_connec' => true));
		exit(0);
	}
	else
	{
		$is_membre_demande_club = $communautes_manager->isMemberOrPendingMember($demande_club->ID, $membre->ID);
		
		if($is_membre_demande_club == 2)
			redirect('club.php?id=' . $demande_club->ID);
	}
}

new Page('clubs', $membre, $bdd, array('demande_club' => $demande_club, 'is_membre_demande_club' => $is_membre_demande_club));
