<?php

class MailMessagerie extends Mail
{
	public function __construct(Messagerie_conversation $conversation, Membre $membre_mail, Membre $emetteur)
	{
		$adresse = $membre_mail->email;
		$objet = SITE_NOM . ' : nouveau message';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Vous avez reçu un nouveau message de la part de ' . $emetteur->sePresenter() . ' dans votre conversation : <em><a href="' . SITE_ADDR . 'perso.php?messages&c=' . $conversation->ID . '">' . htmlspecialchars($conversation->objet) . '</a></em>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}