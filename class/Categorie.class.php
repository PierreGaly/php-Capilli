<?php

class Categorie 
{
	protected $ID;
	protected $nom;
	protected $ordre;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
