<?php

class TransactionsMan
{
	protected $bdd;
	
	const POURCENTAGE_PROPRIO = 80;// jamais supérieur à 100 ! Le proprio touche POURCENTAGE_PROPRIO % de chaque réservation
	const POURCENTAGE_ABANDON = 80;// jamais supérieur à 100 ! Club de Lok prend (100 - POURCENTAGE_ABANDON) % sur chaque abandon si la réservation est acceptée par les deux parties
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getTransactionsLoueur($ID_membre, $state, $accepted)
	{
		$chaine = '';
		
		if($accepted == 0)
			$chaine .= ' AND reponse=2 ';
		else if($accepted == 1)
			$chaine .= ' AND reponse!=2 ';
		
		if($state == 'p')
			$chaine .= ' AND date_fin_loc<CURDATE() ';
		else if($state == 'c')
			$chaine .= ' AND date_debut_loc<=CURDATE() AND date_fin_loc>=CURDATE() ';
		else if($state == 'v')
			$chaine .= ' AND date_debut_loc>CURDATE() ';
		
		$req = $this->bdd->prepare('SELECT * FROM transactions WHERE ID_locataire=:ID_locataire ' . $chaine . ' ORDER BY date_transaction DESC');
		$req->execute(array('ID_locataire' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getTransactionsObjet($ID_objet, $state, $accepted)
	{
		$chaine = '';
		
		if($accepted == 0)
			$chaine .= ' AND reponse=2 ';
		else if($accepted == 1)
			$chaine .= ' AND reponse!=2 ';
		
		if($state == 'p')
			$chaine .= ' AND date_fin_loc<CURDATE() ';
		else if($state == 'c')
			$chaine .= ' AND date_debut_loc<=CURDATE() AND date_fin_loc>=CURDATE() ';
		else if($state == 'v')
			$chaine .= ' AND date_debut_loc>CURDATE() ';
		
		$req = $this->bdd->prepare('SELECT * FROM transactions WHERE ID_objet=:ID_objet ' . $chaine . ' ORDER BY date_transaction DESC');
		$req->execute(array('ID_objet' => $ID_objet));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getAllTransactionsObjet($ID_objet)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions WHERE ID_objet=:ID_objet AND date_fin_loc>=CURDATE() ORDER BY date_transaction DESC');
		$req->execute(array('ID_objet' => $ID_objet));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getTransactionAsProprio($ID_transaction, $ID_membre)
	{
		$req = $this->bdd->prepare('SELECT t.* FROM transactions t INNER JOIN objets o ON o.ID=t.ID_objet WHERE t.ID=:ID_transaction AND o.ID_proprio=:ID_proprio');
		$req->execute(array('ID_proprio' => $ID_membre,
							'ID_transaction' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function countNotifsTransactionsAsProprio($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_messages m INNER JOIN transactions t ON m.ID_transaction=t.ID INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID_proprio=:ID_proprio AND m.vu_proprio=0');
		$req->execute(array('ID_proprio' => $ID_membre));
		$donnee1 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_dates d INNER JOIN transactions t ON d.ID_transaction=t.ID INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID_proprio=:ID_proprio AND d.vu_proprio=0');
		$req->execute(array('ID_proprio' => $ID_membre));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_codes c INNER JOIN transactions t ON c.ID_transaction=t.ID INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID_proprio=:ID_proprio AND c.vu_proprio=0');
		$req->execute(array('ID_proprio' => $ID_membre));
		$donnee3 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_annulation a INNER JOIN transactions t ON a.ID_transaction=t.ID INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID_proprio=:ID_proprio AND a.vu_proprio=0');
		$req->execute(array('ID_proprio' => $ID_membre));
		$donnee4 = $req->fetch();
		$req->closeCursor();
		
		return (int) ($donnee1[0] + $donnee2[0] + $donnee3[0] + $donnee4[0]);
	}
	
	public function countNotifsTransactionsAsLocataire($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_messages m INNER JOIN transactions t ON m.ID_transaction=t.ID WHERE t.ID_locataire=:ID_locataire AND m.vu_locataire=0');
		$req->execute(array('ID_locataire' => $ID_membre));
		$donnee1 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_dates d INNER JOIN transactions t ON d.ID_transaction=t.ID WHERE t.ID_locataire=:ID_locataire AND d.vu_locataire=0');
		$req->execute(array('ID_locataire' => $ID_membre));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_codes c INNER JOIN transactions t ON c.ID_transaction=t.ID WHERE t.ID_locataire=:ID_locataire AND c.vu_locataire=0');
		$req->execute(array('ID_locataire' => $ID_membre));
		$donnee3 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_annulation a INNER JOIN transactions t ON a.ID_transaction=t.ID WHERE t.ID_locataire=:ID_locataire AND a.vu_locataire=0');
		$req->execute(array('ID_locataire' => $ID_membre));
		$donnee4 = $req->fetch();
		$req->closeCursor();
		
		return ($donnee1[0] + $donnee2[0] + $donnee3[0] + $donnee4[0]);
	}
	
	public function countNotifsTransactionsAsLocataireByID_transation($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_messages WHERE ID_transaction=:ID_transaction AND vu_locataire=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee1 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_dates WHERE ID_transaction=:ID_transaction AND vu_locataire=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_codes WHERE ID_transaction=:ID_transaction AND vu_locataire=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee3 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_annulation WHERE ID_transaction=:ID_transaction AND vu_locataire=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee4 = $req->fetch();
		$req->closeCursor();
		
		return (int) ($donnee1[0] + $donnee2[0] + $donnee3[0] + $donnee4[0]);
	}
	
	public function countNotifsTransactionsAsProprioByID_transation($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_messages WHERE ID_transaction=:ID_transaction AND vu_proprio=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee1 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_dates WHERE ID_transaction=:ID_transaction AND vu_proprio=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_codes WHERE ID_transaction=:ID_transaction AND vu_proprio=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee3 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_annulation WHERE ID_transaction=:ID_transaction AND vu_proprio=0');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$donnee4 = $req->fetch();
		$req->closeCursor();
		
		return (int) ($donnee1[0] + $donnee2[0] + $donnee3[0] + $donnee4[0]);
	}
	
	public function countNotifsTransactionsAsProprioByID_objet($ID_objet)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_messages m INNER JOIN transactions t ON m.ID_transaction=t.ID WHERE t.ID_objet=:ID_objet AND m.vu_proprio=0');
		$req->execute(array('ID_objet' => $ID_objet));
		$donnee1 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_dates d INNER JOIN transactions t ON d.ID_transaction=t.ID WHERE t.ID_objet=:ID_objet AND d.vu_proprio=0');
		$req->execute(array('ID_objet' => $ID_objet));
		$donnee2 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_codes c INNER JOIN transactions t ON c.ID_transaction=t.ID WHERE t.ID_objet=:ID_objet AND c.vu_proprio=0');
		$req->execute(array('ID_objet' => $ID_objet));
		$donnee3 = $req->fetch();
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions_annulation a INNER JOIN transactions t ON a.ID_transaction=t.ID WHERE t.ID_objet=:ID_objet AND a.vu_proprio=0');
		$req->execute(array('ID_objet' => $ID_objet));
		$donnee4 = $req->fetch();
		$req->closeCursor();
		
		return (int) ($donnee1[0] + $donnee2[0] + $donnee3[0] + $donnee4[0]);
	}
	
	public function getTransactionByID($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions WHERE ID=:ID');
		$req->execute(array('ID' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getByLocataire($ID_locataire)
	{
		$req = $this->bdd->prepare('SELECT DISTINCT t.* FROM transactions t LEFT JOIN transactions_annulation a ON t.ID=a.ID_transaction LEFT JOIN transactions_dates d ON t.ID=d.ID_transaction LEFT JOIN transactions_messages m ON t.ID=m.ID_transaction LEFT JOIN transactions_codes c ON t.ID=c.ID_transaction WHERE t.ID_locataire=:ID_locataire AND (t.date_fin_loc >= (CURDATE() - INTERVAL 3 DAY) AND t.annulation=0 OR a.vu_locataire=0 OR d.vu_locataire=0 OR m.vu_locataire=0 OR c.vu_locataire=0) ORDER BY t.date_debut_loc');
		$req->execute(array('ID_locataire' => $ID_locataire));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function sendMailInformationLocation($jours)
	{
		require_once($_SESSION['dossier_vue'] . '/php/MailInformationLocation.class.php');
		$transactions_totales = array();
		$membres_manager = new MembresMan($this->bdd);
		
		foreach($jours as $jour)
		{
			$transactions = $this->getByLocataireInXDays($jour);
			$transactions_totales = array_merge($transactions_totales, $transactions);
			
			foreach($transactions as $transaction)
				new MailInformationLocation($membres_manager->getMembreByID($transaction->ID_locataire), $transaction, $jour);
		}
		
		return $transactions_totales;
	}
	
	public function getByLocataireInXDays($nbr_jours)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions WHERE date_debut_loc = CURDATE() + INTERVAL :nbr_jours DAY AND annulation=0');
		$req->execute(array('nbr_jours' => $nbr_jours));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function commander(Membre $membre, $produit)
	{
		$objets_manager = new ObjetsMan($this->bdd);
		$paiements_manager = new PaiementsMan($this->bdd);
		
		$date_debut = substr($produit['date_debut'], 6) . '-' . substr($produit['date_debut'], 3, 2) . '-' . substr($produit['date_debut'], 0, 2);
		$date_fin = substr($produit['date_fin'], 6) . '-' . substr($produit['date_fin'], 3, 2) . '-' . substr($produit['date_fin'], 0, 2);
		
		$req = $this->bdd->prepare('INSERT INTO transactions VALUES(\'\', :ID_objet, :ID_locataire, :prix_unitaire_proprio, :prix_unitaire_locataire, :quantite, :caution, :date_debut_loc, :date_fin_loc, NOW(), 0, 2, :cheque_caution, 0, 2)');
		$req->execute(array('ID_objet' => $produit['ID_objet'],
							'ID_locataire' => $membre->ID,
							'prix_unitaire_proprio' => $this->prixLocataire2prixProprio($produit['prix_unitaire']),
							'prix_unitaire_locataire' => $produit['prix_unitaire'],
							'quantite' => $produit['quantite'],
							'caution' => $objets_manager->getByID($produit['ID_objet'])->caution,
							'date_debut_loc' => $date_debut,
							'date_fin_loc' => $date_fin,
							'cheque_caution' => $produit['cheque_caution']));
		$req->closeCursor();
		
		$transaction = $this->getTransactionByID($this->bdd->lastInsertId());
		$resut = $paiements_manager->add($transaction, $membre->ID, -2, $produit['quantite']*$produit['prix_unitaire']);
		
		if(is_array($resut))
		{
			$req = $this->bdd->prepare('DELETE FROM transactions WHERE ID=:ID_transaction');
			$req->execute(array('ID_transaction' => $transaction->ID));
			$req->closeCursor();
			
			return $resut;
		}
		
		$this->proposerTransaction($transaction, $membre, $date_debut, $date_fin, true);
		
		return false;
	}
	
	public function prixLocataire2prixProprio($prix)
	{
		return round($prix * self::POURCENTAGE_PROPRIO) / 100;
	}
	
	public function checkCommande($objets)
	{
		// Init
		$erreurs = array();
		$date_debut = array();
		$date_fin = array();
		$objets_manager = new ObjetsMan($this->bdd);
		
		// Date objects creation
		foreach($objets as $objet)
		{
			$date_debut[] = date_create_from_format('j/m/Y', $objet['date_debut']);
			$date_fin[] = date_create_from_format('j/m/Y', $objet['date_fin']);
		}
		
		// We check each object consistency
		foreach($objets as $key_panier => $objet)
		{
			// Init
			$date_debut_loc = array();
			$date_fin_loc = array();
			$erreurs_0_dates = array();
			$erreurs_1_dates = array();
			$quantite_objet = $objets_manager->getByID($objet['ID_objet'])->nb_objets;
			
			// Get all registered accepted transactions overlapping with $objet : (date_fin >= $objet->date_debut AND date_debut <= $objet->date_fin) OR ($objet->date_fin >= date_debut AND $objet->date_debut <= date_fin)
			$req = $this->bdd->prepare('SELECT quantite, date_debut_loc, date_fin_loc FROM transactions WHERE ID_objet=:ID_objet AND date_reponse!=0 AND reponse=1 AND annulation=0 AND ((date_debut_loc <= :date_fin AND date_fin_loc >= :date_debut) OR (:date_debut <= date_fin_loc AND :date_fin >= date_debut_loc))');
			$req->execute(array('date_debut' => substr($objet['date_debut'], 6) . '-' . substr($objet['date_debut'], 3, 2) . '-' . substr($objet['date_debut'], 0, 2),
								'date_fin' => substr($objet['date_fin'], 6) . '-' . substr($objet['date_fin'], 3, 2) . '-' . substr($objet['date_fin'], 0, 2),
								'ID_objet' => $objet['ID_objet']));
			$donnees = $req->fetchAll();
			$req->closeCursor();
			
			// Date objects creation of overlapping transactions
			foreach($donnees as $donnee)
			{
				$date_debut_loc[] = date_create_from_format('Y-m-j', $donnee['date_debut_loc']);
				$date_fin_loc[] = date_create_from_format('Y-m-j', $donnee['date_fin_loc']);
			}
			
			// Init Date object for looping
			$jour = date_create_from_format('j/m/Y', $objet['date_debut']);
			$jour_fin = date_create_from_format('j/m/Y', $objet['date_fin']);
			
			// Looping on each day of $objet date range, a bit dangerous
			while($jour <= $jour_fin)
			{
				// Init
				$quantite_totale = 0;
				
				// Check for registered transactions overlapping with $objet on $jour day
				foreach($donnees as $key => $donnee)
					if($date_debut_loc[$key] <= $jour && $date_fin_loc[$key] >= $jour)
						$quantite_totale += $donnee['quantite'];
				
				// Error if too much quantities because of overlapping with registered transactions on $jour day, sorry guy...
				if($quantite_totale > ($quantite_objet + $objet['quantite']))
					$erreurs_0_dates[] = $jour->format('d/m/Y');
				else
				{
					// Check for none registered transactions (client's others commands) overlapping with $objet on $jour day
					foreach($objets as $key => $objet_panier)
						if($objet_panier['ID_objet'] == $objet['ID_objet'] && $date_debut[$key] <= $jour && $date_fin[$key] >= $jour)
							$quantite_totale += $objet_panier['quantite'];
					
					// Error if too much quantities because of overlapping with registered and none registered transactions on $jour day, sorry guy...
					if($quantite_totale > $quantite_objet)
						$erreurs_1_dates[] = $jour->format('d/m/Y');
				}
				
				// Looping reason
				$jour->modify('+1 day');
			}
			
			if(!empty($erreurs_0_dates))
				$erreurs[] = array('key' => $key_panier, 'type' => 0, 'dates' => $erreurs_0_dates);
			
			if(!empty($erreurs_1_dates))
				$erreurs[] = array('key' => $key_panier, 'type' => 1, 'dates' => $erreurs_1_dates);
		}
		
		// Great, here we are !
		return empty($erreurs) ? false : $erreurs;
	}
	
	public function ajouterAuPanier($membre, $ID_objet, $quantite, $date_debut, $date_fin, $cheque_caution)
	{
		$objets_manager = new ObjetsMan($this->bdd);
		$added = false;
		
		for($key=0; $key < count($_SESSION['panier']); $key++)
		{
			if($_SESSION['panier'][$key]['ID_objet'] == $ID_objet)
			{
				$objet = $objets_manager->getByID($_SESSION['panier'][$key]['ID_objet']);
				
				if(empty($objet))
					return true;
				
				$d1 = date_create_from_format('j/m/Y', $_SESSION['panier'][$key]['date_fin']);
				$d2 = date_create_from_format('j/m/Y', $date_debut);
				$d3 = date_create_from_format('j/m/Y', $date_fin);
				$d4 = date_create_from_format('j/m/Y', $_SESSION['panier'][$key]['date_debut']);
				
				if($_SESSION['panier'][$key]['date_debut'] == $date_debut && $_SESSION['panier'][$key]['date_fin'] == $date_fin)
				{
					$_SESSION['panier'][$key]['quantite'] += $quantite;
					$_SESSION['panier'][$key]['cheque_caution'] = $cheque_caution;
					$_SESSION['panier'][$key]['prix_unitaire'] = $objets_manager->prixLocation($objet, $_SESSION['panier'][$key]['date_debut'], $_SESSION['panier'][$key]['date_fin']);
					$added = true;
				}
			}
		}
		
		if(!$added)
			$_SESSION['panier'][] = array('ID_objet' => $ID_objet, 'quantite' => $quantite, 'date_debut' => $date_debut, 'date_fin' => $date_fin, 'prix_unitaire' => $objets_manager->prixLocation($objets_manager->getByID($ID_objet), $date_debut, $date_fin), 'cheque_caution' => $cheque_caution);
		
		return ($this->checkCommande($_SESSION['panier']) !== false);
	}
	
	public function accepterTransaction(Transaction $transaction, $ID_membre)
	{
		if($transaction->ID_locataire == $ID_membre)
		{
			$vu_proprio = 0;
			$vu_locataire = 1;
			$objets_manager = new ObjetsMan($this->bdd);
			$ID_membre_recepteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
		}
		else
		{
			$vu_proprio = 1;
			$vu_locataire = 0;
			$ID_membre_recepteur = $transaction->ID_locataire;
		}
		
		$req = $this->bdd->prepare('UPDATE transactions SET date_reponse=NOW(), reponse=1 WHERE ID=:ID');
		$req->execute(array('ID' => $transaction->ID));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('INSERT INTO transactions_codes VALUES(\'\', :ID_transaction, :code_reservation, 0, :vu_proprio, :vu_locataire)');
		$req->execute(array('ID_transaction' => $transaction->ID,
							'code_reservation' => $this->generate_code_reservation(),
							'vu_proprio' => $vu_proprio,
							'vu_locataire' => $vu_locataire));
		$req->closeCursor();
		
		$membres_manager = new MembresMan($this->bdd);
		require_once($_SESSION['dossier_vue'] . '/php/MailAccepterTransaction.class.php');
		new MailAccepterTransaction($transaction, $membres_manager->getMembreByID($ID_membre), $membres_manager->getMembreByID($ID_membre_recepteur));
	}
	
	public function annulerTransaction(Transaction $transaction, $ID_membre)
	{
		$paiements_manager = new PaiementsMan($this->bdd);
		
		if($transaction->reponse == 1 && $transaction->ID_locataire == $ID_membre)
		{
			$montant = round(self::POURCENTAGE_ABANDON * $transaction->quantite * $transaction->prix_unitaire_locataire) / 100;
			$lemon = $paiements_manager->add($transaction, -2, $transaction->ID_locataire, $montant, 'annulation');
			
			if(!is_array($lemon))
				$lemon2 = $paiements_manager->add($transaction, -2, -1, $transaction->quantite * $transaction->prix_unitaire_locataire - $montant, 'commission_annulation', true);
		}
		else
			$lemon = $paiements_manager->add($transaction, -2, $transaction->ID_locataire, $transaction->quantite * $transaction->prix_unitaire_locataire, 'annulation');
		
		if(is_array($lemon))
			return $lemon['result'];
		
		if($transaction->ID_locataire == $ID_membre)
		{
			$vu_proprio = 0;
			$vu_locataire = 1;
			$objets_manager = new ObjetsMan($this->bdd);
			$ID_membre_recepteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
		}
		else
		{
			$vu_proprio = 1;
			$vu_locataire = 0;
			$ID_membre_recepteur = $transaction->ID_locataire;
		}
		
		$req = $this->bdd->prepare('UPDATE transactions SET annulation=1 WHERE ID=:ID');
		$req->execute(array('ID' => $transaction->ID));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('INSERT INTO transactions_annulation VALUES(\'\', :ID_transaction, :ID_membre, NOW(), :vu_proprio, :vu_locataire)');
		$req->execute(array('ID_transaction' => $transaction->ID,
							'ID_membre' => $ID_membre,
							'vu_proprio' => $vu_proprio,
							'vu_locataire' => $vu_locataire));
		$req->closeCursor();
		
		$membres_manager = new MembresMan($this->bdd);
		require_once($_SESSION['dossier_vue'] . '/php/MailAnnulerTransaction.class.php');
		new MailAnnulerTransaction($transaction, $membres_manager->getMembreByID($ID_membre), $membres_manager->getMembreByID($ID_membre_recepteur));
		
		return false;
	}
	
	public function refuserTransaction($ID_transaction)
	{
		$req = $this->bdd->prepare('UPDATE transactions SET date_reponse=NOW(), reponse=0 WHERE ID=:ID');
		$req->execute(array('ID' => $ID_transaction));
		$req->closeCursor();
		
		$membres_manager = new MembresMan($this->bdd);
		$objets_manager = new ObjetsMan($this->bdd);
		
		$transaction = $this->getTransactionByID($ID_transaction);
		$transaction_dates = $this->getTransactions_dates($transaction->ID);
		
		if($transaction_dates[0]->ID_membre == $transaction->ID_locataire)
		{
			$ID_emetteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
			$ID_recepteur = $transaction->ID_locataire;
		}
		else
		{
			$ID_emetteur = $transaction->ID_locataire;
			$ID_recepteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
		}
		
		require_once($_SESSION['dossier_vue'] . '/php/MailRefuserTransaction.class.php');
		new MailRefuserTransaction($transaction, $membres_manager->getMembreByID($ID_emetteur), $membres_manager->getMembreByID($ID_recepteur));
	}
	
	public function generate_code_reservation()
	{
		//$chars = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$code = '';
		
		for($i=0; $i<5; $i++)
			$code .= $chars[mt_rand(0, strlen($chars) - 1)];
		
		return $code;
	}
	
	public function getTransaction_codeByID_transaction($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_codes WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_code');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function validerTransaction_code($ID_transaction_code)
	{
		$req = $this->bdd->prepare('UPDATE transactions_codes SET date_validation=NOW() WHERE ID=:ID');
		$req->execute(array('ID' => $ID_transaction_code));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT ID_transaction FROM transactions_codes WHERE ID=:ID');
		$req->execute(array('ID' => $ID_transaction_code));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		$paiements_manager = new PaiementsMan($this->bdd);
		$paiements_manager->payerTransaction($this->getTransactionByID($donnee['ID_transaction']));
	}
	
	public function addTransaction_message(Transaction $transaction, $ID_membre, $message)
	{
		if($transaction->ID_locataire == $ID_membre)
		{
			$vu_proprio = 0;
			$vu_locataire = 1;
			$objets_manager = new ObjetsMan($this->bdd);
			$ID_membre_recepteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
		}
		else
		{
			$vu_proprio = 1;
			$vu_locataire = 0;
			$ID_membre_recepteur = $transaction->ID_locataire;
		}
		
		$req = $this->bdd->prepare('INSERT INTO transactions_messages VALUES(\'\', :ID_transaction, :ID_membre, :message, NOW(), :vu_proprio, :vu_locataire)');
		$req->execute(array('ID_transaction' => $transaction->ID,
							'ID_membre' => $ID_membre,
							'message' => $message,
							'vu_proprio' => $vu_proprio,
							'vu_locataire' => $vu_locataire));
		$req->closeCursor();
		
		$membres_manager = new MembresMan($this->bdd);
		require_once($_SESSION['dossier_vue'] . '/php/MailTransactionMessage.class.php');
		new MailTransactionMessage($transaction, $membres_manager->getMembreByID($ID_membre), $membres_manager->getMembreByID($ID_membre_recepteur));
	}
	
	public function getTransactions_messages($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_messages WHERE ID_transaction=:ID_transaction ORDER BY date_creation DESC');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_message');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getTransactions_dates($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_dates WHERE ID_transaction=:ID_transaction ORDER BY date_proposition DESC');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_date');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function proposerTransaction(Transaction $transaction, Membre $membre, $date_debut_loc, $date_fin_loc, $just_created = false)
	{
		if($transaction->ID_locataire == $membre->ID)
		{
			$vu_proprio = 0;
			$vu_locataire = 1;
			$objets_manager = new ObjetsMan($this->bdd);
			$ID_membre_recepteur = $objets_manager->getByID($transaction->ID_objet, 2)->ID_proprio;
		}
		else
		{
			$vu_proprio = 1;
			$vu_locataire = 0;
			$ID_membre_recepteur = $transaction->ID_locataire;
		}
		
		$req = $this->bdd->prepare('INSERT INTO transactions_dates VALUES(\'\', :ID_transaction, :ID_membre, :date_debut_loc, :date_fin_loc, NOW(), :vu_proprio, :vu_locataire)');
		$req->execute(array('ID_transaction' => $transaction->ID,
							'ID_membre' => $membre->ID,
							'date_debut_loc' => $date_debut_loc,
							'date_fin_loc' => $date_fin_loc,
							'vu_proprio' => $vu_proprio,
							'vu_locataire' => $vu_locataire));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions SET date_debut_loc=:date_debut_loc, date_fin_loc=:date_fin_loc WHERE ID=:ID');
		$req->execute(array('ID' => $transaction->ID,
							'date_debut_loc' => $date_debut_loc,
							'date_fin_loc' => $date_fin_loc));
		$req->closeCursor();
		
		$membres_manager = new MembresMan($this->bdd);
		
		if($just_created)
		{
			require_once($_SESSION['dossier_vue'] . '/php/MailNouvelleReservation.class.php');
			new MailNouvelleReservation($this->getTransactionByID($transaction->ID), $membre, $membres_manager->getMembreByID($ID_membre_recepteur));
		}
		else
		{
			$transaction_dates = $this->getTransactions_dates($transaction->ID);
			
			require_once($_SESSION['dossier_vue'] . '/php/MailProposerModificationDates.class.php');
			new MailProposerModificationDates($this->getTransactionByID($transaction->ID), $membre, $membres_manager->getMembreByID($ID_membre_recepteur));
			
			return $membres_manager->updateTDR($membre, (new DateTime($transaction_dates[0]->date_proposition))->getTimestamp() - (new DateTime($transaction_dates[1]->date_proposition))->getTimestamp());
		}
		
		return $membre;
	}
	
	public function getTransaction_annulationByID_transaction($ID_transaction)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_annulation WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_annulation');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function updateVuTransactionAsLocataire($ID_transaction, $ID_membre)
	{
		$req = $this->bdd->prepare('UPDATE transactions_messages SET vu_locataire=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_dates SET vu_locataire=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_codes SET vu_locataire=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_annulation SET vu_locataire=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
	}
	
	public function updateVuTransactionAsProprio($ID_transaction, $ID_membre)
	{
		$req = $this->bdd->prepare('UPDATE transactions_messages SET vu_proprio=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_dates SET vu_proprio=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_codes SET vu_proprio=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('UPDATE transactions_annulation SET vu_proprio=1 WHERE ID_transaction=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
	}
	
	public function getCommentaires($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_commentaires c INNER JOIN transactions t ON c.ID_transaction=t.ID INNER JOIN objets o ON t.ID_objet=o.ID WHERE o.ID_proprio=:ID_proprio ORDER BY c.date_creation DESC');
		$req->execute(array('ID_proprio' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_commentaire');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getCommentaireByTransaction($ID_transaction, $ID_membre)
	{
		$req = $this->bdd->prepare('SELECT * FROM transactions_commentaires WHERE ID_transaction=:ID_transaction AND ID_membre=:ID_membre');
		$req->execute(array('ID_transaction' => $ID_transaction,
							'ID_membre' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction_commentaire');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function laisserCommentaire($ID_transaction, $ID_membre, $commentaire)
	{
		$req = $this->bdd->prepare('INSERT INTO transactions_commentaires VALUES(\'\', :ID_transaction, :ID_membre, :commentaire, NOW())');
		$req->execute(array('ID_transaction' => $ID_transaction,
							'ID_membre' => $ID_membre,
							'commentaire' => $commentaire));
		$req->closeCursor();
	}
	
	public function getTransactionsEnCours($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT t.* FROM transactions t INNER JOIN objets o ON o.ID=t.ID_objet WHERE t.ID_locataire=:ID_membre OR o.ID_proprio=:ID_membre');
		$req->execute(array('ID_membre' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function annulerPaiement($ID_transaction)
	{
		$req = $this->bdd->prepare('UPDATE transactions SET paiement=0 WHERE ID=:ID_transaction');
		$req->execute(array('ID_transaction' => $ID_transaction));
		$req->closeCursor();
	}
}