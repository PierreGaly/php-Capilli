<?php

require_once('session.php');

if(isset($_GET['id']) && isset($_GET['h']))
{
	$membres_manager = new MembresMan($bdd);
	$res_validation = $membres_manager->valider_email($_GET['id'], $_GET['h']);
	
	if(!empty($res_validation))
	{
		$membres_manager->connect($res_validation->email, $res_validation->mdp, true);
		$_SESSION['membre_just_validated_email'] = true;
		redirect('perso.php?dashboard');
	}
	else
		redirect('connexion.php?v&h');
}

if($membre && (isset($_GET['dashboard']) || isset($_GET['clubs']) || isset($_GET['messages']) || isset($_GET['demandes_de_location']) || isset($_GET['annonces']) || isset($_GET['revenus']) || isset($_GET['compte']) || isset($_GET['delAnnonce'])))
{
	$conversation = false;
	$messages = false;
	$erreurs = array();
	
	// dashboard
	if(isset($_POST['da_photo_remove']))
	{
		$membres_manager = new MembresMan($bdd);
		$membres_manager->removeAvatar($membre);
		$_SESSION['membre'] = $membres_manager->getMembreByID($membre->ID);
		$_SESSION['da_photo_removed'] = true;
		
		redirect('perso.php?dashboard');
	}
	
	if(!empty($_FILES['da_photo']))
	{
		$membres_manager = new MembresMan($bdd);
		$res = $membres_manager->modifyAvatar($membre, $_FILES['da_photo']);
		
		if($res === false)
		{
			$_SESSION['membre'] = $membres_manager->getMembreByID($membre->ID);
			$_SESSION['da_photo_modified'] = true;
		}
		else
			$_SESSION['da_photo_error'] = $res;
		
		redirect('perso.php?dashboard');
	}
	
	// annonce
	if(isset($_GET['delAnnonce']) && isset($_GET['id']))
	{
		$objets_manager = new ObjetsMan($bdd);
		$objets_manager->delObjet($membre, $_GET['id']);
		redirect('perso.php?annonces&amp;succes');//succes sert à afficher qu'on a bien supprimé l'annonce mais bon je sais pas trop comment tu fais ça d'habitude...
	}
	
	// messages
	if(isset($_POST['message']) && isset($_GET['c']))
	{
		$messagerie_manager = new MessagerieMan($bdd);
		$messagerie_manager->addMessage($_GET['c'], $membre->ID, $_POST['message']);
		redirect('perso.php?messages');
	}
	
	if(isset($_GET['messages']) && isset($_POST['del_participant']))
	{
		$messagerie_manager = new MessagerieMan($bdd);
		$messagerie_manager->delParticipant($_POST['del_participant'], $membre->ID);
		$_SESSION['perso_message_del_participant'] = true;
		redirect('perso.php?messages');
	}
	
	if(!empty($_POST['new_message_participants']) && !empty($_GET['c']))
	{
		$messagerie_manager = new MessagerieMan($bdd);
		
		foreach($_POST['new_message_participants'] as $ID_destinataire)
			$messagerie_manager->addParticipant($_GET['c'], $ID_destinataire);
		
		redirect('perso.php?messages');
	}
	
	if(!empty($_POST['new_message_destinataires']) && isset($_POST['new_message_objet']) && isset($_POST['new_message_message']))
	{
		$messagerie_manager = new MessagerieMan($bdd);
		
		$ID_conversation = $messagerie_manager->addConversation($_POST['new_message_objet']);
		$messagerie_manager->addParticipant($ID_conversation, $membre->ID);
		
		foreach($_POST['new_message_destinataires'] as $ID_destinataire)
			$messagerie_manager->addParticipant($ID_conversation, $ID_destinataire);
		
		$messagerie_manager->addMessage($ID_conversation, $membre->ID, $_POST['new_message_message']);
		redirect('perso.php?messages&c=' . $ID_conversation);
	}
	
	// revenus
	if(isset($_POST['revenus_vider_tirelire']))
	{
		$paiements_manager = new PaiementsMan($bdd);
		$comptes_bancaires_manager = new ComptesBancairesMan($bdd);
		
		$compte_bancaire = $comptes_bancaires_manager->getByMembre($membre->ID);
		
		if($compte_bancaire != null)
		{
			if($result = $paiements_manager->retirer($membre->ID, $compte_bancaire->ID, number_format($paiements_manager->getTotalPaiements($membre->ID), 2, ',', ' ')))
			{
				if(is_array($result))
					$_SESSION['paiement_montant_lemon'] = $result ;
				else
					$_SESSION['paiement_montant_retire'] = $result;
				
				redirect('perso.php?revenus');
			}
		}
		else
			redirect('perso.php?compte#infos_bancaires');
	}
	
	// compte
	if(isset($_POST['compte_update_email']) && isset($_POST['compte_email']))
	{
		$membres_manager = new MembresMan($bdd);
		$erreurs = $membres_manager->updateEmail($membre, $_POST['compte_email']);
		
		if($erreurs === true)
		{
			$_SESSION['compte_email'] = true;
			redirect('perso.php?compte#ip');
		}
	}
	else if(isset($_POST['compte_update_adresse']) && isset($_POST['compte_adresse_complete']) && isset($_POST['compte_street_number']) && isset($_POST['compte_route']) && isset($_POST['compte_locality']) && isset($_POST['compte_administrative_area_level_1']) && isset($_POST['compte_country']) && isset($_POST['compte_postal_code']) && isset($_POST['compte_lat']) && isset($_POST['compte_lng']))
	{
		$membres_manager = new MembresMan($bdd);
		$erreurs = $membres_manager->updateAdresse($membre, $_POST['compte_adresse_complete'], $_POST['compte_street_number'], $_POST['compte_route'], $_POST['compte_locality'], $_POST['compte_administrative_area_level_1'], $_POST['compte_country'], $_POST['compte_postal_code'], $_POST['compte_lat'], $_POST['compte_lng']);
		
		if($erreurs === true)
		{
			$_SESSION['compte_adresse'] = true;
			redirect('perso.php?compte#ip');
		}
	}
	else if(isset($_POST['compte_update_tel_fixe']) && isset($_POST['compte_tel_fixe']))
	{
		$membres_manager = new MembresMan($bdd);
		$erreurs = $membres_manager->updateTel_fixe($membre, $_POST['compte_tel_fixe']);
		
		if($erreurs === true)
		{
			$_SESSION['compte_tel_fixe'] = true;
			redirect('perso.php?compte#ip');
		}
		
	}
	else if(isset($_POST['compte_update_tel_portable']) && isset($_POST['compte_tel_portable']))
	{
		$membres_manager = new MembresMan($bdd);
		$erreurs = $membres_manager->updateTel_portable($membre, $_POST['compte_tel_portable']);
		
		if($erreurs === true)
		{
			$_SESSION['compte_tel_portable'] = true;
			redirect('perso.php?compte#ip');
		}
	}
	else if(isset($_POST['compte_bancaire_mdp_verif']) && isset($_POST['compte_add_compte_bancaire']) && isset($_POST['compte_titulaire']) && isset($_POST['compte_iban']) && isset($_POST['compte_bic']) && isset($_POST['compte_nom_agence']) && isset($_POST['compte_rue_agence']))
	{
		if($membre->mdp == sha1($_POST['compte_bancaire_mdp_verif']))
		{
			$comptes_bancaires_manager = new ComptesBancairesMan($bdd);
			$erreurs = $comptes_bancaires_manager->add($membre->ID, $_POST['compte_titulaire'], $_POST['compte_iban'], $_POST['compte_bic'], $_POST['compte_nom_agence'], $_POST['compte_rue_agence']);
			
			if($erreurs === true)
			{
				$_SESSION['compte_compte_bancaire_added'] = true;
				redirect('perso.php?compte#cb');
			}
		}
		else
		{
			$_SESSION['compte_compte_bancaire_wrong_password'] = true;
			redirect('perso.php?compte#cb');
		}
	}
	else if(isset($_POST['compte_bancaire_mdp_verif']) && isset($_POST['compte_del_compte_bancaire']))
	{
		if($membre->mdp == sha1($_POST['compte_bancaire_mdp_verif']))
		{
			$comptes_bancaires_manager = new ComptesBancairesMan($bdd);
			$erreurs = $comptes_bancaires_manager->del($membre->ID);
			
			if($erreurs === true)
			{
				$_SESSION['compte_compte_bancaire_deleted'] = true;
				redirect('perso.php?compte#cb');
			}
		}
		else
		{
			$_SESSION['compte_compte_bancaire_wrong_password'] = true;
			redirect('perso.php?compte#cb');
		}
	}
	else if(isset($_POST['compte_update_mdp']) && isset($_POST['compte_mdp']) && isset($_POST['compte_mdp2']) && isset($_POST['compte_mdp_verif']))
	{
		$membres_manager = new MembresMan($bdd);
		$erreurs = $membres_manager->updateMdp($membre, $_POST['compte_mdp'], $_POST['compte_mdp2'], $_POST['compte_mdp_verif']);
		
		if($erreurs === true)
		{
			$_SESSION['compte_mdp'] = true;
			redirect('perso.php?compte#mdp');
		}
	}
	else if(isset($_POST['compte_desinscription_submit']) && isset($_POST['compte_desinscription_mdp']))
	{
		if($membre->mdp == sha1($_POST['compte_desinscription_mdp']))
		{
			$paiements_manager = new PaiementsMan($bdd);
			$objets_manager = new ObjetsMan($bdd);
			$versements_reels_manager = new Versements_reelsMan($bdd);
			$erreur = false;
			$montant = $paiements_manager->getTotalPaiements($membre->ID);
			
			foreach($objets_manager->getByProprio($membre->ID, 2) as $objet)
			{
				if($objets_manager->delObjet($membre, $objet->ID))
					$erreur = true;
			}
			
			if($erreur)
				$_SESSION['desinscription_erreur_objets'] = true;
			else if($montant != 0)
				$_SESSION['desinscription_erreur_tirelire'] = $montant;
			else if($versements_reels_manager->hasVersementsEnAttenteByMembre($membre->ID))
				$_SESSION['desinscription_erreur_versements_reels_attente'] = true;
			else
			{
				$membres_manager = new MembresMan($bdd);
				$lemon = $membres_manager->supprimerCompte($membre->ID);
				
				if(!empty($lemon))
					$_SESSION['desinscription_erreur_lemon'] = $lemon;
				else
				{
					$_SESSION['desinscription_valide'] = true;
					redirect('deconnexion.php');
				}
			}
			
			redirect('perso.php?compte#di');
		}
	}
	
	if(!empty($_GET['c']))
	{
		$messagerie_manager = new MessagerieMan($bdd);
		$conversation = $messagerie_manager->getConversation($_GET['c'], $membre->ID);
	}
	
	if($conversation)
		$messages = $messagerie_manager->getMessages($conversation->ID, $membre->ID);
	
	if(isset($_GET['dashboard']))
	{
		$parrainages_manager = new ParrainagesMan($bdd);
		$parrainages_non_vus = $parrainages_manager->getParrainagesNonVus($membre->ID);
	}
	else
		$parrainages_non_vus = array();
	
	new Page('perso', $membre, $bdd, array('erreurs' => $erreurs, 'conversation' => $conversation, 'messages' => $messages, 'parrainages_non_vus' => $parrainages_non_vus));
}
else if(!$membre)
	new Page('connexion', $membre, $bdd, array('need_connec' => true));
else
	new Page('page_incorrecte', $membre, $bdd);