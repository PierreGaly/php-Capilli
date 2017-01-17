<?php

require_once('session.php');

if(!$membre)
{
	if(isset($_POST['oubli_mdp_email']))
	{
		$membres_manager = new MembresMan($bdd);
		$res = $membres_manager->oubli_mdp($_POST['oubli_mdp_email']);
		
		if($res !== false)
		{
			$_SESSION['mdp_sent'] = $res->email;
			redirect();
		}
	}
	
	new Page('oubli_mdp', $membre, $bdd);
}
else
	new Page('page_incorrecte', $membre, $bdd);