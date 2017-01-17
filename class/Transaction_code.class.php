<?php

class Transaction_code
{
	protected $ID;
	protected $ID_transaction;
	protected $code_reservation;
	protected $date_validation;
	protected $vu_proprio;
	protected $vu_locataire;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
