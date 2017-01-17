<?php

class Ecole
{
	protected $ID;
	protected $UAI;
	protected $nom;
	protected $sigle;
	protected $commune;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
