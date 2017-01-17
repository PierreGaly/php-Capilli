<?php

class Documentation_sous_section
{
	protected $ID;
	protected $ID_section;
	protected $url;
	protected $titre;
	protected $texte;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
