<?php

class Paiement_error
{
	protected $ID;
	protected $ID_transaction;
	protected $ID_membre_from;
	protected $ID_membre_for;
	protected $montant;
	protected $type;
	protected $LemonCode;
	protected $LemonMsg;
	protected $LemonPrio;
	protected $record_error;
	protected $date_creation;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
