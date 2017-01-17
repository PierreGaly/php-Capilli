<?php

class Documentation_section
{
	protected $ID;
	protected $url;
	protected $titre;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
