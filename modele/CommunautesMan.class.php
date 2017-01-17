<?php

class CommunautesMan
{
	protected $bdd;
	protected $extensions_autorisees = array('jpg','gif','png','jpeg');
	
	const PHOTO_MAX_LARGEUR = 1000;
	const PHOTO_MAX_HAUTEUR = 400;
	const TAILLE_MIN_NOM = 3;
	const TAILLE_MAX_NOM = 20;
	const TAILLE_MIN_DESCRIPTION = 20;
	const TAILLE_MAX_DESCRIPTION = 400;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getCommunautes()
	{
		$req = $this->bdd->query('SELECT * FROM communautes');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getMessagesByCommunaute($ID_communaute, $offset, $nombre)
	{
		$req = $this->bdd->prepare('SELECT * FROM communautes_messages WHERE ID_communaute=:ID_communaute ORDER BY date_creation DESC LIMIT ' . (int) $offset . ',' . (int) $nombre);
		$req->execute(array('ID_communaute' => $ID_communaute));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute_message');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function countMessagesByCommunaute($ID_communaute)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM communautes_messages WHERE ID_communaute=:ID_communaute');
		$req->execute(array('ID_communaute' => $ID_communaute));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
	
	public function addMessage($ID_communaute, $ID_membre, $message)
	{
		$req = $this->bdd->prepare('INSERT INTO communautes_messages VALUES(\'\', :ID_communaute, :ID_membre, :message, NOW())');
		$req->execute(array('ID_communaute' => $ID_communaute,
							'ID_membre' => $ID_membre,
							'message' => $message));
		$req->closeCursor();
	}
	
	public function getPropositions()
	{
		$req = $this->bdd->query('SELECT * FROM communautes_propositions');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute_proposition');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function countNotifsPropositions()
	{
		$req = $this->bdd->query('SELECT COUNT(*) c FROM communautes_propositions WHERE vu=0');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function countNotificationsMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) c FROM membres2communautes WHERE vu=0 AND ID_membre=:ID_membre AND date_validation!=0');
		$req->execute(array('ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function isNotifClubByMembre($ID_membre, $ID_club)
	{
		$req = $this->bdd->prepare('SELECT ID FROM membres2communautes WHERE vu=0 AND ID_membre=:ID_membre AND ID_communaute=:ID_communaute AND date_validation!=0');
		$req->execute(array('ID_membre' => $ID_membre,
							'ID_communaute' => $ID_club));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function delNotifClubByMembre($ID_membre, $ID_club)
	{
		$req = $this->bdd->prepare('UPDATE membres2communautes SET vu=1 WHERE ID_membre=:ID_membre AND ID_communaute=:ID_communaute AND date_validation!=0');
		$req->execute(array('ID_membre' => $ID_membre,
							'ID_communaute' => $ID_club));
		$req->closeCursor();
	}
	
	public function getPropositionByID($ID_proposition)
	{
		$req = $this->bdd->prepare('SELECT * FROM communautes_propositions WHERE ID=:ID');
		$req->execute(array('ID' => $ID_proposition));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute_proposition');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function suspendre_notification($ID_proposition)
	{
		$req = $this->bdd->prepare('UPDATE communautes_propositions SET vu=1 WHERE ID=:ID');
		$req->execute(array('ID' => $ID_proposition));
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function remettre_notification($ID_proposition)
	{
		$req = $this->bdd->prepare('UPDATE communautes_propositions SET vu=0 WHERE ID=:ID');
		$req->execute(array('ID' => $ID_proposition));
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getCommunautesByMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT c.* FROM communautes c INNER JOIN membres2communautes m ON c.ID=m.ID_communaute WHERE m.ID_membre=:ID_membre AND m.date_validation!=0');
		$req->execute(array('ID_membre' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getCommunauteByID($ID_communaute)
	{
		$req = $this->bdd->prepare('SELECT * FROM communautes WHERE ID=:ID');
		$req->execute(array('ID' => $ID_communaute));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function isMembre($ID_communaute, $ID_membre)
	{
		$req = $this->bdd->prepare('SELECT ID FROM membres2communautes WHERE ID_communaute=:ID_communaute AND ID_membre=:ID_membre AND date_validation!=0');
		$req->execute(array('ID_communaute' => $ID_communaute,
							'ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function isMemberOrPendingMember($ID_communaute, $ID_membre)
	{
		$req = $this->bdd->prepare('SELECT date_validation FROM membres2communautes WHERE ID_communaute=:ID_communaute AND ID_membre=:ID_membre');
		$req->execute(array('ID_communaute' => $ID_communaute,
							'ID_membre' => $ID_membre));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		if(empty($donnee))
			return false;
		
		if($donnee['date_validation'] == 0)
			return 1;
		
		return 2;
	}
	
	public function accept($ID_communaute, $ID_membre)
	{
		if($this->isMemberOrPendingMember($ID_communaute, $ID_membre) == 1)
		{
			$req = $this->bdd->prepare('UPDATE membres2communautes SET date_validation=NOW() WHERE ID_communaute=:ID_communaute AND ID_membre=:ID_membre');
			$req->execute(array('ID_communaute' => $ID_communaute,
								'ID_membre' => $ID_membre));
			$req->closeCursor();
			
			return true;
		}
		
		return false;
	}
	
	public function countDemandesPendingByCommunaute($ID_communaute)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) c FROM membres2communautes WHERE ID_communaute=:ID_communaute AND date_validation=0');
		$req->execute(array('ID_communaute' => $ID_communaute));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function countMembersByCommunaute($ID_communaute)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) c FROM membres2communautes WHERE ID_communaute=:ID_communaute AND date_validation!=0');
		$req->execute(array('ID_communaute' => $ID_communaute));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function countAnnoncesByCommunaute($ID_communaute)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) c FROM objets WHERE ID_club=:ID_communaute AND actif=1 AND deleted=0');
		$req->execute(array('ID_communaute' => $ID_communaute));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function addMembre($ID_communaute, $ID_membre)
	{
		if($this->isMemberOrPendingMember($ID_communaute, $ID_membre) == 0)
		{
			$req = $this->bdd->prepare('INSERT INTO membres2communautes VALUES(\'\', :ID_membre, :ID_communaute, NOW(), 0, 0)');
			$req->execute(array('ID_membre' => $ID_membre,
								'ID_communaute' => $ID_communaute));
			$req->closeCursor();
			
			return false;
		}
		
		return true;
	}
	
	public function isValidNom($nom)
	{
		$l = strlen($nom);
		return ($l >= self::TAILLE_MIN_NOM && $l <= self::TAILLE_MAX_NOM);
	}
	
	public function isValidDescription($description)
	{
		$l = strlen($description);
		return ($l >= self::TAILLE_MIN_DESCRIPTION && $l <= self::TAILLE_MAX_DESCRIPTION);
	}
	
	public function isValidImage($image)
	{
		return false;
	}
	
	public function newImage($name, $tmp_name, $type)
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
			$infos = pathinfo($name);
			$compteur = 0;
			
			do
			{
				if($compteur == 0)
					$path = $infos['filename'] . '.' . $extension;
				else
					$path = $infos['filename'] . ' (' . $compteur . ').' . $extension;
				
				$compteur++;
			}
			while(file_exists(IMAGES_COMMUNAUTES . '/' . $path));
				
			require_once('fonctions/redimensionnement.php');
			$miniature = redimensionner($tmp_name, $extension, self::PHOTO_MAX_LARGEUR, self::PHOTO_MAX_HAUTEUR);
			
			switch($extension)
			{
				case 'jpg':
					imagejpeg($miniature, IMAGES_COMMUNAUTES . $path, 100);
					break;
				
				case 'png':
					imagepng($miniature, IMAGES_COMMUNAUTES . $path, 9);
					break;
				
				case 'gif':
					imagegif($miniature, IMAGES_COMMUNAUTES . $path, 100);
					break;
			}
			
			return $path;
		}
		else
			return '';
	}
	
	public function handleImage($image)
	{
		if($image['error'] == 4)
			return false;
		
		$extension = pathinfo($image['name'], PATHINFO_EXTENSION);
		
		if(in_array(strtolower($extension), $this->extensions_autorisees))
		{
			$infosImg = getimagesize($image['tmp_name']);
			
			if($infosImg[2] >= 1 && $infosImg[2] <= 14)
			{
				if(isset($image['error']) && UPLOAD_ERR_OK === $image['error'])
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
	
	public function proposerCommunaute($ID_membre, $nom, $description, $image)
	{
		$erreurs = array('nom' => !$this->isValidNom($nom),
						'description' => !$this->isValidDescription($description),
						'image' => $this->handleImage($image));
		
		if(!in_array(true, $erreurs))
		{
			if($image['error'] != 4)
				$image_name = $this->newImage($image['name'], $image['tmp_name'], $image['type']);
			
			$req = $this->bdd->prepare('INSERT INTO communautes_propositions VALUES(\'\', :ID_membre, :nom, :image, :description, NOW(), 0)');
			$req->execute(array('ID_membre' => $ID_membre,
								'nom' => $nom,
								'image' => isset($image_name) ? $image_name : '',
								'description' => $description));
			$req->closeCursor();
			
			return $this->bdd->lastInsertId();
		}
		
		return $erreurs;
	}
	
	public function handleImageChoix($image_choix, $image_new)
	{
		if($image_choix == 'same' && $image_new['error'] == 4)
			return false;
		else if($image_choix == 'new' && $image_new['error'] != 4)
			return $this->handleImage($image_new);
		else
			return 'image envoyée alors que vous souhaitez conserver l\'image proposée';
	}
	
	public function addCommunaute($proposition, $nom, $description, $image_choix, $image_new)
	{
		$erreurs = array('nom' => !$this->isValidNom($nom),
						'description' => !$this->isValidDescription($description),
						'image' => $this->handleImageChoix($image_choix, $image_new));
		
		if(!in_array(true, $erreurs))
		{
			if($image_choix == 'new')
			{
				$image_name = $this->newImage($image_new['name'], $image_new['tmp_name'], $image_new['type']);
				unlink(IMAGES_COMMUNAUTES . $proposition->image);
			}
			else
				$image_name = $proposition->image;
			
			$req = $this->bdd->prepare('INSERT INTO communautes VALUES(\'\', :nom, :image, :description, NOW())');
			$req->execute(array('nom' => $nom,
								'image' => $image_name,
								'description' => $description));
			$req->closeCursor();
			
			$ID_communaute = $this->bdd->lastInsertId();
			$membres_manager = new MembresMan($this->bdd);
			$membre_proposition = $membres_manager->getMembreByID($proposition->ID_membre);
			
			if(!empty($membre_proposition) && $membre_proposition->actif == 1)
			{
				$req = $this->bdd->prepare('INSERT INTO membres2communautes VALUES(\'\', :ID_membre, :ID_communaute, NOW(), NOW(), 0)');
				$req->execute(array('ID_membre' => $proposition->ID_membre,
									'ID_communaute' => $ID_communaute));
				$req->closeCursor();
			}
			
			$req = $this->bdd->prepare('DELETE FROM communautes_propositions WHERE ID=:ID');
			$req->execute(array('ID' => $proposition->ID));
			$req->closeCursor();
			
			return $ID_communaute;
		}
		
		return $erreurs;
	}
	
	public function getClubsAccueil()
	{
		$req = $this->bdd->query('SELECT c.* FROM communautes c INNER JOIN clubs_accueil a ON a.ID_club=c.ID ORDER BY a.ordre DESC LIMIT ' . (int) NOMBRE_CLUBS_ACCUEIL);
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Communaute');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getClubsAccueilTable()
	{
		$req = $this->bdd->query('SELECT * FROM clubs_accueil ORDER BY ordre DESC');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function addClubAccueil($ID_club)
	{
		$club = $this->getCommunauteByID($ID_club);
		
		if(!empty($club))
		{
			$req = $this->bdd->prepare('INSERT INTO clubs_accueil VALUES(\'\', :ID_objet, 0)');
			$req->execute(array('ID_objet' => $ID_club));
			$req->closeCursor();
			
			return true;
		}
		
		return false;
	}
	
	public function delClubAccueil($ID)
	{
		$req = $this->bdd->prepare('DELETE FROM clubs_accueil WHERE ID=:ID');
		$req->execute(array('ID' => $ID));
		$req->closeCursor();
	}
	
	public function modifyClubAccueil($ID, $ordre)
	{
		$req = $this->bdd->prepare('UPDATE clubs_accueil SET ordre=:ordre WHERE ID=:ID');
		$req->execute(array('ID' => $ID,
							'ordre' => $ordre));
		$req->closeCursor();
	}
}