<?php

class CategoriesMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getCategories()
	{
		$req = $this->bdd->query('SELECT * FROM categories ORDER BY ordre');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Categorie');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getCategorieByNom($nom)
	{
		$req = $this->bdd->prepare('SELECT * FROM categories WHERE nom=?');
		$req->execute(array($nom));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Categorie');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getCategorieByID($ID_categorie)
	{
		$req = $this->bdd->prepare('SELECT * FROM categories WHERE ID=?');
		$req->execute(array($ID_categorie));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Categorie');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function modifierCategorie($ID_categorie, $nom, $ordre)
	{
		$req = $this->bdd->prepare('UPDATE categories SET nom=:nom, ordre=:ordre WHERE ID=:ID_categorie');
		$req->execute(array('ID_categorie' => $ID_categorie,
							'nom' => $nom,
							'ordre' => $ordre));
		$req->closeCursor();
	}
	
	public function addCategorie($nom, $ordre)
	{
		$req = $this->bdd->prepare('INSERT INTO categories VALUES(\'\', :nom, :ordre)');
		$req->execute(array('nom' => $nom,
							'ordre' => $ordre));
		$req->closeCursor();
	}
}