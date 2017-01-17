<?php

class Transaction_annulation
{
	protected $ID;
	protected $ID_transaction;
	protected $ID_membre;
	protected $date_creation;
	protected $ID_destinataire;
	protected $vu_proprio;
	protected $vu_locataire;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
