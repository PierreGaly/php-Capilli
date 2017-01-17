<?php

class Compte_bancaire 
{
	protected $ID;
	protected $ID_lemon;
	protected $ID_membre;
	protected $titulaire;
	protected $iban;
	protected $bic;
	protected $nom_agence;
	protected $rue_agence;
	protected $date_creation;
	protected $date_suppression;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
