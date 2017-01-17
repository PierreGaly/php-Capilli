<?php

class Communaute_proposition
{
	protected $ID;
	protected $ID_membre;
	protected $nom;
	protected $image;
	protected $description;
	protected $date_proposition;
	protected $vu;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
