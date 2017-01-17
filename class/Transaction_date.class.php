<?php

class Transaction_date
{
	protected $ID;
	protected $ID_transaction;
	protected $ID_membre;
	protected $date_debut_loc;
	protected $date_fin_loc;
	protected $date_proposition;
	protected $vu_proprio;
	protected $vu_locataire;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
