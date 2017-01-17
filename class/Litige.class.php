<?php

class Litige
{
	protected $ID;
	protected $ID_membre;
	protected $ID_transaction;
	protected $message;
	protected $date_creation;
	protected $date_traitement;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
