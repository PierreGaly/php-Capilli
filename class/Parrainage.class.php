<?php

class Parrainage
{
	protected $ID;
	protected $ID_parrain;
	protected $ID_filleul;
	protected $date_demande;
	protected $vu;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
