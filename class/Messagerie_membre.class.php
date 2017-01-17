<?php

class Messagerie_membre
{
	protected $ID;
	protected $ID_conversation;
	protected $ID_membre;
	protected $lu;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
