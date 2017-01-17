<?php

class Transaction_message
{
	protected $ID;
	protected $ID_transaction;
	protected $ID_membre;
	protected $message;
	protected $date_creation;
	protected $vu_proprio;
	protected $vu_locataire;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
