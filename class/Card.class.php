<?php

class Card 
{
	protected $ID;
	protected $ID_membre;
	protected $ID_card;
	protected $IS3DS;
	protected $CTRY;
	protected $AUTH;
	protected $NUM;
	protected $EXP;
	protected $date_use;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}