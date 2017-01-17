<?php

class ComptesBancairesMan
{
	protected $bdd;
	
	const BIC_MAX_LENGTH = 11;
	const NOM_AGENCE = 26;
	const RUE_AGENCE = 26;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getByMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT * FROM comptes_bancaires WHERE ID_membre=:ID_membre AND date_suppression=0');
		$req->execute(array('ID_membre' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Compte_bancaire');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getByID($ID_compte_bancaire)
	{
		$req = $this->bdd->prepare('SELECT * FROM comptes_bancaires WHERE ID=:ID');
		$req->execute(array('ID' => $ID_compte_bancaire));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Compte_bancaire');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public static function getHumanReadableIBAN($iban)
	{
		return wordwrap($iban, 4, ' ', true);
	}
	
	public static function getHumanReadableBIC($bic)
	{
		$bic_formated = substr($bic, 0, 4) . ' ' . substr($bic, 4, 2) . ' ' . substr($bic, 6, 2);
		
		if(strlen($bic) > 8)
			$bic_formated .= ' ' . substr($bic, 8);
		
		return $bic_formated;
	}
	
	public function isValidTitulaire($titulaire)
	{
		return !empty($titulaire);
	}
	
	public function isValidBic($bic)
	{
		return (boolean) preg_match('/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/', $bic);
	}
	
	public function isValidNomAgence($iban, $nom_agence)
	{
		$country = (strlen($iban) >= 2) ? strtoupper(substr($iban, 0, 2)) : '';
		
		if(strlen($nom_agence) > self::NOM_AGENCE || ($country != 'FR' && $country != 'MC' && empty($nom_agence)))
			return false;
		
		return true;
	}
	
	public function isValidRueAgence($iban, $rue_agence)
	{
		$country = (strlen($iban) >= 2) ? strtoupper(substr($iban, 0, 2)) : '';
		
		if(strlen($rue_agence) > self::RUE_AGENCE || ($country != 'FR' && $country != 'MC' && empty($rue_agence)))
			return false;
		
		return true;
	}
	
	public function add($ID_membre, $titulaire, $iban, $bic, $nom_agence, $rue_agence)
	{
		require_once('php-iban-master/php-iban.php');
		
		$bic = str_replace(' ', '', $bic);
		$iban = iban_to_machine_format($iban);
		
		$erreurs = array('titulaire' => !$this->isValidTitulaire($titulaire),
						 'iban' => !verify_iban($iban, $machine_format_only=true),
						 'bic' => !$this->isValidBic($bic),
						 'only_one' => ($this->getByMembre($ID_membre) != null),
						 'nom_agence' => !$this->isValidNomAgence($iban, $nom_agence),
						 'rue_agence' => !$this->isValidRueAgence($iban, $rue_agence),
						 'lemon' => false);
		
		if(!in_array(true, $erreurs))
		{
			$lemon_result = LemonWay::RegisterIBAN(array('wallet' => $ID_membre,
														   'holder' => $titulaire,
														   'bic' => $bic,
														   'iban' => $iban,
														   'dom1' => $nom_agence,
														   'dom2' => $rue_agence));
			
			if($lemon_result['error'])
			{
				$erreurs['lemon'] = $lemon_result['result'];
				return $erreurs;
			}
			
			$req = $this->bdd->prepare('INSERT INTO comptes_bancaires VALUES(\'\', :ID_lemon, :ID_membre, :titulaire, :iban, :bic, :nom_agence, :rue_agence, NOW(), 0)');
			$req->execute(array('ID_lemon' => array_key_exists('ID', $lemon_result['result']) ? $lemon_result['result']['ID'] : -1,
								'ID_membre' => $ID_membre,
								'titulaire' => $titulaire,
								'iban' => $iban,
								'bic' => $bic,
								'nom_agence' => $nom_agence,
								'rue_agence' => $rue_agence));
			$req->closeCursor();
			
			return true;
		}
		
		return $erreurs;
	}
	
	public function del($ID_membre)
	{
		$req = $this->bdd->prepare('UPDATE comptes_bancaires SET date_suppression=NOW() WHERE ID_membre=:ID_membre AND date_suppression=0');
		$req->execute(array('ID_membre' => $ID_membre));
		$req->closeCursor();
		
		return true;
	}
}