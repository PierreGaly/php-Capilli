<?php

class MessagerieMan
{
	protected $bdd;
	
	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function getConversationsByMembre($ID_membre)
	{
		$req = $this->bdd->prepare('SELECT c.* FROM messagerie_conversations c INNER JOIN messagerie_membres m ON c.ID=m.ID_conversation WHERE m.ID_membre=:ID_membre ORDER BY m.lu ASC, c.date_dernier_message DESC');
		$req->execute(array('ID_membre' => $ID_membre));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Messagerie_conversation');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function getParticipants($id_conversation)
	{
		$req = $this->bdd->prepare('SELECT mem.* FROM messagerie_membres mes INNER JOIN membres mem ON mes.ID_membre=mem.ID WHERE mes.ID_conversation=:ID_conversation');
		$req->execute(array('ID_conversation' => $id_conversation));
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
		$donnees = $req->fetchAll();
		$req->closeCursor();
		
		return $donnees;
	}
	
	public function addConversation($objet)
	{
		$req = $this->bdd->prepare('INSERT INTO messagerie_conversations VALUES(\'\', :objet, 0)');
		$req->execute(array('objet' => $objet));
		$req->closeCursor();
		
		return $this->bdd->lastInsertId();
	}
	
	public function isParticipant($ID_conversation, $ID_participant)
	{
		$req = $this->bdd->prepare('SELECT ID FROM messagerie_membres WHERE ID_conversation=:ID_conversation AND ID_membre=:ID_membre');
		$req->execute(array('ID_conversation' => $ID_conversation,
							'ID_membre' => $ID_participant));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee);
	}
	
	public function addParticipant($ID_conversation, $ID_participant)
	{
		if(!$this->isParticipant($ID_conversation, $ID_participant))
		{
			$req = $this->bdd->prepare('INSERT INTO messagerie_membres VALUES(\'\', :ID_conversation, :ID_membre, 1)');
			$req->execute(array('ID_conversation' => $ID_conversation,
								'ID_membre' => $ID_participant));
			$req->closeCursor();
		}
	}
	
	public function delParticipant($ID_conversation, $ID_participant)
	{
		$req = $this->bdd->prepare('DELETE FROM messagerie_membres WHERE ID_conversation=:ID_conversation AND ID_membre=:ID_membre');
		$req->execute(array('ID_conversation' => $ID_conversation,
							'ID_membre' => $ID_participant));
		$req->closeCursor();
		
		$req = $this->bdd->prepare('SELECT ID FROM messagerie_membres WHERE ID_conversation=:ID_conversation');
		$req->execute(array('ID_conversation' => $ID_conversation));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		if(empty($donnee))
		{
			$req = $this->bdd->prepare('DELETE FROM messagerie_messages WHERE ID_conversation=:ID_conversation');
			$req->execute(array('ID_conversation' => $ID_conversation));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('DELETE FROM messagerie_conversations WHERE ID=:ID_conversation');
			$req->execute(array('ID_conversation' => $ID_conversation));
			$req->closeCursor();
		}
	}
	
	public function getConversation($ID_conversation, $ID_participant)
	{
		if($this->isParticipant($ID_conversation, $ID_participant))
		{
			$req = $this->bdd->prepare('SELECT * FROM messagerie_conversations WHERE ID=:ID_conversation');
			$req->execute(array('ID_conversation' => $ID_conversation));
			$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Messagerie_conversation');
			$conversation = $req->fetch();
			$req->closeCursor();
			
			return $conversation;
		}
		
		return false;
	}
	
	public function getMessages($ID_conversation, $ID_participant)
	{
		if($this->isParticipant($ID_conversation, $ID_participant))
		{
			$req = $this->bdd->prepare('SELECT * FROM messagerie_messages WHERE ID_conversation=:ID_conversation ORDER BY date_creation');
			$req->execute(array('ID_conversation' => $ID_conversation));
			$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Messagerie_message');
			$messsages = $req->fetchAll();
			$req->closeCursor();
			
			$req = $this->bdd->prepare('UPDATE messagerie_membres SET lu=1 WHERE ID_conversation=:ID_conversation AND ID_membre=:ID_membre');
			$req->execute(array('ID_conversation' => $ID_conversation,
								'ID_membre' => $ID_participant));
			$req->closeCursor();
			
			return $messsages;
		}
		
		return false;
	}
	
	public function addMessage($ID_conversation, $ID_participant, $message)
	{
		if($this->isParticipant($ID_conversation, $ID_participant))
		{
			$req = $this->bdd->prepare('INSERT INTO messagerie_messages VALUES(\'\', :ID_conversation, :ID_membre, :message, NOW())');
			$req->execute(array('ID_conversation' => $ID_conversation,
								'ID_membre' => $ID_participant,
								'message' => preg_replace('/[0-9]/', '', $message)));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('UPDATE messagerie_conversations SET date_dernier_message=NOW() WHERE ID=:ID');
			$req->execute(array('ID' => $ID_conversation));
			$req->closeCursor();
			
			$req = $this->bdd->prepare('UPDATE messagerie_membres SET lu=0 WHERE ID_conversation=:ID_conversation AND ID_membre!=:ID_membre');
			$req->execute(array('ID_conversation' => $ID_conversation,
								'ID_membre' => $ID_participant));
			$req->closeCursor();
			
			
			$req = $this->bdd->prepare('SELECT * FROM membres mem INNER JOIN messagerie_membres mes ON mem.ID=mes.ID_membre WHERE mes.ID_conversation=:ID_conversation AND mes.ID_membre!=:ID_membre');
			$req->execute(array('ID_conversation' => $ID_conversation,
								'ID_membre' => $ID_participant));
			$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Membre');
			$participants = $req->fetchAll();
			$req->closeCursor();
			
			$membres_manager = new MembresMan($this->bdd);
			$conversation = $this->getConversation($ID_conversation, $ID_participant);
			$emetteur = $membres_manager->getMembreByID($ID_participant);
			
			require_once($_SESSION['dossier_vue'] . '/php/MailMessagerie.class.php');
			
			foreach($participants as $participant)
				new MailMessagerie($conversation, $participant, $emetteur);
			
			return true;
		}
		
		return false;
	}
	
	public function hasNouveauMessage($ID_conversation, $ID_participant)
	{
		$req = $this->bdd->prepare('SELECT lu FROM messagerie_membres WHERE ID_conversation=:ID_conversation AND ID_membre=:ID_membre');
		$req->execute(array('ID_conversation' => $ID_conversation,
							'ID_membre' => $ID_participant));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return !empty($donnee) && !$donnee['lu'];
	}
	
	public function countNouveauxMessages($ID_participant)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM messagerie_membres WHERE ID_membre=:ID_membre AND lu=0');
		$req->execute(array('ID_membre' => $ID_participant));
		$donnee = $req->fetch();
		$req->closeCursor();
		
		return $donnee[0];
	}
}