<?php

class MailInscription extends Mail
{
	public function __construct(Membre $membre_mail)
	{
		$hash = MembresMan::hasher($membre_mail->ID, $membre_mail->email, $membre_mail->date_inscription);
		$lien_validation = SITE_ADDR . 'perso.php?id=' . $membre_mail->ID . '&amp;h=' . $hash;
		
		$adresse = $membre_mail->email;
		//$adresse = 'web-R45Eyj@mail-tester.com';
		$objet = 'Inscription à ' . SITE_NOM;
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Nous vous remercions de votre intérêt pour <a href="' . SITE_ADDR . '">' . SITE_NOM . '</a>.
			<br />Pour terminer votre inscription, cliquez sur lien suivant : <a href="' . $lien_validation . '">' . $lien_validation . '</a>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}