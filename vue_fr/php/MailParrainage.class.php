<?php

class MailParrainage extends Mail
{
	public function __construct(Membre $parrain, Membre $filleul)
	{
		$adresse = $parrain->email;
		$objet = SITE_NOM . ' : lien de parrainage';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($parrain->prenom) . ' ' . htmlspecialchars($parrain->nom) . '</strong>,
			<br /><br />
			Le membre ' . $filleul->sePresenter() . ' vous a choisi comme parrain lors de son inscription. À ce titre vous recevrez une commission sur chaque transaction qu\'il effectuera en tant que propriétaire.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}