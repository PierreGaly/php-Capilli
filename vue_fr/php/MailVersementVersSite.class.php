<?php

class MailVersementVersSite extends Mail
{
	public function __construct(Membre $membre_mail, $somme)
	{
		$adresse = $membre_mail->email;
		$objet = SITE_NOM . ' : versement effectué vers votre tirelire';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Nous avons effectué le versement de <strong class="rose_custom">' . number_format($somme, 2, ',', ' ') . ' €</strong> vers votre <strong>tirelire ' . SITE_NOM . '</strong>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}