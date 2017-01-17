<?php

class CardsMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function save($ID_membre, $ID_card, $IS3DS, $CTRY, $AUTH, $NUM, $EXP)
	{
		$test = $this->getCardByCard_id($ID_card);
		
		if(empty($test))
		{
			$req = $this->bdd->prepare('INSERT INTO cards VALUES(\'\', :ID_membre, :ID_card, :IS3DS, :CTRY, :AUTH, :NUM, :EXP, NOW())');//:last4, :country, :exp_month, :exp_year, :brand, :card_id, 
			$req->execute(array('ID_membre' => $ID_membre,
								'ID_card' => $ID_card,
								'IS3DS' => $IS3DS,
								'CTRY' => $CTRY,
								'AUTH' => $AUTH,
								'NUM' => $NUM,
								'EXP' => $EXP));
			$req->closeCursor();
		}
		else
		{
			$req = $this->bdd->prepare('UPDATE cards SET date_use=NOW() WHERE ID=:ID');
			$req->execute(array('ID' => $test->ID));
			$req->closeCursor();
		}
	}
	
	public function getCardByCard_id($ID_card)
	{
		$req = $this->bdd->prepare('SELECT * FROM cards WHERE ID_card=:ID_card');
		$req->execute(array('ID_card' => $ID_card));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Card');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
}