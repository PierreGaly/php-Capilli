<?php

class Messagerie_conversation
{
	protected $ID;
	protected $objet;
	protected $date_dernier_message;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
