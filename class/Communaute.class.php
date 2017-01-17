<?php

class Communaute
{
	protected $ID;
	protected $nom;
	protected $image;
	protected $description;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
