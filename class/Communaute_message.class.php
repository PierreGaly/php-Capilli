<?php

class Communaute_message
{
	protected $ID;
	protected $ID_communaute;
	protected $ID_membre;
	protected $message;
	protected $date_creation;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
