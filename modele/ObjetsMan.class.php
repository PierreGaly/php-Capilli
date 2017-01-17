<?php

class ObjetsMan
{
	protected $bdd;
	
	const DEFAULT_MAP_LAT = 46.227638;
	const DEFAULT_MAP_LNG = 2.213749;
	const RADIUS_MAX = 20000;
	const TAILLE_MIN_TITRE = 1;
	const TAILLE_MAX_TITRE = 25;
	const TAILLE_MAX_MARQUE = 255;
	const TAILLE_MAX_MODELE = 255;
	const NB_MAX_OBJETS = 999;
	const TAILLE_MAX_PHOTO = 10485760;//10 Mio
	const PHOTO_MAX_LARGEUR = 1000;
	const PHOTO_MAX_HAUTEUR = 400;
	const PRIX_MIN = 1;
	const PRIX_MAX = 5000;
	const DEFAULT_PHOTO_PATH = 'sources/appareil_photo.png';

	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function searchObjets2($latitude, $longitude, $nom_categorie, $nom_sous_categorie)
	{
		if($latitude === false || $longitude === false)
		{
			// query without geolocalisation
			if(empty($nom_sous_categorie))
			{
				// query selecting by categorie
				$req = $this->bdd->prepare('SELECT * FROM objets WHERE deleted=0 AND ID_sous_categorie IN (SELECT ID FROM sous_categories WHERE ID_club=-1 AND deleted=0 AND ID_categorie=(SELECT ID FROM categories WHERE nom=:nom_categorie)) AND actif=1 ORDER BY prix_journee');
				$req->execute(array('nom_categorie' => $nom_categorie));
			}
			else
			{
				// query selecting by sous_categorie
				$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_club=-1 AND deleted=0 AND ID_sous_categorie=(SELECT ID FROM sous_categories WHERE nom=:nom_sous_categorie) AND actif=1 ORDER BY prix_journee');
				$req->execute(array('nom_sous_categorie' => $nom_sous_categorie));
			}
		}
		else
		{
			// query with geolocalisation
			$dist = '6367445*acos(cos(radians(:latitude))*cos(radians(m.lat))*cos(radians(m.lng)-radians(:longitude)) + sin(radians(:latitude))*sin(radians(m.lat)))';
			
			if(empty($nom_sous_categorie))
			{
				// query selecting by categorie
				$req = $this->bdd->prepare('SELECT o.*, (' . $dist . ') AS dist FROM objets o INNER JOIN membres m ON m.ID = o.ID_proprio WHERE o.ID_club=-1 AND o.deleted=0 AND o.ID_sous_categorie IN (SELECT ID FROM sous_categories WHERE ID_categorie=(SELECT ID FROM categories WHERE nom=:nom_categorie)) AND o.actif=1 HAVING dist < :radius_max ORDER BY dist');
				$req->execute(array('latitude' => $latitude,
									'longitude' => $longitude,
									'nom_categorie' => $nom_categorie,
									'radius_max' => self::RADIUS_MAX));
			}
			else
			{
				// query selecting by sous_categorie
				$req = $this->bdd->prepare('SELECT o.*, (' . $dist . ') AS dist FROM objets o INNER JOIN membres m ON m.ID = o.ID_proprio WHERE o.ID_club=-1 AND o.deleted=0 AND o.ID_sous_categorie=(SELECT ID FROM sous_categories WHERE nom=:nom_sous_categorie) AND o.actif=1 HAVING dist < :radius_max ORDER BY dist');
				$req->execute(array('latitude' => $latitude,
									'longitude' => $longitude,
									'nom_sous_categorie' => $nom_sous_categorie,
									'radius_max' => self::RADIUS_MAX));
			}
		}
		
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function searchObjets($latitude, $longitude, $ID_categorie, $ID_sous_categorie, $ID_club, $mot_cle)
	{
		$mot_cle = trim($mot_cle);
		
		if($latitude === false || $longitude === false)
		{
			// query without geolocalisation
			if($ID_sous_categorie > 0) // query selecting by sous_categorie
			{
				$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_club=:ID_club AND deleted=0 AND actif=1 AND ID_sous_categorie=:ID_sous_categorie AND (nom LIKE :mot_cle OR description LIKE :mot_cle) ORDER BY prix_journee');
				$req->execute(array('ID_club' => $ID_club,
									'ID_sous_categorie' => $ID_sous_categorie,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
			else if($ID_categorie > 0) // query selecting by categorie
			{
				$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_club=:ID_club AND deleted=0 AND actif=1 AND ID_sous_categorie IN (SELECT ID FROM sous_categories WHERE ID_categorie=:ID_categorie) AND (nom LIKE :mot_cle OR description LIKE :mot_cle) ORDER BY prix_journee');
				$req->execute(array('ID_club' => $ID_club,
									'ID_categorie' => $ID_categorie,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
			else
			{
				$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_club=:ID_club AND deleted=0 AND actif=1 AND (nom LIKE :mot_cle OR description LIKE :mot_cle) ORDER BY prix_journee');
				$req->execute(array('ID_club' => $ID_club,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
		}
		else
		{
			// query with geolocalisation
			$dist = '6367445*acos(cos(radians(:latitude))*cos(radians(m.lat))*cos(radians(m.lng)-radians(:longitude)) + sin(radians(:latitude))*sin(radians(m.lat)))';
			
			if($ID_sous_categorie > 0) // query selecting by sous_categorie
			{
				$req = $this->bdd->prepare('SELECT o.*, (' . $dist . ') AS dist FROM objets o INNER JOIN membres m ON m.ID=o.ID_proprio WHERE o.ID_club=:ID_club AND o.deleted=0 AND o.actif=1 AND o.ID_sous_categorie=:ID_sous_categorie AND (o.nom LIKE :mot_cle OR o.description LIKE :mot_cle) HAVING dist < :radius_max ORDER BY dist');
				$req->execute(array('ID_club' => $ID_club,
									'latitude' => $latitude,
									'longitude' => $longitude,
									'ID_sous_categorie' => $ID_sous_categorie,
									'radius_max' => self::RADIUS_MAX,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
			else if($ID_categorie > 0) // query selecting by categorie
			{
				$req = $this->bdd->prepare('SELECT o.*, (' . $dist . ') AS dist FROM objets o INNER JOIN membres m ON m.ID=o.ID_proprio WHERE o.ID_club=:ID_club AND o.deleted=0 AND o.actif=1 AND o.ID_sous_categorie IN (SELECT ID FROM sous_categories WHERE ID_categorie=:ID_categorie) AND (o.nom LIKE :mot_cle OR o.description LIKE :mot_cle) HAVING dist < :radius_max ORDER BY dist');
				$req->execute(array('ID_club' => $ID_club,
									'latitude' => $latitude,
									'longitude' => $longitude,
									'ID_categorie' => $ID_categorie,
									'radius_max' => self::RADIUS_MAX,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
			else
			{
				$req = $this->bdd->prepare('SELECT o.*, (' . $dist . ') AS dist FROM objets o INNER JOIN membres m ON m.ID=o.ID_proprio WHERE o.ID_club=:ID_club AND o.deleted=0 AND o.actif=1 AND (o.nom LIKE :mot_cle OR o.description LIKE :mot_cle) HAVING dist < :radius_max ORDER BY dist');
				$req->execute(array('ID_club' => $ID_club,
									'latitude' => $latitude,
									'longitude' => $longitude,
									'radius_max' => self::RADIUS_MAX,
									'mot_cle' => '%' . $mot_cle . '%'));
			}
		}
		
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getProprio($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM membres WHERE ID=:ID');
		$req->execute(array('ID' => $id));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getByID($id, $deleted = 0)
	{
		$chaine = '';
		
		if($deleted == 0)
			$chaine = ' AND deleted=0';
		else if($deleted == 1)
			$chaine = ' AND deleted=1';
		
		$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID=:ID' . $chaine);
		$req->execute(array('ID' => $id));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function getByProprio($id_proprio, $actif, $limit = -1)
	{
		$chaine = '';
		$chaine2 = '';
		
		if($actif == 0)
			$chaine2 = ' AND actif=0 ';
		else if($actif == 1)
			$chaine2 = ' AND actif=1 ';
		
		if($limit >= 0)
			$chaine = ' LIMIT 0, 3';
		
		$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_proprio=:ID_proprio AND deleted=0 ' . $chaine2 . 'ORDER BY date_creation DESC' . $chaine);
		$req->execute(array('ID_proprio' => $id_proprio));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getByClub($ID_club, $nombre = 0)
	{
		$chaine = '';
		
		if($nombre > 0)
			$chaine = ' LIMIT ' . ((int) $nombre);
		
		$req = $this->bdd->prepare('SELECT * FROM objets WHERE ID_club=:ID_club AND deleted=0 ORDER BY date_creation DESC' . $chaine);
		$req->execute(array('ID_club' => $ID_club));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function prixLocation(Objet $objet, $date_debut, $date_fin)
	{
		$d_debut = date_create_from_format('j/m/Y', $date_debut);
		$d_fin = date_create_from_format('j/m/Y', $date_fin);
		
		if(!$d_debut || !$d_fin)
			return false;
		
		$nbr_mois = 0;
		$nbr_semaines = 0;
		$nbr_jours = $d_fin->diff($d_debut, false)->days + 1;
		
		if($objet->prix_weekend > 0 && $nbr_jours == 2 && $d_debut->format('N') == 6 && $d_fin->format('N') == 7)
			return $objet->prix_weekend;
		
		if($objet->prix_mois > 0)
			$nbr_mois = floor($nbr_jours/30);
		
		$nbr_jours -= 30*$nbr_mois;
		
		if($objet->prix_semaine > 0)
			$nbr_semaines = floor($nbr_jours/7);
		
		$nbr_jours -= 7*$nbr_semaines;
		
		return $nbr_mois*$objet->prix_mois + $nbr_semaines*$objet->prix_semaine + $nbr_jours*$objet->prix_journee;
	}
	
	public function isValidTitre($titre)
	{
		$l = strlen($titre);
		return ($l >= self::TAILLE_MIN_TITRE && $l <= self::TAILLE_MAX_TITRE);
	}
	
	public function isValidSous_categorie($ID_sous_categorie)
	{
		$SousCategories_manager = new SousCategoriesMan($this->bdd);
		$sous_categorie = $SousCategories_manager->getSousCategorieByID($ID_sous_categorie);
		
		return !empty($sous_categorie);
	}
	
	public function isValidDescription($description)
	{
		return !empty($description);
	}
	
	public function isValidLocation($conditions_location)
	{
		return true;
	}
	
	public function isValidUtilisation($conditions_utilisation)
	{
		return true;
	}
	
	public function isValidPrix_journee($prix)
	{
		return strlen($prix) >= 4 && $prix[strlen($prix) - 3] == '.' && ctype_digit(substr($prix, -2)) && ((int) substr($prix, -2)) >=0 && ctype_digit(substr($prix, 0, -3)) && ((int) substr($prix, 0, -3)) >= 0  && !(strlen($prix) == 4 && $prix[0] == '0');
	}
	
	public function isValidPrix_weekend($prix)
	{
		return strlen($prix) == 0 || $this->isValidPrix_journee($prix);
	}
	
	public function isValidPrix_semaine($prix)
	{
		return $this->isValidPrix_weekend($prix);
	}
	
	public function isValidPrix_mois($prix)
	{
		return $this->isValidPrix_weekend($prix);
	}
	
	public function isValidCaution($caution)
	{
		return $this->isValidPrix_journee($caution);
	}
	
	public function isValidNb_objets($nb_objets)
	{
		return ctype_digit($nb_objets) && ((int) $nb_objets) > 0;
	}
	
	public static function formatPrix($prix)
	{
		return str_replace(',', '.', str_replace(' ', '', $prix));
	}
	
	public function isValidMarque($marque)
	{
		return strlen($marque) <= self::TAILLE_MAX_MARQUE;
	}
	
	public function isValidModele($modele)
	{
		return strlen($modele) <= self::TAILLE_MAX_MODELE;
	}
	
	public function isValidClub($ID_membre, $ID_club)
	{
		if($ID_club == -1)
			return true;
		
		$communautes_manager = new CommunautesMan($this->bdd);
		return $communautes_manager->isMembre($ID_club, $ID_membre);
	}
	
	public function addObjet(Membre $membre, $titre, $sous_categorie, $club, $description, $cheque, $location, $utilisation, $marque, $modele, $prix_journee, $prix_weekend, $prix_semaine, $prix_mois, $caution, $nb_objets, $photos)
	{
		$erreur_photos = false;
		$photos_tab = array();
		
		$cheque = ($cheque == true);
		$prix_journee = $this->formatPrix($prix_journee);
		$prix_weekend = $this->formatPrix($prix_weekend);
		$prix_semaine = $this->formatPrix($prix_semaine);
		$prix_mois = $this->formatPrix($prix_mois);
		$caution = $this->formatPrix($caution);
		
		$erreurs = array('titre' => !$this->isValidTitre($titre),
						'sous_categorie' => !$this->isValidSous_categorie($sous_categorie),
						'club' => !$this->isValidClub($membre->ID, $club),
						'description' => !$this->isValidDescription($description),
						'location' => !$this->isValidLocation($location),
						'utilisation' => !$this->isValidUtilisation($utilisation),
						'marque' => !$this->isValidMarque($marque),
						'modele' => !$this->isValidModele($modele),
						'prix_journee' => !$this->isValidPrix_journee($prix_journee),
						'prix_weekend' => !$this->isValidPrix_weekend($prix_weekend),
						'prix_semaine' => !$this->isValidPrix_semaine($prix_semaine),
						'prix_mois' => !$this->isValidPrix_mois($prix_mois),
						'caution' => !$this->isValidCaution($caution),
						'nb_objets' => !$this->isValidNb_objets($nb_objets));
		
		foreach($photos['tmp_name'] as $k => $v)
		{
			if($photos['error'][$k] != 4)
			{
				$res = $this->addPhoto(-1, $photos['tmp_name'][$k], $photos['name'][$k], $photos['size'][$k], $photos['error'][$k], $photos['type'][$k], false);
				$photos_tab[] = array($photos['name'][$k], $res);
				
				if($res !== false)
					$erreur_photos = true;
			}
		}
		
		if(!in_array(true, $erreurs) && !$erreur_photos)
		{
			$req = $this->bdd->prepare('INSERT INTO objets VALUES(\'\', :ID_proprio, :nom, :description, :cheque_caution, -1, :ID_sous_categorie, :ID_club, :conditions_location, :conditions_utilisation, :marque, :modele, :prix_journee, :prix_weekend, :prix_semaine, :prix_mois, :caution, :nb_objets, 1, \'\', 0, NOW())');
			$req->execute(array('ID_proprio' => $membre->ID,
								'nom' => $titre,
								'description' => $description,
								'cheque_caution' => $cheque,
								'ID_sous_categorie' => $sous_categorie,
								'ID_club' => $club,
								'conditions_location' => $location,
								'conditions_utilisation' => $utilisation,
								'marque' => $marque,
								'modele' => $modele,
								'prix_journee' => $prix_journee,
								'prix_weekend' => $prix_weekend,
								'prix_semaine' => $prix_semaine,
								'prix_mois' => $prix_mois,
								'caution' => $caution,
								'nb_objets' => $nb_objets));
			$req->closeCursor();
			
			$ID_objet = $this->bdd->lastInsertId();
			
			mkdir(IMAGES_BIENS . $ID_objet);
			
			foreach($photos['tmp_name'] as $k => $v)
				$this->addPhoto($ID_objet, $photos['tmp_name'][$k], $photos['name'][$k], $photos['size'][$k], $photos['error'][$k], $photos['type'][$k], true);
			
			$photos = glob(IMAGES_BIENS . $ID_objet . '/*.*');
			
			if(!empty($photos))
				$this->modifyPhotoPrincipale($ID_objet, substr($photos[0], strlen(IMAGES_BIENS . $ID_objet . '/')));
			
			return $ID_objet;
		}
		else
			return array_merge($erreurs, array('photos' => $photos_tab));
	}
	
	public function delObjet(Membre $membre, $ID_objet)
	{
		$req = $this->bdd->prepare('SELECT t.ID FROM transactions t INNER JOIN objets o ON o.ID=t.ID_objet WHERE o.ID=:ID AND o.ID_proprio=:ID_proprio AND t.reponse!=0 AND t.annulation=0 AND t.date_fin_loc >= CURDATE()');
		$req->execute(array('ID' => $ID_objet,
							'ID_proprio' => $membre->ID));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		if(empty($donnee))
		{
			$req = $this->bdd->prepare('UPDATE objets SET deleted=1 WHERE ID=?');
			$req->execute(array($ID_objet));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('DELETE FROM objets_accueil WHERE ID_objet=?');
			$req->execute(array($ID_objet));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('DELETE FROM objets_notes WHERE ID_objet=?');
			$req->execute(array($ID_objet));
			$req->closeCursor();
			
			foreach(glob(IMAGES_BIENS . $ID_objet . '/*') as $photo)
				unlink($photo);
			
			rmdir(IMAGES_BIENS . $ID_objet);
			
			return false;
		}
		else
			return true;
	}
	
	public function isDeleted($ID_objet)
	{
		$req = $this->bdd->prepare('SELECT ID FROM objets WHERE deleted=1');
		$req->execute(array('ID' => $ID_objet));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function addPhoto($ID_objet, $tmp_name, $name, $size, $error, $type, $handle = true)
	{
		if($error != UPLOAD_ERR_OK)
			return 'erreur_envoi';
		
		switch($type)
		{
			case 'image/jpg':
			case 'image/jpeg':
				$extension = 'jpg';
				break;
		
			case 'image/gif':
				$extension = 'gif';
				break;
		
			case 'image/png':
				$extension = 'png';
				break;
		
			default:
				return 'extension';
		}
		
		if($size > self::TAILLE_MAX_PHOTO)
			return 'taille';
		
		if(!$handle)
			return false;
		
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
		while(file_exists(IMAGES_BIENS . $ID_objet . '/' . $path));
			
		require_once('fonctions/redimensionnement.php');
		$miniature = redimensionner($tmp_name, $extension, self::PHOTO_MAX_LARGEUR, self::PHOTO_MAX_HAUTEUR);
		
		switch($extension)
		{
			case 'jpg':
				imagejpeg($miniature, IMAGES_BIENS . $ID_objet . '/' . $path, 100);
				break;
			
			case 'png':
				imagepng($miniature, IMAGES_BIENS . $ID_objet . '/' . $path, 9);
				break;
			
			case 'gif':
				imagegif($miniature, IMAGES_BIENS . $ID_objet . '/' . $path, 100);
				break;
		}
		
		return false;
	}
	
	public function champ_exists($champ, $valeur)
	{
		$req = $this->bdd->prepare('SELECT ID FROM membres WHERE ' . $champ . '=?');
		$req->execute(array($valeur));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function modifyDescription($ID_objet, $description)
	{
		if($this->isValidDescription($description))
		{
			$req = $this->bdd->prepare('UPDATE objets SET description=:description WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'description' => $description));
			$req->closeCursor();
			
			return false;
		}
		
		return true;
	}
	
	public function modifyLocation($ID_objet, $location)
	{
		if($this->isValidLocation($location))
		{
			$req = $this->bdd->prepare('UPDATE objets SET conditions_location=:conditions_location WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'conditions_location' => $location));
			$req->closeCursor();
			
			return false;
		}
		
		return true;
	}
	
	public function modifyUtilisation($ID_objet, $utilisation)
	{
		if($this->isValidUtilisation($utilisation))
		{
			$req = $this->bdd->prepare('UPDATE objets SET conditions_utilisation=:conditions_utilisation WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'conditions_utilisation' => $utilisation));
			$req->closeCursor();
			
			return false;
		}
		
		return true;
	}
	
	public function modifyInformations($ID_objet, $nb_objets, $marque, $modele)
	{
		$erreurs = array('marque' => !$this->isValidMarque($marque),
						 'modele' => !$this->isValidModele($modele),
						 'nb_objets' => !$this->isValidNb_objets($nb_objets));
		
		if(!in_array(true, $erreurs))
		{
			$req = $this->bdd->prepare('UPDATE objets SET marque=:marque, modele=:modele, nb_objets=:nb_objets WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'marque' => $marque,
								'modele' => $modele,
								'nb_objets' => $nb_objets));
			$req->closeCursor();
			
			return false;
		}
		
		return $erreurs;
	}
	
	public function modifyTarifs($ID_objet, $prix_journee, $prix_weekend, $prix_semaine, $prix_mois, $caution, $cheque)
	{
		$cheque = ($cheque == true);
		$prix_journee = $this->formatPrix($prix_journee);
		$prix_weekend = $this->formatPrix($prix_weekend);
		$prix_semaine = $this->formatPrix($prix_semaine);
		$prix_mois = $this->formatPrix($prix_mois);
		$caution = $this->formatPrix($caution);
		
		$erreurs = array('prix_journee' => !$this->isValidPrix_journee($prix_journee),
						 'prix_weekend' => !$this->isValidPrix_weekend($prix_weekend),
						 'prix_semaine' => !$this->isValidPrix_semaine($prix_semaine),
						 'prix_mois' => !$this->isValidPrix_mois($prix_mois),
						 'caution' => !$this->isValidCaution($caution));
		
		if(!in_array(true, $erreurs))
		{
			$req = $this->bdd->prepare('UPDATE objets SET prix_journee=:prix_journee, prix_weekend=:prix_weekend, prix_semaine=:prix_semaine, prix_mois=:prix_mois, caution=:caution, cheque_caution=:cheque_caution WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'prix_journee' => $prix_journee,
								'prix_weekend' => $prix_weekend,
								'prix_semaine' => $prix_semaine,
								'prix_mois' => $prix_mois,
								'caution' => $caution,
								'cheque_caution' => $cheque));
			$req->closeCursor();
			
			return false;
		}
		
		return $erreurs;
	}
	
	public function modifySous_categorie($ID_objet, $ID_sous_categorie)
	{
		$erreurs = array('sous_categorie' => !$this->isValidSous_categorie($ID_sous_categorie));
		
		if(!in_array(true, $erreurs))
		{
			$req = $this->bdd->prepare('UPDATE objets SET ID_sous_categorie=:ID_sous_categorie WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'ID_sous_categorie' => $ID_sous_categorie));
			$req->closeCursor();
			
			return false;
		}
		
		return $erreurs;
	}
	
	public function modifyActif($ID_objet, $actif)
	{
		$actif = ($actif == 1) ? 1 : 0;
		
		$req = $this->bdd->prepare('UPDATE objets SET actif=:actif WHERE ID=:ID');
		$req->execute(array('ID' => $ID_objet,
							'actif' => $actif));
		$req->closeCursor();
		
		return false;
	}
	
	public function addNote($ID_objet, $ID_transaction, $ID_noteur, $note)
	{
		if($this->getNote($ID_transaction, $ID_noteur) === false)
		{
			$req = $this->bdd->prepare('INSERT INTO objets_notes VALUES(\'\', :ID_objet, :ID_transaction, :ID_noteur, :note, NOW())');
			$req->execute(array('ID_objet' => $ID_objet,
								'ID_transaction' => $ID_transaction,
								'ID_noteur' => $ID_noteur,
								'note' => $note));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('SELECT SUM(note) s, COUNT(*) c FROM objets_notes WHERE ID_objet=:ID_objet');
			$req->execute(array('ID_objet' => $ID_objet));
			$donnees = $req->fetch();
			$req->closeCursor();
			
			$req = $this->bdd->prepare('UPDATE objets SET note=:note WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'note' => ((double) $donnees['s'])/((double) $donnees['c'])));
			$req->closeCursor();
		}
	}
	
	public function getNote($ID_transaction, $ID_noteur)
	{
		$req = $this->bdd->prepare('SELECT note FROM objets_notes WHERE ID_transaction=:ID_transaction AND ID_noteur=:ID_noteur');
		$req->execute(array('ID_transaction' => $ID_transaction,
							'ID_noteur' => $ID_noteur));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		if(empty($donnee))
			return false;
		
		return $donnee['note'];
	}
	
	public function getNoteMean($ID_objet)
	{
		$req = $this->bdd->prepare('SELECT SUM(note) s, COUNT(*) c FROM objets_notes WHERE ID_objet=:ID_objet');
		$req->execute(array('ID_objet' => $ID_objet));
		$donnees = $req->fetch();
		$req->closeCursor();
		
		if($donnees['c'] == 0)
			return -1;
		
		return ((double) $donnees['s'])/((double) $donnees['c']);
	}
	
	public function getAnnoncesAccueil()
	{
		$req = $this->bdd->query('SELECT o.* FROM objets o INNER JOIN objets_accueil a ON a.ID_objet=o.ID WHERE o.deleted=0 AND o.actif=1 ORDER BY a.ordre DESC LIMIT ' . (int) NOMBRE_ANNONCES_ACCUEIL);
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Objet');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getAnnoncesAccueilTable()
	{
		$req = $this->bdd->query('SELECT * FROM objets_accueil ORDER BY ordre DESC');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function addAnnonceAccueil($ID_objet)
	{
		$objet = $this->getByID($ID_objet);
		
		if(!empty($objet) && $objet->ID_club == -1 && $objet->actif == 1 && $objet->deleted == 0)
		{
			$req = $this->bdd->prepare('INSERT INTO objets_accueil VALUES(\'\', :ID_objet, 0)');
			$req->execute(array('ID_objet' => $ID_objet));
			$req->closeCursor();
			
			return true;
		}
		
		return false;
	}
	
	public function delAnnonceAccueil($ID)
	{
		$req = $this->bdd->prepare('DELETE FROM objets_accueil WHERE ID=:ID');
		$req->execute(array('ID' => $ID));
		$req->closeCursor();
	}
	
	public function modifyAnnonceAccueil($ID, $ordre)
	{
		$req = $this->bdd->prepare('UPDATE objets_accueil SET ordre=:ordre WHERE ID=:ID');
		$req->execute(array('ID' => $ID,
							'ordre' => $ordre));
		$req->closeCursor();
	}
	
	public function modifyPhotoPrincipale($ID_objet, $photo_principale)
	{
		if(file_exists(IMAGES_BIENS . $ID_objet . '/' . $photo_principale))
		{
			$req = $this->bdd->prepare('UPDATE objets SET photo_principale=:photo_principale WHERE ID=:ID');
			$req->execute(array('ID' => $ID_objet,
								'photo_principale' => $photo_principale));
			$req->closeCursor();
			
			return true;
		}
		
		return false;
	}
	
	public function supprimerPhoto(Objet $objet, $photo)
	{
		if(file_exists(IMAGES_BIENS . $objet->ID . '/' . $photo))
		{
			if($objet->photo_principale == $photo)
			{
				$photos = glob(IMAGES_BIENS . $objet->ID . '/*.*');
				
				if($photos[0] == IMAGES_BIENS . $objet->ID . '/' . $photo)
				{
					if(count($photos) > 1)
						$photo_principale = substr($photos[1], strlen(IMAGES_BIENS . $objet->ID . '/'));
					else
						$photo_principale = '';
				}
				else
					$photo_principale = substr($photos[0], strlen(IMAGES_BIENS . $objet->ID . '/'));
				
				$req = $this->bdd->prepare('UPDATE objets SET photo_principale=:photos_principale WHERE ID=:ID');
				$req->execute(array('ID' => $objet->ID,
									'photos_principale' => $photo_principale));
				$req->closeCursor();
			}
			
			unlink(IMAGES_BIENS . $objet->ID . '/' . $photo);
			
			return true;
		}
		
		return false;
	}
	
	public function countAnnoncesErrors()
	{
		$req = $this->bdd->query('SELECT COUNT(*) c FROM objets WHERE ID IN (SELECT ID_objet FROM objets_accueil ORDER BY ordre DESC) AND actif!=1');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee['c'];
	}
	
	public function countAnnonces($actif = 1)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM objets WHERE actif=:actif');
		$req->execute(array('actif' => (!$actif) ? 0 : 1));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
}