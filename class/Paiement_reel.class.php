<?php

class Paiement_reel
{
	protected $ID;
	protected $ID_membre;
	protected $montant;
	protected $payment_id;
	protected $date_creation;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
