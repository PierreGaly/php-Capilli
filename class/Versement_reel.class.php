<?php

class Versement_reel 
{
	protected $ID;
	protected $ID_paiement;
	protected $ID_compte_bancaire;
	protected $date_creation;
	protected $date_validation;
	
	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
