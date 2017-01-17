<?php

require_once('session.php');

if(isset($_GET['perso_messages']) && isset($_GET['pseudo']))
{
	header("Content-type: application/json");
	
	$membres_manager = new MembresMan($bdd);
	echo json_encode($membres_manager->getMembresByDebutPseudo($membre ? $membre->ID : -1, $_GET['pseudo']));
}

if(isset($_GET['perso_messages_conversation']) && isset($_GET['c']) && isset($_GET['pseudo']))
{
	header("Content-type: application/json");
	
	$membres_manager = new MembresMan($bdd);
	echo json_encode($membres_manager->getMembresByDebutPseudoPourMessagerie($_GET['c'], $_GET['pseudo']));
}

if(!empty($_GET['id']) && !empty($_GET['d1']) && !empty($_GET['d2']) && isset($_GET['q']))
{
	header("Content-type: application/json");
	
	$objets_manager = new ObjetsMan($bdd);
	$transactions_manager = new TransactionsMan($bdd);
	
	$quantite = ((int) $_GET['q'] == 0) ? 0 : (int) $_GET['q'];
	$prix_unitaire = $objets_manager->prixLocation($objets_manager->getByID($_GET['id']), $_GET['d1'], $_GET['d2']);
	$objet = array('ID_objet' => $_GET['id'],
				   'quantite' => ($quantite == 0) ? 1 : $quantite,
				   'date_debut' => $_GET['d1'],
				   'date_fin' => $_GET['d2'],
				   'prix_unitaire' => $prix_unitaire);
	
	$erreurs = $transactions_manager->checkCommande(array($objet));
	
	echo json_encode(array($erreurs === false, $quantite, $prix_unitaire));
}

if(isset($_GET['annonce_nouvelle']) && !empty($_GET['c']))
{
	header("Content-type: application/json");
	$sous_categories_manager = new SousCategoriesMan($bdd);
	$sous_categories = $sous_categories_manager->getSousCategoriesByCategorie($_GET['c']);
	$donnees = array();
	
	foreach($sous_categories as $sous_categorie)
		$donnees[] = array($sous_categorie->ID, htmlspecialchars($sous_categorie->nom));
	
	echo json_encode($donnees);
}

if(isset($_GET['inscription']) && isset($_GET['ecole_name']))
{
	header("Content-type: application/json");
	$ecole_name = trim($_GET['ecole_name']);
	$ecoles = array();
	
	if($ecole_name !== '')
	{
		$ecoles_manager = new EcolesMan($bdd);
		$donnees = $ecoles_manager->getEcolesByDebutNom($ecole_name, 5);
		
		foreach($donnees as $donnee)
			$ecoles[] = array($donnee->ID, htmlspecialchars(($donnee->sigle === '') ? $donnee->nom : $donnee->sigle), htmlspecialchars($donnee->commune));
	}
	
	echo json_encode(array($_GET['ecole_name'], $ecoles));
}
