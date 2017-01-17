<?php

class Versements_reelsMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function add($ID_paiement, $ID_compte_bancaire)
	{
		$req = $this->bdd->prepare('INSERT INTO versements_reels VALUES(\'\', :ID_paiement, :ID_compte_bancaire, NOW(), 0)');
		$req->execute(array('ID_paiement' => $ID_paiement,
							'ID_compte_bancaire' => $ID_compte_bancaire));
		$req->closeCursor();
	}
	
	public function getVersementsEnAttente()
	{
		$req = $this->bdd->query('SELECT * FROM versements_reels WHERE date_validation=0');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Versement_reel');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function hasVersementsEnAttenteByMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT v.ID FROM versements_reels v INNER JOIN comptes_bancaires c ON v.ID_compte_bancaire=c.ID WHERE v.date_validation=0 AND c.ID_membre=:ID_membre');
		$req->execute(array('ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function countVersementsEnAttente()
	{
		$req = $this->bdd->query('SELECT COUNT(*) FROM versements_reels WHERE date_validation=0');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
	
	public function getByID($ID_versement_reel)
	{
		$req = $this->bdd->prepare('SELECT * FROM versements_reels WHERE ID=:ID');
		$req->execute(array('ID' => $ID_versement_reel));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Versement_reel');
		$donnees = $req->fetch();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function actualiserCompteBancaire(Versement_reel $versement_reel)
	{
		$comptes_bancaires_manager = new ComptesBancairesMan($this->bdd);
		$paiements_manager = new PaiementsMan($this->bdd);
		$membres_manager = new MembresMan($this->bdd);
		
		$compte_bancaire = $comptes_bancaires_manager->getByMembre($membres_manager->getMembreByID($paiements_manager->getPaiementByID($versement_reel->ID_paiement)->ID_membre_from)->ID);
		
		if($compte_bancaire == null)
			return 'compte_bancaire_not_exists';
		
		if($compte_bancaire->ID == $versement_reel->ID_compte_bancaire)
			return 'compte_bancaire_same';
		
		$req = $this->bdd->prepare('UPDATE versements_reels SET ID_compte_bancaire=:ID_compte_bancaire WHERE ID=:ID');
		$req->execute(array('ID_compte_bancaire' => $compte_bancaire->ID,
							'ID' => $versement_reel->ID));
		$req->closeCursor();
		
		return false;
	}
	
	public function invalider($ID_versement_reel)
	{
		$versement_reel = $this->getByID($ID_versement_reel);
		
		if($versement_reel == null)
			return 'versement_invalide_not_exists';
		
		if($versement_reel->date_validation == 0)
			return 'versement_already_invalide';
		
		$req = $this->bdd->prepare('UPDATE versements_reels SET date_validation=0 WHERE ID=:ID');
		$req->execute(array('ID' => $versement_reel->ID));
		$req->closeCursor();
		
		return false;
	}
	
	public function valider($ID_versement_reel)
	{
		$paiements_manager = new PaiementsMan($this->bdd);
		$comptes_bancaires_manager = new ComptesBancairesMan($this->bdd);
		
		$versement_reel = $this->getByID($ID_versement_reel);
		$paiement = $paiements_manager->getPaiementByID($versement_reel->ID_paiement);
		$compte_bancaire = $comptes_bancaires_manager->getByID($versement_reel->ID_compte_bancaire);
		
		if($versement_reel == null)
			return 'versement_valide_not_exists';
		
		if($versement_reel->date_validation != 0)
			return 'versement_already_valide';
		
		$lemon = LemonWay::MoneyOut(array('wallet' => $paiement->ID_membre_from,
										  'amountTot' => number_format($paiement->montant, 2, '.', ''),
										  'message' => 'Transaction vers le compte bancaire',
										  'ibanId' => $compte_bancaire->ID_lemon,
										  'autoCommission' => 0));
		
		if($lemon['error'])
			return $lemon['result'];
		
		$req = $this->bdd->prepare('UPDATE versements_reels SET date_validation=NOW() WHERE ID=:ID');
		$req->execute(array('ID' => $versement_reel->ID));
		$req->closeCursor();
		
		return false;
	}
}