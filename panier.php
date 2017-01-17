<?php

require_once('session.php');

$objets_manager = new ObjetsMan($bdd);
$erreur_panier = false;
$erreurs = array();
$erreurs2 = array();

foreach($_SESSION['panier'] as $value)
{
	$objet = $objets_manager->getByID($value['ID_objet']);
	
	if($erreur_panier = empty($objet))
		break;
}

if(!$erreur_panier)
{
	$transactions_manager = new TransactionsMan($bdd);
	$erreurs = $transactions_manager->checkCommande($_SESSION['panier']);
	
	if(!empty($_POST) && $erreurs === false)
	{
		if($membre)
		{
			$paiements_manager = new PaiementsMan($bdd);
			$montant_tirelire = $paiements_manager->getTotalPaiements($membre->ID);
			$prix_total = 0;
			
			foreach($_SESSION['panier'] as $key => $produit)
				$prix_total += $_SESSION['panier'][$key]['prix_unitaire']*$produit['quantite'];
			
			if($prix_total <= $montant_tirelire)
			{
				$result = array();
				
				foreach($_SESSION['panier'] as $objet)
					$result[] = array('objet' => $objet, 'result' => $transactions_manager->commander($membre, $objet));
				
				//require_once($_SESSION['dossier_vue'] . '/php/MailCommande.class.php');
				//new MailCommande($membre, $_SESSION['panier']);
				
				$_SESSION['panier'] = array();
				$_SESSION['commande_success'] = $result;
				redirect('perso.php?demandes_de_location');
			}
			else
				$erreurs2['pas_assez_d_argent'] = true;
		}
		else
		{
			new Page('connexion', $membre, $bdd, array('need_connec' => true));
			exit(0);
		}
	}
}

if(isset($_GET['r']))
{
	$index = intval($_GET['r']);
	$c = count($_SESSION['panier']);
	
	for($i=$index+1; $i<$c; $i++)
		$_SESSION['panier'][$i-1] = $_SESSION['panier'][$i];
	
	if($index >= 0 && $index <= $c-1)
		unset($_SESSION['panier'][$c - 1]);
	
	redirect('panier.php');
}

new Page('panier', $membre, $bdd, array('erreurs' => $erreurs, 'erreurs2' => $erreurs2));