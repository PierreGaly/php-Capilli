<?php

require_once('session.php');

/*
$membres_manager = new MembresMan($bdd);
require_once($_SESSION['dossier_vue'] . '/php/MailInscription.class.php');
new MailInscription($membres_manager->getMembreByID(51));
*/

if($membre)
{
	if($membre->administrateur)
	{
		$erreurs = array();
		
		if(false && isset($_POST['administration_invalider_versement']) && isset($_POST['administration_invalider_versement_ID']))
		{
			$versements_reel_manager = new Versements_reelsMan($bdd);
			$result = $versements_reel_manager->invalider($_POST['administration_invalider_versement_ID']);
			
			if($result == 'versement_already_invalide')
				$_SESSION['administration_versement_already_invalide'] = (int) $_POST['administration_invalider_versement_ID'];
			else if($result == 'versement_invalide_not_exists')
				$_SESSION['administration_versement_invalide_not_exists'] = (int) $_POST['administration_invalider_versement_ID'];
			else if($result == false)
				$_SESSION['administration_versement_invalide'] = (int) $_POST['administration_invalider_versement_ID'];
			
			redirect();
		}
		
		if(isset($_POST['administration_versement_reel_valider']) && isset($_POST['administration_versement_reel_ID']))
		{
			$versements_reel_manager = new Versements_reelsMan($bdd);
			$result = $versements_reel_manager->valider($_POST['administration_versement_reel_ID']);
			
			if($result == 'versement_already_valide')
				$_SESSION['administration_versement_already_valide'] = (int) $_POST['administration_versement_reel_ID'];
			else if($result == 'versement_valide_not_exists')
				$_SESSION['administration_versement_valide_not_exists'] = (int) $_POST['administration_versement_reel_ID'];
			else if(is_array($result))
				$_SESSION['administration_versement_valide_lemon'] = $result;
			else if($result == false)
				$_SESSION['administration_versement_valide'] = (int) $_POST['administration_versement_reel_ID'];
			
			redirect();
		}
		
		if(isset($_POST['administration_versement_reel_actualiser']) && isset($_POST['administration_versement_reel_ID']))
		{
			$versements_reel_manager = new Versements_reelsMan($bdd);
			$versement_reel = $versements_reel_manager->getByID($_POST['administration_versement_reel_ID']);
			
			if($versement_reel != null)
			{
				$result = $versements_reel_manager->actualiserCompteBancaire($versement_reel);
				
				if($result == 'compte_bancaire_not_exists')
					$_SESSION['administration_compte_bancaire_not_exists'] = (int) $_POST['administration_versement_reel_ID'];
				else if($result == 'compte_bancaire_same')
					$_SESSION['administration_compte_bancaire_same'] = (int) $_POST['administration_versement_reel_ID'];
				else if($result == false)
					$_SESSION['administration_compte_bancaire_updated'] = (int) $_POST['administration_versement_reel_ID'];
			}
			
			redirect();
		}
		
		if(isset($_POST['submit_litiges']))
		{
			$litiges_manager = new LitigesMan($bdd);
			$nbr_litiges_traites = 0;
			
			foreach($_POST as $key => $value)
			{
				if(substr($key, 0, 15) == 'litige_traiter_' && is_numeric(substr($key, 15)))
				{
					$litiges_manager->traiterLitige(substr($key, 15));
					$nbr_litiges_traites++;
				}
			}
			
			$_SESSION['administration_litiges_traites'] = $nbr_litiges_traites;
			$erreurs = true;
		}
		
		if(isset($_POST['submit_paiements']))
		{
			$transactions_manager = new TransactionsMan($bdd);
			$nbr_paiements_annules = 0;
			
			foreach($_POST as $key => $value)
			{
				if(substr($key, 0, 17) == 'paiement_annuler_' && is_numeric(substr($key, 17)))
				{
					$transactions_manager->annulerPaiement(substr($key, 17));
					$nbr_paiements_annules++;
				}
			}
			
			$_SESSION['administration_paiements_annules'] = $nbr_paiements_annules;
			$erreurs = true;
		}
		
		if(!empty($_POST['administration_source_add']))
		{
			$membres_manager = new MembresMan($bdd);
			$membres_manager->addSource($membre, $_POST['administration_source_add']);
			$_SESSION['administration_source_added'] = $_POST['administration_source_add'];
			$erreurs = true;
		}
		
		if(!empty($_POST['administration_club_add']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			
			if($communautes_manager->addClubAccueil($_POST['administration_club_add']))
			{
				$_SESSION['administration_club_added'] = true;
				$erreurs = true;
			}
		}
		
		if(!empty($_GET['suspendre_notif_communaute']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			$proposition = $communautes_manager->getPropositionByID($_GET['suspendre_notif_communaute']);
			
			if(!empty($proposition))
			{
				$communautes_manager->suspendre_notification($proposition->ID);
				$_SESSION['notif_communaute_suspendue'] = $proposition->nom;
				redirect('administration.php');
			}
		}
		
		if(!empty($_GET['remettre_notif_communaute']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			$proposition = $communautes_manager->getPropositionByID($_GET['remettre_notif_communaute']);
			
			if(!empty($proposition))
			{
				$communautes_manager->remettre_notification($proposition->ID);
				$_SESSION['notif_communaute_remise'] = $proposition->nom;
				redirect('administration.php');
			}
		}
		
		if(!empty($_POST['administration_annonce_add']))
		{
			$objets_manager = new ObjetsMan($bdd);
			
			if($objets_manager->addAnnonceAccueil($_POST['administration_annonce_add']))
			{
				$_SESSION['administration_annonce_added'] = true;
				$erreurs = true;
			}
		}
		
		if(isset($_GET['d']))
		{
			$membres_manager = new MembresMan($bdd);
			$membres_manager->delSource($membre, $_GET['d']);
			$_SESSION['administration_source_removed'] = true;
			redirect('administration.php');
		}
		
		if(isset($_GET['c']) && isset($_GET['o']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			$communautes_manager->modifyClubAccueil($_GET['c'], $_GET['o']);
			$_SESSION['administration_club_modified'] = true;
			redirect('administration.php');
		}
		
		if(isset($_GET['ca']))
		{
			$communautes_manager = new CommunautesMan($bdd);
			$communautes_manager->delClubAccueil($_GET['ca']);
			$_SESSION['administration_club_removed'] = true;
			redirect('administration.php');
		}
		
		if(isset($_GET['e']) && isset($_GET['o']))
		{
			$objets_manager = new ObjetsMan($bdd);
			$objets_manager->modifyAnnonceAccueil($_GET['e'], $_GET['o']);
			$_SESSION['administration_annonce_modified'] = true;
			redirect('administration.php');
		}
		
		if(isset($_GET['a']))
		{
			$objets_manager = new ObjetsMan($bdd);
			$objets_manager->delAnnonceAccueil($_GET['a']);
			$_SESSION['administration_annonce_removed'] = true;
			redirect('administration.php');
		}
		
		if(isset($_GET['c']) && isset($_GET['n']))
		{
			$membres_manager = new MembresMan($bdd);
			$membres_manager->changeSource($membre, $_GET['c'], $_GET['n']);
			$_SESSION['administration_source_removed'] = true;
			redirect('administration.php');
		}
		
		if(isset($_POST['submit_modifier_cat']) && isset($_POST['modifier_cat_name']) && isset($_POST['modifier_cat_ordre']) && isset($_POST['modifier_cat_id']))
		{
			$categories_manager = new CategoriesMan($bdd);
			$categories_manager->modifierCategorie($_POST['modifier_cat_id'], $_POST['modifier_cat_name'], $_POST['modifier_cat_ordre']);
			
			$_SESSION['administration_cat_modified'] = $_POST['modifier_cat_name'];
			
			redirect();
		}
		
		if(isset($_POST['submit_modifier_sous_cat']) && isset($_POST['modifier_sous_cat_name']) && isset($_POST['modifier_sous_cat_ordre']) && isset($_POST['modifier_sous_cat_id']) && isset($_FILES['modifier_sous_cat_photo']))
		{
			$sous_categories_manager = new SousCategoriesMan($bdd);
			$erreurs = $sous_categories_manager->modifierSous_categorie($_POST['modifier_sous_cat_id'], $_POST['modifier_sous_cat_name'], $_POST['modifier_sous_cat_ordre'], $_FILES['modifier_sous_cat_photo']);
			
			if($erreurs == false)
			{
				$_SESSION['administration_sous_cat_modified'] = $_POST['modifier_sous_cat_name'];
				redirect();
			}
		}
		
		if(!empty($_POST['new_cat_name']) && !empty($_POST['new_cat_ordre']) && isset($_POST['submit_new_cat']))
		{
			$categories_manager = new CategoriesMan($bdd);
			$categories_manager->addCategorie($_POST['new_cat_name'], $_POST['new_cat_ordre']);
			
			$_SESSION['administration_cat_created'] = $_POST['new_cat_name'];
			
			redirect();
		}
		
		if(!empty($_POST['new_sous_cat_id']) && !empty($_POST['new_sous_cat_name']) && !empty($_POST['new_sous_cat_ordre']) && isset($_POST['submit_new_sous_cat']) && isset($_FILES['new_sous_cat_photo']))
		{
			$sous_categories_manager = new SousCategoriesMan($bdd);
			$erreurs = $sous_categories_manager->addSous_categorie($_POST['new_sous_cat_id'], $_POST['new_sous_cat_name'], $_POST['new_sous_cat_ordre'], $_FILES['new_sous_cat_photo']);
			
			if($erreurs == false)
			{
				$_SESSION['administration_sous_cat_created'] = $_POST['new_sous_cat_name'];
				redirect();
			}
		}
		
		if(isset($_POST['administration_vider_tirelire_principale']))
		{
			$paiements_manager = new PaiementsMan($bdd);
			$comptes_bancaires_manager = new ComptesBancairesMan($bdd);
			
			$compte_bancaire = $comptes_bancaires_manager->getByMembre(ID_MEMBRE_ENTREPRISE);
			
			if($compte_bancaire != null)
			{
				if($erreurs = $paiements_manager->retirer(-1, $compte_bancaire->ID, number_format($paiements_manager->getTotalPaiements(-1), 2, ',', ' ')))
				{
					if(is_array($erreurs))
						$_SESSION['administration_tirelire_lemon'] = $erreurs;
					else
						$_SESSION['administration_tirelire_retire'] = $erreurs;
				}
			}
			else
				$_SESSION['administration_tirelire_compte_bancaire'] = true;
			
			redirect();
		}
		
		if($erreurs === true)
			redirect();
		
		new Page('administration', $membre, $bdd, array('erreurs' => $erreurs));
	}
	else
		new Page('page_incorrecte', $membre, $bdd);
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));
