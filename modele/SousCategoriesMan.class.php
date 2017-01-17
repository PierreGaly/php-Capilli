<?php

class SousCategoriesMan
{
	protected $bdd;
	const TAILLE_MAX_PHOTO = 10485760;//10 Mio
	const PHOTO_MAX_LARGEUR = 1000;
	const PHOTO_MAX_HAUTEUR = 400;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getSousCategoriesByCategorie($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM sous_categories WHERE ID_categorie=? ORDER BY ordre');
		$req->execute(array($id));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Sous_categorie');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getSousCategorieByID($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM sous_categories WHERE ID=?');
		$req->execute(array($id));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Sous_categorie');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
	
	public function modifierSous_categorie($ID_sous_categorie, $nom, $ordre, $image)
	{
		$error = false;
		
		if($image['error'] == 4)
			$image_name = $this->getSousCategorieByID($ID_sous_categorie)->image;
		else
		{
			$res = $this->addImage($image['tmp_name'], $image['name'], $image['size'], $image['error'], $image['type']);
			
			if(is_array($res))
				$image_name = $res[1];
			else
				$error = $res[0];
		}
		
		if($error == false)
		{
			$req = $this->bdd->prepare('UPDATE sous_categories SET nom=:nom, ordre=:ordre, image=:image WHERE ID=:ID_sous_categorie');
			$req->execute(array('ID_sous_categorie' => $ID_sous_categorie,
								'nom' => $nom,
								'ordre' => $ordre,
								'image' => $image_name));
			$req->closeCursor();
		}
		
		return $error;
	}
	
	public function addSous_categorie($ID_categorie, $nom, $ordre, $image)
	{
		$error = false;
		
		if($image['error'] == 4)
			$error = 'empty';
		else
		{
			$res = $this->addImage($image['tmp_name'], $image['name'], $image['size'], $image['error'], $image['type']);
			
			if(is_array($res))
				$image_name = $res[1];
			else
				$error = $res[0];
		}
		
		if($error == false)
		{
			$req = $this->bdd->prepare('INSERT INTO sous_categories VALUE(\'\', :ID_categorie, :nom, :image, :ordre)');
			$req->execute(array('ID_categorie' => $ID_categorie,
								'nom' => $nom,
								'image' => $image_name,
								'ordre' => $ordre));
			$req->closeCursor();
		}
		
		return $error;
	}
	
	public function addImage($tmp_name, $name, $size, $error, $type)
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
		while(file_exists(IMAGES_SOUS_CAT . '/' . $path));
			
		require_once('fonctions/redimensionnement.php');
		$miniature = redimensionner($tmp_name, $extension, self::PHOTO_MAX_LARGEUR, self::PHOTO_MAX_HAUTEUR);
		
		switch($extension)
		{
			case 'jpg':
				imagejpeg($miniature, IMAGES_SOUS_CAT . '/' . $path, 100);
				break;
			
			case 'png':
				imagepng($miniature, IMAGES_SOUS_CAT . '/' . $path, 9);
				break;
			
			case 'gif':
				imagegif($miniature, IMAGES_SOUS_CAT . '/' . $path, 100);
				break;
		}
		
		return array('OK', $path);
	}
}
