<?php

require_once('session.php');

if(!$membre)
{
	if(isset($_GET['v']))
	{
		if(isset($_POST['email_connec3']))
		{
			$membres_manager = new MembresMan($bdd);
			$membres_manager->renvoyerMailInscription($_POST['email_connec3']);
			
			$_SESSION['mail_inscription_sent'] = $_POST['email_connec3'];
			redirect('connexion.php?v=' . urlencode($_POST['email_connec3']));
		}
		
		new Page('connexion_email_invalide', $membre, $bdd);
	}
	else
		new Page('connexion', $membre, $bdd);
}
else
	new Page('page_incorrecte', $membre, $bdd);