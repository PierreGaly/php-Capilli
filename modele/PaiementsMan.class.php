<?php

class PaiementsMan
{
	protected $bdd;
	
	const POURCENTAGE_PARRAINAGE = 3;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function add($transaction, $ID_membre_from, $ID_membre_for, $montant, $type = false, $record_error = false)
	{
		if($montant <= 0)
			return -1;
		
		$membres_manager = new MembresMan($this->bdd);
		$ID_transaction = ($transaction instanceof Transaction) ? $transaction->ID : ((int) $transaction);
		
		$req = $this->bdd->prepare('INSERT INTO paiements VALUES(\'\', :ID_transaction, :ID_membre_from, :ID_membre_for, :montant, NOW())');
		$req->execute(array('ID_transaction' => $ID_transaction,
							'ID_membre_from' => $ID_membre_from,
							'ID_membre_for' => $ID_membre_for,
							'montant' => $montant));
		$req->closeCursor();
		
		$ID_paiement = $this->bdd->lastInsertId();
		
		if($ID_membre_from != -3 && $ID_membre_for != -3)
		{
			$message = '';
			
			if($ID_membre_for == -2)
				$message = 'Location en tant que locataire';
			else if($ID_membre_from == -2 && $type = 'location')
				$message = 'Location en tant que propriétaire';
			else if($ID_membre_from == -2 && $type = 'annulation')
				$message = 'Remboursement après annulation';
			else if($ID_membre_from == -2 && $type = 'parrainage')
				$message = 'Commission par parrainage';
			else if($ID_membre_from == -2 && $type = 'commission_annulation')
				$message = 'Commission par annulation';
			else if($ID_membre_from == -2 && $type = 'commission_location')
				$message = 'Commission par location';
			
			$lemon = LemonWay::SendPayment(array('debitWallet' => $ID_membre_from,
												 'creditWallet' => $ID_membre_for,
												 'amount' => number_format($montant, 2, '.', ''),
												 'message' => $message,
												 'privateData' => $ID_paiement));
			
			if($lemon['error'])
			{
				$req = $this->bdd->prepare('DELETE FROM paiements WHERE ID=:ID_paiement');
				$req->execute(array('ID_paiement' => $ID_paiement));
				$req->closeCursor();
				
				if(true || $record_error)
				{
					$req = $this->bdd->prepare('INSERT INTO paiements_errors VALUES(\'\', :ID_transaction, :ID_membre_from, :ID_membre_for, :montant, :type, :LemonCode, :LemonMsg, :LemonPrio, :record_error, NOW())');
					$req->execute(array('ID_transaction' => $ID_transaction,
										'ID_membre_from' => $ID_membre_from,
										'ID_membre_for' => $ID_membre_for,
										'montant' => $montant,
										'type' => $type,
										'LemonCode' => $lemon['result']['Code'],
										'LemonMsg' => $lemon['result']['Msg'],
										'LemonPrio' => $lemon['result']['Prio'],
										'record_error' => $record_error));
					$req->closeCursor();
				}
				
				return $lemon['result'];
			}
		}
		
		if($ID_membre_for == -3)
		{
			require_once($_SESSION['dossier_vue'] . '/php/MailVersementVersBanque.class.php');
			new MailVersementVersBanque($membres_manager->getMembreByID($ID_membre_from), $montant);
		}
		else if($ID_membre_from == -3)
		{
			require_once($_SESSION['dossier_vue'] . '/php/MailVersementVersSite.class.php');
			new MailVersementVersSite($membres_manager->getMembreByID($ID_membre_for), $montant);
		}
		
		return $ID_paiement;
	}
	
	public function getPaiements_errors()
	{
		$req = $this->bdd->query('SELECT * FROM paiements_errors WHERE record_error=1');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Paiement_error');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getPaiementsAVenirFor($ID_membre, $paiement_refused = true, $paiement_done = false, $paiement_pending = true)
	{
		$req = $this->bdd->prepare('SELECT -1 ID, t.ID ID_transaction, t.ID_locataire ID_membre_from, o.ID_proprio ID_membre_for, t.prix_unitaire_proprio*t.quantite montant, (date_fin_loc + INTERVAL :nbr_jours_paiement_auto DAY) date_creation, t.paiement paiement FROM transactions t INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID_proprio=:ID_proprio AND t.reponse=1 AND t.annulation=0 AND t.paiement!=1 ORDER BY t.date_transaction DESC');
		$req->execute(array('nbr_jours_paiement_auto' => NBR_JOURS_PAIEMENT_AUTO,
							'ID_proprio' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Paiement');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getPaiementsAVenir($paiement_refused = true, $paiement_done = false, $paiement_pending = true)
	{
		$tab = array();
		
		if($paiement_refused)
			$tab[] = 't.paiement=0';
		
		if($paiement_done)
			$tab[] = 't.paiement=1';
		
		if($paiement_pending)
			$tab[] = 't.paiement=2';
		
		$chaine = implode(' OR ', $tab);
		
		$req = $this->bdd->prepare('SELECT -1 ID, t.ID ID_transaction, t.ID_locataire ID_membre_from, o.ID_proprio ID_membre_for, t.prix_unitaire_proprio*t.quantite montant, (date_fin_loc + INTERVAL :nbr_jours_paiement_auto DAY) date_creation, t.paiement paiement FROM transactions t INNER JOIN objets o ON o.ID=t.ID_objet WHERE t.reponse=1 AND t.annulation=0 AND (' . $chaine . ') ORDER BY t.date_transaction DESC');
		$req->execute(array('nbr_jours_paiement_auto' => NBR_JOURS_PAIEMENT_AUTO));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Paiement');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getPaiementByID($ID_paiement)
	{
		$req = $this->bdd->prepare('SELECT * FROM paiements WHERE ID=:ID');
		$req->execute(array('ID' => $ID_paiement));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Paiement');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getPaiementsByMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT * FROM paiements WHERE ID_membre_for=:ID_membre_for OR ID_membre_from=:ID_membre_from ORDER BY date_creation DESC');
		$req->execute(array('ID_membre_for' => $ID_membre,
							'ID_membre_from' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Paiement');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function isValidMontant($prix)
	{
		return (strlen($prix) >= 4 && $prix[strlen($prix) - 3] == '.' && ctype_digit(substr($prix, -2)) && ((int) substr($prix, -2)) >=0 && ctype_digit(substr($prix, 0, -3)) && ((int) substr($prix, 0, -3)) >= 0) && !(strlen($prix) == 4 && $prix[0] == '0' && $prix[2] == '0' && $prix[3] == '0');
	}
	
	public function retirer($ID_membre, $ID_compte_bancaire, $montant)
	{
		$objets_manager = new ObjetsMan($this->bdd);
		$versements_reels_manager = new Versements_reelsMan($this->bdd);
		
		$montant = $objets_manager->formatPrix($montant);
		
		if($this->isValidMontant($montant))
		{
			$total = $this->getTotalPaiements($ID_membre);
			
			if($montant > 0 && $montant <= $total)
			{
				$result = $this->add(-1, $ID_membre, -3, $montant);
				
				if(is_array($result))
					return $result;
				
				$lemon = $versements_reels_manager->add($result, $ID_compte_bancaire);
				
				return $lemon['error'] ? $lemon['result'] : $montant;
			}
		}
		
		return false;
	}
	
	public function verser($ID_membre, $montant)
	{
		$this->add(-1, -3, $ID_membre, $montant);
	}
	/*
	public function getLastRaz($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT date_creation FROM paiements_raz WHERE ID_membre=:ID_membre');
		$req->execute(array('ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return empty($donnee) ? 0 : $donnee['date_creation'];
	}*/
	
	public function getTotalPaiements($ID_membre)
	{
		/*
		$req = $this->bdd->prepare('SELECT SUM(montant) somme FROM paiements WHERE date_creation>:date_last_raz AND ID_membre_for=:ID_membre_for');
		$req->execute(array('date_last_raz' => $this->getLastRaz($ID_membre),
							'ID_membre_for' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		*/
		
		$req = $this->bdd->prepare('SELECT SUM(montant) somme FROM paiements WHERE ID_membre_for=:ID_membre_for');
		$req->execute(array('ID_membre_for' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT SUM(montant) somme FROM paiements WHERE ID_membre_from=:ID_membre_from');
		$req->execute(array('ID_membre_from' => $ID_membre));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		return $donnee['somme'] - $donnee2['somme'];
	}
	
	public function getTotalPaiementsByParrainage($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT SUM(p.montant) somme FROM paiements p INNER JOIN transactions t ON p.ID_transaction=t.ID INNER JOIN objets o ON t.ID_objet=o.ID WHERE p.ID_membre_from=-2 AND p.ID_membre_for=:ID_membre AND t.ID_locataire!=:ID_membre AND o.ID_proprio!=:ID_membre');
		$req->execute(array('ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['somme'];
	}
	
	/*
	public function getTotalPaiementsLocations($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT SUM(p.montant) somme FROM paiements p INNER JOIN transactions t ON t.ID=p.ID_transaction INNER JOIN objets o ON t.ID_objet=o.ID WHERE p.date_creation>:date_last_raz AND p.ID_membre_for=:ID_membre_for AND o.ID_proprio=p.ID_membre_for');
		$req->execute(array('date_last_raz' => $this->getLastRaz($ID_membre),
							'ID_membre_for' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['somme'];
	}
	
	public function getTotalPaiementsParrainage($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT SUM(p.montant) somme FROM paiements p INNER JOIN transactions t ON t.ID=p.ID_transaction INNER JOIN objets o ON t.ID_objet=o.ID WHERE p.date_creation>:date_last_raz AND p.ID_membre_for=:ID_membre_for AND o.ID_proprio!=p.ID_membre_for');
		$req->execute(array('date_last_raz' => $this->getLastRaz($ID_membre),
							'ID_membre_for' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['somme'];
	}
	*/
	
	public function calculMontantParrain(Transaction $transaction)
	{
		return round($transaction->quantite * $transaction->prix_unitaire_locataire * self::POURCENTAGE_PARRAINAGE ) / 100;
	}
	
	public function payerTransaction(Transaction $transaction)
	{
		if($transaction->paiement == 2)
		{
			$parrainages_manager = new ParrainagesMan($this->bdd);
			$objets_manager = new ObjetsMan($this->bdd);
			
			$objet = $objets_manager->getByID($transaction->ID_objet, 2);
			$result = $this->add($transaction, -2, $objet->ID_proprio, $transaction->quantite * $transaction->prix_unitaire_proprio, 'location', true);
			
			if(is_array($result))
				return false;
			
			$parrain = $parrainages_manager->getParrain($objet->ID_proprio);
			
			$req = $this->bdd->prepare('UPDATE transactions SET paiement=1 WHERE ID=:ID');
			$req->execute(array('ID' => $transaction->ID));
			$req->closeCursor();
			
			if(!empty($parrain))
			{
				$montant_parrain = $this->calculMontantParrain($transaction);
				$result = $this->add($transaction, -2, $parrain->ID, $montant_parrain, 'parrainage', true);
			}
			else
				$montant_parrain = 0;
			
			$result = $this->add($transaction, -2, -1, $transaction->quantite * ($transaction->prix_unitaire_locataire - $transaction->prix_unitaire_proprio) - $montant_parrain, 'commission_location', true);
		}
	}
	
	public function payerTransactions()
	{
		$transactions_manager = new TransactionsMan($this->bdd);
		$paiements = $this->getPaiementsAVenir(false, false, true);
		$today = new DateTime((new DateTime('now'))->format('Y-m-d'));
		$paiements_effectues = array();
		
		foreach($paiements as $paiement)
		{
			if(new DateTime($paiement->date_creation) <= $today)
			{
				$this->payerTransaction($transactions_manager->getTransactionByID($paiement->ID_transaction));
				$paiements_effectues[] = $paiement;
			}
		}
		
		return $paiements_effectues;
	}
}