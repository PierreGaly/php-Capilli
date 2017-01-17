<?php

require_once('session.php');

if($membre)
{
	if(!empty($_POST['li_description']))
	{
		$litiges_manager = new LitigesMan($bdd);
		$litiges_manager->add($membre->ID, !empty($_POST['li_reservation']) ? $_POST['li_reservation'] : -1, $_POST['li_description']);
		
		$_SESSION['litige_declared'] = true;
		
		redirect();
	}
	
	new Page('litige', $membre, $bdd);
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));
