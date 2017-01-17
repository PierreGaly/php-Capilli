<?php

class Sous_categorie
{
	protected $ID;
	protected $ID_categorie;
	protected $nom;
	protected $image;
	protected $ordre;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
