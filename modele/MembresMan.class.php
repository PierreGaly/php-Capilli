<?php

class MembresMan
{
	protected $bdd;
	protected $extensions_autorisees = array('jpg','gif','png','jpeg');
	
	const TAILLE_MIN_PRENOM = 2;
	const TAILLE_MAX_PRENOM = 20;
	const TAILLE_MIN_NOM = 2;
	const TAILLE_MAX_NOM = 20;
	const TAILLE_MIN_EMAIL = 7;
	const TAILLE_MAX_EMAIL = 51;
	const TAILLE_MIN_MDP = 4;
	const TAILLE_MAX_MDP = 255;
	const TAILLE_NOM_AVATAR = 8;
	const TAILLE_OUBLI_MDP = 8;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function connect($email, $mdp, $hashed = false, $create_cookies = false)
	{
		if(!$hashed)
			$mdp = sha1($mdp);
		
		$req = $this->bdd->prepare('SELECT * FROM membres WHERE email=:email AND mdp=:mdp AND actif=1');
		$req->execute(array('email' => $email,
							'mdp' => $mdp));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		
		if(!empty($donnee) && ($donnee->email_valide == 1 || $donnee->type == 1 || $donnee->type == 2))
		{
			$req = $this->bdd->prepare('UPDATE membres SET date_derniere_connexion=NOW() WHERE ID=:ID');
			$req->execute(array('ID' => $donnee->ID));
			$req->closeCursor();
			
			$_SESSION['membre'] = $donnee;
			LemonWay::UpdateWalletDetails(array('wallet' => $donnee->ID, 'newIp' => $_SERVER['REMOTE_ADDR']));
			
			if($create_cookies)
			{
				$date = time() + 31536000;// 1 year
				setcookie('email', $donnee->email, $date);
				setcookie('mdp', $donnee->mdp, $date);
			}
			
			return false;
		}
		
		if(isset($_COOKIE['email']))
			setcookie('email');
		
		if(isset($_COOKIE['mdp']))
			setcookie('mdp');
		
		if(!empty($donnee))
			return 'erreur_conn_email_valide';
		
		return 'erreur_conn_membre_existe';
	}
	
	public function getMembreByEmail($email)
	{
		$req = $this->bdd->prepare('SELECT * FROM membres WHERE email=:email');
		$req->execute(array('email' => $email));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getMembreByID($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM membres WHERE ID=:ID');
		$req->execute(array('ID' => $id));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getMembres($nombre = 0, $offset = 0, $actif = 1)
	{
		if($nombre > 0)
			$chaine = ' LIMIT ' . ((int) $offset) . ', ' . ((int) $nombre);
		
		$req = $this->bdd->prepare('SELECT * FROM membres WHERE actif=:actif' . $chaine);
		$req->execute(array('actif' => (bool) $actif));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getByClub($ID_club, $validation = 2, $nombre = 0, $offset = 0)
	{
		$chaine = '';
		$chaine2 = '';
		
		if($validation == 1)
			$chaine = ' AND c.date_validation!=0';
		else if($validation == 0)
			$chaine = ' AND c.date_validation=0';
		
		if($nombre > 0)
			$chaine2 = ' LIMIT ' . ((int) $offset) . ', ' . ((int) $nombre);
		
		$req = $this->bdd->prepare('SELECT m.* FROM membres m INNER JOIN membres2communautes c ON m.ID=c.ID_membre WHERE c.ID_communaute=:ID_communaute' . $chaine . ' ORDER BY c.date_validation' . $chaine2);
		$req->execute(array('ID_communaute' => $ID_club));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	static public function isValidEmail($email)
	{
		return preg_match('#^[a-zA-Z0-9._-]{1,30}+@[a-z0-9._-]{2,15}\.[a-z]{2,4}$#', $email);
	}
	
	public function isValidPrenom($prenom)
	{
		return preg_match('#^.{' . self::TAILLE_MIN_PRENOM . ',' . self::TAILLE_MAX_PRENOM . '}$#', $prenom);
	}
	
	public function isValidNom($nom)
	{
		return preg_match('#^.{' . self::TAILLE_MIN_NOM . ',' . self::TAILLE_MAX_NOM . '}$#', $nom);
	}
	
	static public function isValidAdresse($adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng)
	{
		return !empty($lat) && !empty($lng);
	}
	
	static public function isValidTel_fixe($tel_fixe)
	{
		//return empty($tel_fixe) || preg_match('#^[^067][0-9]{8}$#', substr($tel_fixe, -9));
		return empty($tel_fixe) || preg_match('#^[0-9]{9}$#', substr($tel_fixe, -9));
	}
	
	static public function isValidTel_portable($tel_portable)
	{
		//return preg_match('#^(6|7)[0-9]{8}$#', substr($tel_portable, -9));
		return empty($tel_fixe) || preg_match('#^[0-9]{9}$#', substr($tel_fixe, -9));
	}
	
	public function newAvatar($tmp_name, $type)
	{
		$extension = '';
		
		if($type == 'image/jpeg')
			$extension = 'jpg';
		elseif($type == 'image/png')
			$extension = 'png';
		elseif($type == 'image/gif')
			$extension = 'gif';
		
		if($extension != '')
		{
			$chars = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			
			do
			{
				$nom = '';
				
				for($i=0; $i<MembresMan::TAILLE_NOM_AVATAR; $i++)
					$nom .= $chars[mt_rand(0, strlen($chars) - 1)];
				
				$nom .= '.' . $extension;
				
				$req = $this->bdd->prepare('SELECT ID FROM membres WHERE avatar=?');
				$req->execute(array($nom));
				$donnee = $req->fetch();
				$req->closeCursor();
			}
			while(!empty($donnee));
				
			require_once('fonctions/redimensionnement.php');
			$miniature = redimensionner($tmp_name, $extension, 200, 200);
			
			switch($extension)
			{
				case 'jpg':
					imagejpeg($miniature, 'avatars/' . $nom, 100);
					break;
				
				case 'png':
					imagepng($miniature, 'avatars/' . $nom, 9);
					break;
				
				case 'gif':
					imagegif($miniature, 'avatars/' . $nom, 100);
					break;
			}
			
			return $nom;
		}
		else
			return '';
	}
	
	public function handleAvatar($avatar)
	{
		if($avatar['error'] == 4)
			return false;
		
		$extension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
		
		if(in_array(strtolower($extension), $this->extensions_autorisees))
		{
			$infosImg = getimagesize($avatar['tmp_name']);
			
			if($infosImg[2] >= 1 && $infosImg[2] <= 14)
			{
				if(isset($avatar['error']) && UPLOAD_ERR_OK === $avatar['error'])
					return false;
				else
					$message = 'erreur interne';
			}
			else
				$message = 'le fichier envoyé n\'est pas une image';
		}
		else
			$message = 'l\'extension du fichier est incorrecte';
		
		return $message;
	}
	
	static public function isValidMdp($mdp)
	{
		return preg_match('#^.[^\s]{' . self::TAILLE_MIN_MDP . ',' . self::TAILLE_MAX_MDP . '}$#', $mdp);
	}
	
	static public function isValiDate_naissance($date_naissance)
	{
		if(strlen($date_naissance) != 10 || $date_naissance[2] != '/' || $date_naissance[5] != '/')
			return false;
		
		$month = substr($date_naissance, 3, 2);
		$day = substr($date_naissance, 0, 2);
		$year = substr($date_naissance, 6);
		
		return checkdate($month, $day , $year) && (new DateTime($month . '/' . $day . '/' . $year)) < (new DateTime("now"));
	}
	
	public function isValidSource($source)
	{
		$req = $this->bdd->prepare('SELECT ID FROM sources WHERE ID=?');
		$req->execute(array($source));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function format_tel($telephone)
	{
		if(empty($telephone))
			return '';
		
		$indicatif = '33';
		$telephone = preg_replace('/[^0-9+]/', '', $telephone);
		
		if(strlen($telephone) == 13 && substr($telephone, -13, 2) == '00' && strpos($telephone, '+') === false)
			$indicatif = substr($telephone, 2, 2);
		else if(strlen($telephone) == 12 && $telephone[0] == '+' && strpos($telephone, '+', 1) === false)
			$indicatif = substr($telephone, 1, 2);
		
		return '00' . $indicatif . substr(preg_replace('/[^0-9]/', '', $telephone), -9);
	}
	
	public function isValidParrain($parrain)
	{
		if(empty($parrain))
			return true;
		
		$parrain = $this->getMembreByID($parrain->ID);
		
		return !empty($parrain);
	}
	
	public function inscription($charte, $type, $civilite, $email, $prenom, $nom, $adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng, $tel_fixe, $tel_portable, $avatar, $mdp, $mdp2, $date_naissance, $source, $parrain)
	{
		$tel_fixe = $this->format_tel($tel_fixe);
		$tel_portable = $this->format_tel($tel_portable);
		
		$erreurs = array('charte' => !$charte,
						'email_valide' => !$this->isValidEmail($email),
						'email_existe' => $this->email_exists($email),
						'prenom' => !$this->isValidPrenom($prenom),
						'nom' => !$this->isValidNom($nom),
						'adresse_complete' => !$this->isValidAdresse($adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng),
						'tel_fixe' => !$this->isValidTel_fixe($tel_fixe),
						'tel_portable' => !$this->isValidTel_portable($tel_portable),
						'avatar' => $this->handleAvatar($avatar),
						'mdp_correspondance' => $mdp != $mdp2,
						'mdp' => !$this->isValidMdp($mdp),
						'date_naissance' => !$this->isValiDate_naissance($date_naissance),
						'source' => !$this->isValidSource($source),
						'parrain' => !$this->isValidParrain($parrain),
						'lemon' => false);
		
		if(!in_array(true, $erreurs))
		{
			if($avatar['error'] != 4)
				$avatar_name = $this->newAvatar($avatar['tmp_name'], $avatar['type']);
			
			$req = $this->bdd->prepare('INSERT INTO membres VALUES(\'\', \'-1\', :type, :civilite, :email, 0, :nom, :prenom, :adresse_complete, :street_number, :route, :locality, :administrative_area_level_1, :country, :postal_code, :lat, :lng, \'-1\', :avatar, :mdp, :tel_fixe, :tel_portable, NOW(), 0, :date_naissance, :ID_source, 0, 0, 0, 0, 1)');
			$req->execute(array('type' => $type,
								'civilite' => $civilite,
								'email' => $email,
								'nom' => $nom,
								'prenom' => $prenom,
								'adresse_complete' => $adresse_complete,
								'street_number' => $street_number,
								'route' => $route,
								'locality' => $locality,
								'administrative_area_level_1' => $administrative_area_level_1,
								'country' => $country,
								'postal_code' => $postal_code,
								'lat' => $lat,
								'lng' => $lng,
								'avatar' => isset($avatar_name) ? $avatar_name : '',
								'mdp' => sha1($mdp),
								'tel_fixe' => $tel_fixe,
								'tel_portable' => $tel_portable,
								'date_naissance' => substr($date_naissance, 6, 4) . '-' . substr($date_naissance, 3, 2) . '-' . substr($date_naissance, 0, 2),
								'ID_source' => $source));
			$req->closeCursor();
			
			$ID_membre = $this->bdd->lastInsertId();
			
			if(LemonWay::LEMON_DISABLED == false)
			{
				$lemon_result = LemonWay::RegisterWallet(array('wallet' => $ID_membre,
											   'clientMail' => $email,
											   'clientTitle' => $civilite ? 'F' : 'M',
											   'clientFirstName' => $prenom,
											   'clientLastName' => $nom,
											   'street' => $street_number . ' ' . $route,
											   'postCode' => $postal_code,
											   'city' => $locality,
											   'ctry' => strtoupper(substr($country, 0, 3)),
											   'phoneNumber' => $tel_fixe,
											   'mobileNumber' => $tel_portable,
											   'birthdate' => $date_naissance,
											   'isCompany' => $type,
											   'isOneTimeCustomer' => '0'));
				
				if($lemon_result['error'] || empty($lemon_result['result']['LWID']))
				{
					$req = $this->bdd->prepare('DELETE FROM membres WHERE ID=:ID_membre');
					$req->execute(array('ID_membre' => $ID_membre));
					$req->closeCursor();
					
					$erreurs['lemon'] = $lemon_result['result'];
					
					return $erreurs;
				}
				else
				{
					$req = $this->bdd->prepare('UPDATE membres SET LWID=:LWID WHERE ID=:ID_membre');
					$req->execute(array('LWID' => $lemon_result['result']['LWID'],
										'ID_membre' => $ID_membre));
					$req->closeCursor();
				}
			}
			
			if($parrain)
			{
				$parrainages_manager = new ParrainagesMan($this->bdd);
				$parrainages_manager->addParrainage($parrain, $this->getMembreByEmail($email));
				unset($_SESSION['inscription_parrain']);
			}
			
			require_once($_SESSION['dossier_vue'] . '/php/MailInscription.class.php');
			new MailInscription($this->getMembreByID($ID_membre));
			
			$_SESSION['membre_just_inscrit'] = $email;
			
			return true;
		}
		else
			return $erreurs;
	}
	
	public function inscription2($charte, $type, $civilite, $email, $prenom, $nom, $adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng, $ID_ecole, $tel_fixe, $tel_portable, $avatar, $mdp, $mdp2, $date_naissance, $parrain)
	{
		$tel_fixe = $this->format_tel($tel_fixe);
		$tel_portable = $this->format_tel($tel_portable);
		
		if($type == 'student')
			$type = 0;
		else if($type == 'professionnel')
			$type = 1;
		else if($type == 'association')
			$type = 2;
		
		$erreurs = array('charte' => !$charte,
						'email_valide' => !$this->isValidEmail($email),
						'email_existe' => $this->email_exists($email),
						'prenom' => ($type == 0) && !$this->isValidPrenom($prenom),
						'nom' => !$this->isValidNom($nom),
						'adresse_complete' => !$this->isValidAdresse($adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng),
						'tel_fixe' => !$this->isValidTel_fixe($tel_fixe),
						'tel_portable' => !$this->isValidTel_portable($tel_portable),
						'avatar' => $this->handleAvatar($avatar),
						'mdp_correspondance' => $mdp != $mdp2,
						'mdp' => !$this->isValidMdp($mdp),
						'date_naissance' => ($type === 0) && !$this->isValiDate_naissance($date_naissance),
						'parrain' => !$this->isValidParrain($parrain),
						'lemon' => false);
		
		if(!in_array(true, $erreurs))
		{
			if($ID_ecole != -1)
			{
				$ecoles_manager = new EcolesMan($this->bdd);
				$ecole = $ecoles_manager->getEcoleByID($ID_ecole);
			}
			
			if($avatar['error'] != 4)
				$avatar_name = $this->newAvatar($avatar['tmp_name'], $avatar['type']);
			
			$req = $this->bdd->prepare('INSERT INTO membres VALUES(\'\', \'-1\', :type, :civilite, :email, 0, :nom, :prenom, :adresse_complete, :street_number, :route, :locality, :administrative_area_level_1, :country, :postal_code, :lat, :lng, :ID_ecole, :avatar, :mdp, :tel_fixe, :tel_portable, NOW(), 0, :date_naissance, :ID_source, 0, 0, 0, 0, 1)');
			$req->execute(array('type' => $type,
								'civilite' => $civilite,
								'email' => $email,
								'nom' => $nom,
								'prenom' => $prenom,
								'adresse_complete' => $adresse_complete,
								'street_number' => $street_number,
								'route' => $route,
								'locality' => $locality,
								'administrative_area_level_1' => $administrative_area_level_1,
								'country' => $country,
								'postal_code' => $postal_code,
								'lat' => $lat,
								'lng' => $lng,
								'ID_ecole' => empty($ecole) ? '-1' : $ecole->ID,
								'avatar' => isset($avatar_name) ? $avatar_name : '',
								'mdp' => sha1($mdp),
								'tel_fixe' => $tel_fixe,
								'tel_portable' => $tel_portable,
								'date_naissance' => ($type === 0) ? (substr($date_naissance, 6, 4) . '-' . substr($date_naissance, 3, 2) . '-' . substr($date_naissance, 0, 2)) : '',
								'ID_source' => -1));
			$req->closeCursor();
			
			$ID_membre = $this->bdd->lastInsertId();
			
			if(LemonWay::LEMON_DISABLED == false)
			{
				$lemon_result = LemonWay::RegisterWallet(array('wallet' => $ID_membre,
											   'clientMail' => $email,
											   'clientTitle' => $civilite ? 'F' : 'M',
											   'clientFirstName' => $prenom,
											   'clientLastName' => $nom,
											   'street' => $street_number . ' ' . $route,
											   'postCode' => $postal_code,
											   'city' => $locality,
											   'ctry' => strtoupper(substr($country, 0, 3)),
											   'phoneNumber' => $tel_fixe,
											   'mobileNumber' => $tel_portable,
											   'birthdate' => $date_naissance,
											   'isCompany' => $type != 0,
											   'isOneTimeCustomer' => '0'));
				
				if($lemon_result['error'] || empty($lemon_result['result']['LWID']))
				{
					$req = $this->bdd->prepare('DELETE FROM membres WHERE ID=:ID_membre');
					$req->execute(array('ID_membre' => $ID_membre));
					$req->closeCursor();
					
					$erreurs['lemon'] = $lemon_result['result'];
					
					return $erreurs;
				}
				else
				{
					$req = $this->bdd->prepare('UPDATE membres SET LWID=:LWID WHERE ID=:ID_membre');
					$req->execute(array('LWID' => $lemon_result['result']['LWID'],
										'ID_membre' => $ID_membre));
					$req->closeCursor();
				}
			}
			
			if($parrain)
			{
				$parrainages_manager = new ParrainagesMan($this->bdd);
				$parrainages_manager->addParrainage($parrain, $this->getMembreByEmail($email));
				unset($_SESSION['inscription_parrain']);
			}
			
			require_once($_SESSION['dossier_vue'] . '/php/MailInscription.class.php');
			new MailInscription($this->getMembreByID($ID_membre));
			
			$_SESSION['membre_just_inscrit'] = $email;
			
			return true;
		}
		else
			return $erreurs;
	}
	
	public function email_exists($email)
	{
		$req = $this->bdd->prepare('SELECT ID FROM membres WHERE email=:email AND actif=1');
		$req->execute(array('email' => $email));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function champ_exists($champ, $valeur)
	{
		$req = $this->bdd->prepare('SELECT ID FROM membres WHERE ' . $champ . '=?');
		$req->execute(array($valeur));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function getSources()
	{
		$req = $this->bdd->query('SELECT * FROM sources');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Source');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function updateEmail(Membre $membre_email, $email)
	{
		$erreurs['email_existe'] = false;//$this->champ_exists('email', $email);
		$erreurs['email_valide'] = !$this->isValidEmail($email);
		
		if(!in_array(true, $erreurs))
		{
			$lemon = LemonWay::UpdateWalletDetails(array('wallet' => $membre_email->ID,
														 'newEmail' => $email));
			
			if($lemon['error'])
				$erreurs['email_lemon'] = $lemon['result'];
			else
			{
				$req = $this->bdd->prepare('UPDATE membres SET email=:email WHERE ID=:ID');
				$req->execute(array('email' => $email,
									'ID' => $membre_email->ID));
				$req->closeCursor();
				
				//require_once($_SESSION['dossier_vue'] . '/php/MailModificationEmail.class.php');
				//new MailModificationEmail($membre_email);
				
				$_SESSION['membre'] = $this->getMembreByID($membre_email->ID);
				
				return true;
			}
		}
		
		return $erreurs;
	}
	
	public function updateAdresse(Membre $membre_adresse_complete, $adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng)
	{
		$erreurs['adresse_valide'] = !$this->isValidAdresse($adresse_complete, $street_number, $route, $locality, $administrative_area_level_1, $country, $postal_code, $lat, $lng);
		$erreurs['adresse_lemon'] = false;
		
		if(!in_array(true, $erreurs))
		{
			$lemon = LemonWay::UpdateWalletDetails(array('wallet' => $membre_adresse_complete->ID,
												'newStreet' => $street_number . ' ' . $route,
												'newPostCode' => $postal_code,
												'newCity' => $locality,
												'newCtry' => strtoupper(substr($country, 0, 3))));
			
			if($lemon['error'])
				$erreurs['adresse_lemon'] = $lemon['result'];
			else
			{
				$req = $this->bdd->prepare('UPDATE membres SET adresse_complete=:adresse_complete, street_number=:street_number, route=:route, locality=:locality, administrative_area_level_1=:administrative_area_level_1, country=:country, postal_code=:postal_code, lat=:lat, lng=:lng WHERE ID=:ID');
				$req->execute(array('adresse_complete' => $adresse_complete,
									'street_number' => $street_number,
									'route' => $route,
									'locality' => $locality,
									'administrative_area_level_1' => $administrative_area_level_1,
									'country' => $country,
									'postal_code' => $postal_code,
									'lat' => $lat,
									'lng' => $lng,
									'ID' => $membre_adresse_complete->ID));
				$req->closeCursor();
				
				$_SESSION['membre'] = $this->getMembreByID($membre_adresse_complete->ID);
				
				return true;
			}
		}
		
		return $erreurs;
	}
	
	public function updateTel_fixe(Membre $membre_tel_fixe, $tel_fixe)
	{
		$tel_fixe = $this->format_tel($tel_fixe);
		$erreurs['tel_fixe_valide'] = !$this->isValidTel_fixe($tel_fixe);
		$erreurs['tel_fixe_lemon'] = false;
		
		if(!in_array(true, $erreurs))
		{
			$lemon = LemonWay::UpdateWalletDetails(array('wallet' => $membre_tel_fixe->ID, 'newPhoneNumber' => $tel_fixe));
			
			if($lemon['error'])
				$erreurs['tel_fixe_lemon'] = $lemon['result'];
			else
			{
				$req = $this->bdd->prepare('UPDATE membres SET tel_fixe=:tel_fixe WHERE ID=:ID');
				$req->execute(array('tel_fixe' => $tel_fixe,
									'ID' => $membre_tel_fixe->ID));
				$req->closeCursor();
				
				$_SESSION['membre'] = $this->getMembreByID($membre_tel_fixe->ID);
				
				return true;
			}
		}
		
		return $erreurs;
	}
	
	public function updateTel_portable(Membre $membre_tel_portable, $tel_portable)
	{
		$tel_portable = $this->format_tel($tel_portable);
		$erreurs['tel_portable_valide'] = !$this->isValidTel_portable($tel_portable);
		$erreurs['tel_portable_lemon'] = false;
		
		if(!in_array(true, $erreurs))
		{
			$lemon = LemonWay::UpdateWalletDetails(array('wallet' => $membre_tel_portable->ID, 'newMobileNumber' => $tel_portable));
			
			if($lemon['error'])
				$erreurs['tel_portable_lemon'] = $lemon['result'];
			else
			{
				$req = $this->bdd->prepare('UPDATE membres SET tel_portable=:tel_portable WHERE ID=:ID');
				$req->execute(array('tel_portable' => $tel_portable,
									'ID' => $membre_tel_portable->ID));
				$req->closeCursor();
				
				$_SESSION['membre'] = $this->getMembreByID($membre_tel_portable->ID);
				
				return true;
			}
		}
		
		return $erreurs;
	}
	
	public function updateMdp(Membre $membre_mdp, $mdp1, $mdp2, $mdp_verif)
	{
		$erreurs['mdp_verif'] = ($membre_mdp->mdp != sha1($mdp_verif));
		$erreurs['mdp_correspondance'] = ($mdp1 != $mdp2);
		$erreurs['mdp_valide'] = !$this->isValidMdp($mdp1);
		
		if(!in_array(true, $erreurs))
		{
			$req = $this->bdd->prepare('UPDATE membres SET mdp=:mdp WHERE ID=:ID');
			$req->execute(array('mdp' => sha1($mdp1),
								'ID' => $membre_mdp->ID));
			$req->closeCursor();
			
			$_SESSION['membre'] = $this->getMembreByID($membre_mdp->ID);
			
			return true;
		}
		
		return $erreurs;
	}
	
	public function getMembresByDebutPseudo($ID_membre, $pseudo)
	{
		$req = $this->bdd->prepare('SELECT ID, nom, prenom, email FROM membres WHERE CONCAT(nom, \' \', prenom) LIKE :pseudo OR CONCAT(prenom, \' \', nom) LIKE :pseudo AND ID!=:ID');
		$req->execute(array('pseudo' => $pseudo . '%',
							'ID' => $ID_membre));
		$membres = $req->fetchAll();
		$req->closeCursor();
		
		return $membres;
	}
	
	public function getMembresByDebutPseudoPourMessagerie($ID_conversation, $pseudo)
	{
		$req = $this->bdd->prepare('SELECT ID, nom, prenom, email FROM membres m WHERE ID NOT IN (SELECT ID_membre FROM messagerie_membres WHERE ID_conversation=:ID_conversation) AND (CONCAT(nom, \' \', prenom) LIKE :pseudo OR CONCAT(prenom, \' \', nom) LIKE :pseudo)');
		$req->execute(array('pseudo' => $pseudo . '%',
							'ID_conversation' => $ID_conversation));
		$membres = $req->fetchAll();
		$req->closeCursor();
		
		return $membres;
	}
	
	public function addSource(Membre $membre, $source)
	{
		if($membre->administrateur)
		{
			$req = $this->bdd->prepare('INSERT INTO sources VALUES(\'\', :source)');
			$req->execute(array('source' => $source));
			$req->closeCursor();
		}
	}
	
	public function delSource(Membre $membre, $ID_source)
	{
		if($membre->administrateur)
		{
			$req = $this->bdd->prepare('DELETE FROM sources WHERE ID=:ID');
			$req->execute(array('ID' => $ID_source));
			$req->closeCursor();
		}
	}
	
	public function changeSource(Membre $membre, $ID_source, $new_name)
	{
		if($membre->administrateur)
		{
			$req = $this->bdd->prepare('UPDATE sources SET source=:source WHERE ID=:ID');
			$req->execute(array('ID' => $ID_source,
								'source' => $new_name));
			$req->closeCursor();
		}
	}
	
	public function updateTDR(Membre $membre, $TDR)
	{
		$TDR_value = (((double) $membre->TDR_value) * ((double) $membre->TDR_nombre) + ((double) $TDR))/(((double) $membre->TDR_nombre) + 1.0);
		
		$req = $this->bdd->prepare('UPDATE membres SET TDR_value=:TDR_value, TDR_nombre=:TDR_nombre WHERE ID=:ID');
		$req->execute(array('ID' => $membre->ID,
							'TDR_value' => $TDR_value,
							'TDR_nombre' => $membre->TDR_nombre + 1));
		$req->closeCursor();
		
		return $this->getMembreByID($membre->ID);
	}
	
	public function getTransactionsEffectueesMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM transactions WHERE ID_locataire=:ID_locataire AND reponse=1 AND annulation=0 AND t.date_fin_loc < NOW()');
		$req->execute(array('ID_locataire' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return (int) $donnee[0];
	}
	
	public function getTransactionsAnnoncesPubliees($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM objets WHERE ID_proprio=:ID_proprio AND actif=1');
		$req->execute(array('ID_proprio' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return (int) $donnee[0];
	}
	
	public function getPourcentageReponses($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT t.* FROM transactions t INNER JOIN objets o ON t.ID_objet=o.ID WHERE o.ID_proprio=:ID_proprio AND t.date_debut_loc < CURDATE() AND t.annulation=0');
		$req->execute(array('ID_proprio' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Transaction');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		if(empty($donnees))
			return -1;
		
		$nbr_reponse = 0;
		
		foreach($donnees as $transaction)
		{
			if($transaction->reponse != 2)
				$nbr_reponse++;
			else
			{
				$req = $this->bdd->prepare('SELECT ID_membre FROM transactions_dates WHERE ID_transaction=:ID_transaction ORDER BY date_proposition DESC LIMIT 0, 1');
				$req->execute(array('ID_transaction' => $transaction->ID));
				$reponse = $req->fetch();
				$req->closeCursor();
				
				if($reponse['ID_membre'] == $ID_membre)
					$nbr_reponse++;
			}
		}
		
		return ((double) $nbr_reponse) / ((double) count($donnees));
	}
	
	public function removeAvatar(Membre $membre)
	{
		if($membre->avatar != '' && $membre->avatar != '/')
		{
			unlink('avatars/' . $membre->avatar);
			
			$req = $this->bdd->prepare('UPDATE membres SET avatar=\'\' WHERE ID=:ID');
			$req->execute(array('ID' => $membre->ID));
			$req->closeCursor();
		}
	}
	
	public function modifyAvatar(Membre $membre, $avatar)
	{
		if($avatar['error'] == 4)
			return 'aucun fichier reçu';
		
		$erreur = $this->handleAvatar($avatar);
		
		if($erreur == false)
		{
			if($membre->avatar != '' && $membre->avatar != '/')
				unlink('avatars/' . $membre->avatar);
			
			$req = $this->bdd->prepare('UPDATE membres SET avatar=:avatar WHERE ID=:ID');
			$req->execute(array('ID' => $membre->ID,
								'avatar' => $this->newAvatar($avatar['tmp_name'], $avatar['type'])));
			$req->closeCursor();
		}
		
		return $erreur;
	}
	
	public function addNote($ID_membre, $ID_transaction, $ID_noteur, $note)
	{
		if($this->getNote($ID_transaction, $ID_noteur) === false)
		{
			$req = $this->bdd->prepare('INSERT INTO membres_notes VALUES(\'\', :ID_membre, :ID_transaction, :ID_noteur, :note, NOW())');
			$req->execute(array('ID_membre' => $ID_membre,
								'ID_transaction' => $ID_transaction,
								'ID_noteur' => $ID_noteur,
								'note' => $note));
			$req->closeCursor();
		}
	}
	
	public function getNote($ID_transaction, $ID_noteur)
	{
		$req = $this->bdd->prepare('SELECT note FROM membres_notes WHERE ID_transaction=:ID_transaction AND ID_noteur=:ID_noteur');
		$req->execute(array('ID_transaction' => $ID_transaction,
							'ID_noteur' => $ID_noteur));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		if(empty($donnee))
			return false;
		
		return $donnee['note'];
	}
	
	public function getNoteMean($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT SUM(note) s, COUNT(*) c FROM membres_notes WHERE ID_membre=:ID_membre');
		$req->execute(array('ID_membre' => $ID_membre));
		$donnees = $req->fetch();
		$req->closeCursor();
		
		if($donnees['c'] == 0)
			return -1;
		
		return ((double) $donnees['s'])/((double) $donnees['c']);
	}
	
	public function valider_email($ID_membre, $hash)
	{
		$req = $this->bdd->prepare('SELECT email, date_inscription FROM membres WHERE ID=?');
		$req->execute(array($ID_membre));
		$donnees = $req->fetch();
		$req->closeCursor();
		
		if($hash == $this->hasher($ID_membre, $donnees['email'], $donnees['date_inscription']))
		{
			$req = $this->bdd->prepare('UPDATE membres SET email_valide=1 WHERE ID=?');
			$req->execute(array($ID_membre));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('SELECT * FROM membres WHERE ID=?');
			$req->execute(array($ID_membre));
			$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
			$donnee = $req->fetch();
			$req->closeCursor();
			
			return $donnee;
		}
		else
			return false;
	}
	
	static public function hasher($ID_membre, $email, $date_inscription)
	{
		return substr(sha1(sha1($ID_membre . $date_inscription) . md5($email . $date_inscription)), 3, 32);
	}
	
	public function renvoyerMailInscription($email)
	{
		$membre_email = $this->getMembreByEmail($email);
		
		if(!empty($membre_email) && $membre_email->email_valide == 0)
		{
			require_once($_SESSION['dossier_vue'] . '/php/MailInscription.class.php');
			new MailInscription($membre_email);
		}
	}
	
	public function supprimerCompte($ID_membre)
	{
		$lemon = LemonWay::UpdateWalletStatus(array('wallet' => $ID_membre, 'newStatus' => '12'));
		
		if($lemon['error'])
			return $lemon['result'];
		
		$req = $this->bdd->prepare('UPDATE membres SET actif=0 WHERE ID=:ID');
		$req->execute(array('ID' => $ID_membre));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('DELETE FROM membres_notes WHERE ID_membre=:ID');
		$req->execute(array('ID' => $ID_membre));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('DELETE FROM membres2communautes WHERE ID_membre=:ID');
		$req->execute(array('ID' => $ID_membre));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('DELETE FROM parrainages WHERE ID_parrain=:ID OR ID_filleul=:ID');
		$req->execute(array('ID' => $ID_membre));
		$req->closeCursor();
		
		return false;
	}
	
	public function oubli_mdp($email)
	{
		$membre_email = $this->getMembreByEmail($email);
		
		if(!empty($membre_email) && $membre_email->actif)
		{
			$chars = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
			$mdp = '';
			
			for($i=0; $i<MembresMan::TAILLE_OUBLI_MDP; $i++)
				$mdp .= $chars[mt_rand(0, strlen($chars) - 1)];
			
			$req = $this->bdd->prepare('UPDATE membres SET mdp=:mdp WHERE ID=:ID');
			$req->execute(array('ID' => $membre_email->ID,
								'mdp' => sha1($mdp)));
			$req->closeCursor();
			
			require_once($_SESSION['dossier_vue'] . '/php/MailOubliMdp.class.php');
			new MailOubliMdp($membre_email, $mdp);
			
			return $membre_email;
		}
		
		return false;
	}
	
	public function countMembres($actif = 1)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM membres WHERE actif=:actif');
		$req->execute(array('actif' => $actif ? 1 : 0));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
}