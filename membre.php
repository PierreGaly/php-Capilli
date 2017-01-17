<?php

require_once('session.php');

if(!empty($_GET['id']))
{
	$membre_manager = new MembresMan($bdd);
	$membre_apercu = $membre_manager->getMembreByID($_GET['id']);
}

if(!empty($membre_apercu) && $membre_apercu->actif == 1)
{
	if($membre && $membre->administrateur && isset($_POST['membre_apercu_valider_email']))
	{
		$membre_manager->valider_email($membre_apercu->ID, $membre_manager->hasher($membre_apercu->ID, $membre_apercu->email, $membre_apercu->date_inscription));
		redirect('membre.php?id=' . $membre_apercu->ID);
	}
	
	new Page('membre_apercu', $membre, $bdd, array('membre_apercu' => $membre_apercu), $membre_apercu->prenom . ' ' . $membre_apercu->nom);
}
else if(!isset($_GET['id']) && $membre && $membre->administrateur)
	new Page('membre_liste', $membre, $bdd);
else
	new Page('membre_apercu_invalide', $membre, $bdd);