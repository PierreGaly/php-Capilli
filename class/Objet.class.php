<?php

class Objet
{
	protected $ID;
	protected $ID_proprio;
	protected $nom;
	protected $description;
	protected $cheque_caution;
	protected $note;
	protected $ID_sous_categorie;
	protected $ID_club;
	protected $conditions_location;
	protected $conditions_utilisation;
	protected $marque;
	protected $modele;
	protected $prix_journee;
	protected $prix_weekend;
	protected $prix_semaine;
	protected $prix_mois;
	protected $caution;
	protected $nb_objets;
	protected $actif;
	protected $photo_principale;
	protected $deleted;
	protected $date_creation;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
	
	public function getPhotoPrincipale()
	{
		return ($this->photo_principale == '') ? ObjetsMan::DEFAULT_PHOTO_PATH : (IMAGES_BIENS . $this->ID . '/' . $this->photo_principale);
	}
}
