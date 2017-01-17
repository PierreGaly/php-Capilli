<?php

require_once('session.php');

$objet = false;

if(isset($_GET['id']))
{
	$objets_manager = new ObjetsMan($bdd);
	$objet = $objets_manager->getByID($_GET['id']);
}

if($objet)
{
	$communautes_manager = new CommunautesMan($bdd);
	
	if($objet->ID_club == -1 || ($membre && $communautes_manager->isMembre($objet->ID_club, $membre->ID)))
	{
		if(isset($_GET['edit']))
		{
			if($membre)
			{
				if($membre->ID == $objet->ID_proprio)
				{
					$erreurs = array();
					
					if(!empty($_FILES['photos']))//Cas où le proprio veut modifier une photo
					{
						$photo_principale_defined = ($objet->photo_principale != '');
						
						foreach($_FILES['photos']['tmp_name'] as $k => $v)
						{
							$res = $objets_manager->addPhoto($objet->ID, $_FILES['photos']['tmp_name'][$k], $_FILES['photos']['name'][$k], $_FILES['photos']['size'][$k], $_FILES['photos']['error'][$k], $_FILES['photos']['type'][$k]);
							$erreurs[] = array($_FILES['photos']['name'][$k], $res);
							
							if($res === false && !$photo_principale_defined)
							{
								$objets_manager->modifyPhotoPrincipale($objet->ID, $_FILES['photos']['name'][$k]);
								$photo_principale_defined = true;
							}
						}
						
						$_SESSION['annonce_photos'] = $erreurs;
						redirect();
					}
					else if(!empty($_GET['pp']))
					{
						if($objets_manager->modifyPhotoPrincipale($objet->ID, $_GET['pp']))
							$_SESSION['ma_photo_principale'] = $_GET['pp'];
						
						redirect('annonce.php?id=' . $_GET['id'] . '&edit');
					}
					else if(!empty($_GET['ps']))
					{
						if($objets_manager->supprimerPhoto($objet, $_GET['ps']))
							$_SESSION['ma_photo_removed'] = $_GET['ps'];
						
						redirect('annonce.php?id=' . $_GET['id'] . '&edit');
					}
					else if(isset($_POST['ma_remove']))
					{
						$res = $objets_manager->delObjet($membre, $objet->ID);
						
						if($res)
						{
							$_SESSION['annonce_not_remove'] = true;
							redirect('annonce.php?id=' . $objet->ID . '&edit');
						}
						else
						{
							$_SESSION['annonce_removed'] = true;
							redirect('perso.php?annonces');
						}
					}
					else if(isset($_POST['ma_sous_categorie']))
						$erreurs['sous_categorie'] = $objets_manager->modifySous_categorie($objet->ID, $_POST['ma_sous_categorie']);
					else if(isset($_POST['ma_description']))
						$erreurs['description'] = $objets_manager->modifyDescription($objet->ID, $_POST['ma_description']);
					else if(isset($_POST['ma_location']))
						$erreurs['location'] = $objets_manager->modifyLocation($objet->ID, $_POST['ma_location']);
					else if(isset($_POST['ma_utilisation']))
						$erreurs['utilisation'] = $objets_manager->modifyUtilisation($objet->ID, $_POST['ma_utilisation']);
					else if(isset($_POST['ma_nb_objets']) && isset($_POST['ma_marque']) && isset($_POST['ma_modele']))
						$erreurs['informations'] = $objets_manager->modifyInformations($objet->ID, $_POST['ma_nb_objets'], $_POST['ma_marque'], $_POST['ma_modele']);
					else if(isset($_POST['ma_prix_journee']) && isset($_POST['ma_prix_weekend']) && isset($_POST['ma_prix_semaine']) && isset($_POST['ma_prix_mois']) && isset($_POST['ma_caution']))
						$erreurs['tarifs'] = $objets_manager->modifyTarifs($objet->ID, $_POST['ma_prix_journee'], $_POST['ma_prix_weekend'], $_POST['ma_prix_semaine'], $_POST['ma_prix_mois'], $_POST['ma_caution'], isset($_POST['ma_cheque']) ? true : false);
					else if(isset($_POST['ma_submit_actif']))
						$erreurs['actif'] = $objets_manager->modifyActif($objet->ID, 1);
					else if(isset($_POST['ma_submit_inactif']))
						$erreurs['inactif'] = $objets_manager->modifyActif($objet->ID, 0);
					
					if(!empty($erreurs) && !in_array(true, $erreurs))
						redirect('annonce.php?id=' . $objet->ID);
					
					new Page('annonce_modifier', $membre, $bdd, array('objet' => $objet, 'erreurs' => $erreurs));
				}
				else
					new Page('page_incorrecte', $membre, $bdd);
			}
			else
				new Page('connexion', $membre, $bdd, array('need_connec' => true));
		}
		else if(!empty($_POST['dates_commande']) && isset($_POST['quantite_commande']) && strlen($_POST['dates_commande']) == 23 && (!$membre || $objet->ID_proprio != $membre->ID))
		{
			$transactions_manager = new TransactionsMan($bdd);
			
			if($transactions_manager->ajouterAuPanier($membre, $objet->ID, ((int) $_POST['quantite_commande'] == 0) ? 1 : (int) $_POST['quantite_commande'], substr($_POST['dates_commande'], 0, 10), substr($_POST['dates_commande'], 13, 10), $objet->cheque_caution))
				redirect('panier.php');
			
			$_SESSION['commande_ajoutee'] = true;
			redirect();
		}
		else if(isset($_GET['reservations']))
		{
			if($membre)
			{
				if($membre->ID == $objet->ID_proprio)
				{
					$transactions_manager = new TransactionsMan($bdd);
					$transactions = $transactions_manager->getAllTransactionsObjet($objet->ID);
					
					new Page('annonce_reservations', $membre, $bdd, array('objet' => $objet, 'transactions' => $transactions));
				}
				else
					new Page('page_incorrecte', $membre, $bdd);
			}
			else
				new Page('connexion', $membre, $bdd, array('need_connec' => true));
		}
		else
		{
			if($objet->actif == 1 || ($membre && $membre->ID == $objet->ID_proprio))
				new Page('annonce_afficher', $membre, $bdd, array('objet' => $objet), $objet->nom);
			else
				new Page('annonce_inactive', $membre, $bdd);
		}
	}
	else if($membre)
		new Page('annonce_interdite', $membre, $bdd);
	else
		new Page('connexion', $membre, $bdd, array('need_connec' => true));
}
else if(isset($_GET['id']))
	new Page('annonce_supprimee', $membre, $bdd);
else if($membre)
{
	$result = false;
	
	if(isset($_POST['na_titre'])
		&& isset($_POST['na_sous_categorie'])
		&& isset($_POST['na_club'])
		&& isset($_POST['na_description'])
		&& isset($_POST['na_location'])
		&& isset($_POST['na_utilisation'])
		&& isset($_POST['na_marque'])
		&& isset($_POST['na_modele'])
		&& isset($_POST['na_prix_journee'])
		&& isset($_POST['na_prix_weekend'])
		&& isset($_POST['na_prix_semaine'])
		&& isset($_POST['na_prix_mois'])
		&& isset($_POST['na_caution'])
		&& isset($_POST['na_nb_objets'])
		&& isset($_FILES['na_photos']))
	{
		$objets_manager = new ObjetsMan($bdd);
		$result = $objets_manager->addObjet($membre,
											$_POST['na_titre'],
											$_POST['na_sous_categorie'],
											$_POST['na_club'],
											$_POST['na_description'],
											isset($_POST['na_cheque']) ? true : false,
											$_POST['na_location'],
											$_POST['na_utilisation'],
											$_POST['na_marque'],
											$_POST['na_modele'],
											$_POST['na_prix_journee'],
											$_POST['na_prix_weekend'],
											$_POST['na_prix_semaine'],
											$_POST['na_prix_mois'],
											$_POST['na_caution'],
											$_POST['na_nb_objets'],
											$_FILES['na_photos']);
		
		if(!is_array($result))
			redirect('annonce.php?id=' . $result);
	}
	
	new Page('annonce_nouvelle', $membre, $bdd, array('erreurs' => $result));
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));