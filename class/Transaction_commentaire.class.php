<?php

class Transaction_commentaire
{
	protected $ID;
	protected $ID_transaction;
	protected $ID_membre;
	protected $commentaire;
	protected $date_creation;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
