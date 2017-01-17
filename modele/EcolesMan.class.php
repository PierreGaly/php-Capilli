<?php

class EcolesMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getEcolesByDebutNom($name, $limite)
	{
		$req = $this->bdd->prepare('SELECT * FROM ecoles WHERE nom LIKE :name OR sigle LIKE :name LIMIT ' . ((int) $limite));
		$req->execute(array('name' => '%' . $name . '%'));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Ecole');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getEcoleByID($ID)
	{
		$req = $this->bdd->prepare('SELECT * FROM ecoles WHERE ID=:ID');
		$req->execute(array('ID' => $ID));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Ecole');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
}