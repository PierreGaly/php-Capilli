<?php

class ParrainagesMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function existsParrainage($ID_parrain)
	{
		$req = $this->bdd->prepare('SELECT * FROM parrainages WHERE ID_parrain=:ID_parrain');
		$req->execute(array('ID_parrain' => $ID_parrain));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function getParrain($ID_filleul)
	{
		$req = $this->bdd->prepare('SELECT ID_parrain FROM parrainages WHERE ID_filleul=:ID_filleul');
		$req->execute(array('ID_filleul' => $ID_filleul));
		$ID_parrain = $req->fetch();
		$req->closeCursor();
		
		if(empty($ID_parrain))
			return false;
		
		$membres_manager = new MembresMan($this->bdd);
		
		return $membres_manager->getMembreByID($ID_parrain['ID_parrain']);
	}
	
	public function getFilleuls($ID_parrain)
	{
		$req = $this->bdd->prepare('SELECT * FROM membres m INNER JOIN parrainages p ON p.ID_filleul=m.ID WHERE p.ID_parrain=:ID_parrain');
		$req->execute(array('ID_parrain' => $ID_parrain));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getPetitsFilleuls($ID_parrain)
	{
		$req = $this->bdd->prepare('SELECT * FROM membres m INNER JOIN parrainages p2 ON p2.ID_filleul=m.ID INNER JOIN parrainages p1 ON p1.ID_filleul=p2.ID_parrain WHERE p1.ID_parrain=:ID_parrain');
		$req->execute(array('ID_parrain' => $ID_parrain));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function addParrainage($parrain, $filleul)
	{
		$req = $this->bdd->prepare('INSERT INTO parrainages VALUES(\'\', :ID_parrain, :ID_filleul, NOW(), 0)');
		$req->execute(array('ID_parrain' => $parrain->ID,
							'ID_filleul' => $filleul->ID));
		$req->closeCursor();
		
		require_once($_SESSION['dossier_vue'] . '/php/MailParrainage.class.php');
		new MailParrainage($parrain, $filleul);
	}
	
	public function countNouveauxParrainages($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) c FROM parrainages WHERE ID_parrain=:ID_parrain AND vu=0');
		$req->execute(array('ID_parrain' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
	
	public function getParrainagesNonVus($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT * FROM parrainages WHERE ID_parrain=:ID_parrain AND vu=0');
		$req->execute(array('ID_parrain' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Parrainage');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE parrainages SET vu=1 WHERE ID_parrain=:ID_parrain AND vu=0');
		$req->execute(array('ID_parrain' => $ID_membre));
		$req->closeCursor();
		
		return $donnees;
	}
}
