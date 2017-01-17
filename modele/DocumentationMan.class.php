<?php

class DocumentationMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getSections()
	{
		$req = $this->bdd->query('SELECT * FROM documentation_sections');
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Documentation_section');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getSousSectionsBySection($ID_section)
	{
		$req = $this->bdd->prepare('SELECT * FROM documentation_sous_sections WHERE ID_section=:ID_section');
		$req->execute(array('ID_section' => $ID_section));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Documentation_sous_section');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function updateSousSection($ID_sous_section, $texte)
	{
		$req = $this->bdd->prepare('UPDATE documentation_sous_sections SET texte=:texte WHERE ID=:ID_sous_section');
		$req->execute(array('ID_sous_section' => $ID_sous_section,
							'texte' => $texte));
		$req->closeCursor();
	}
	
	public function getSectionByUrl($url)
	{
		$req = $this->bdd->prepare('SELECT * FROM documentation_sections WHERE url=:url');
		$req->execute(array('url' => $url));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Documentation_section');
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee;
	}
}