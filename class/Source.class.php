<?php

class Source
{
	protected $ID;
	protected $source;

	public function __get($attr)
	{
		if(property_exists($this, $attr))
			return $this->$attr;
	}
}
