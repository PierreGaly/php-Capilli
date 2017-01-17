<?php

class LitigesMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function add($ID_membre, $ID_transaction, $message)
	{
		$req = $this->bdd->prepare('INSERT INTO litiges VALUES(\'\', :ID_membre, :ID_reservation, :message, NOW(), 0)');
		$req->execute(array('ID_membre' => $ID_membre,
							'ID_reservation' => $ID_transaction,
							'message' => $message));
		$req->closeCursor();
	}
	
	public function traiterLitige($ID_litige)
	{
		$req = $this->bdd->prepare('UPDATE litiges SET date_traitement=NOW() WHERE ID=:ID');
		$req->execute(array('ID' => $ID_litige));
		$req->closeCursor();
	}
	
	public function getLitiges()
	{
		$req = $this->bdd->query('SELECT * FROM litiges WHERE date_traitement=0 ORDER BY date_creation');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Litige');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function countLitiges()
	{
		$req = $this->bdd->query('SELECT COUNT(*) c FROM litiges WHERE date_traitement=0 ORDER BY date_creation');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
}