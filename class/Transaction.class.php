<?php

class Transaction
{
	protected $ID;
	protected $ID_objet;
	protected $ID_locataire;
	protected $prix_unitaire_proprio;
	protected $prix_unitaire_locataire;
	protected $quantite;
	protected $caution;
	protected $date_debut_loc;
	protected $date_fin_loc;
	protected $date_transaction;
	protected $date_reponse;
	protected $reponse;
	protected $cheque_caution;
	protected $annulation;
	protected $paiement;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
