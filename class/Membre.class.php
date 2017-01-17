<?php

class Membre
{
	protected $ID;
	protected $LWID;
	protected $type;
	protected $civilite;
	protected $email;
	protected $email_valide;
	protected $nom;
	protected $prenom;
	protected $adresse_complete;
	protected $street_number;
	protected $route;
	protected $locality;
	protected $administrative_area_level_1;
	protected $country;
	protected $postal_code;
	protected $lat;
	protected $lng;
	protected $ID_ecole;
	protected $avatar;
	protected $mdp;
	protected $tel_fixe;
	protected $tel_portable;
	protected $date_inscription;
	protected $date_derniere_connexion;
	protected $date_naissance;
	protected $ID_source;
	protected $TDR_value;
	protected $TDR_nombre;
	protected $edit_doc;
	protected $administrateur;
	protected $actif;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
	
	public function encode_temps_reponse()
	{
		$value = $this->TDR_value;
		
		$arrondis = array(array(60, 'seconde', 'secondes'),
						 array(60, 'minute', 'minutes'),
						 array(24, 'heure', 'heures'),
						 array(7, 'jour', 'jours'),
						 array(4.33, 'semaine', 'semaines'),
						 array(12, 'mois', 'mois'),
						 array(1, 'année', 'années'));
		
		foreach($arrondis as $key => $arrondi)
		{
			$new_value = $value / $arrondi[0];
			
			if($new_value < 1)
				break;
			
			$value = $new_value;
		}
		
		$value = round($value);
		
		if($value > 1)
			return $value . ' ' . $arrondi[2];
		
		return $value . ' ' . $arrondi[1];
	}
	
	public function getAge()
	{
		$today = new DateTime((new DateTime('now'))->format('Y-m-d'));
		return date_diff($today, new DateTime($this->date_naissance))->format('%y');
	}
	
	public function sePresenter()
	{
		if($this->prenom !== '')
			$name = htmlspecialchars($this->prenom) . ' ' . htmlspecialchars($this->nom);
		else
			$name = htmlspecialchars($this->nom);
		
		return '<a href="' . SITE_ADDR . 'membre.php?id=' . $this->ID . '">' . $name . '</a>';
	}
}
