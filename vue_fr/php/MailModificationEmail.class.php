<?php

class MailModificationEmail extends Mail
{
	public function __construct(Membre $membre_mail)
	{
		$hash = MembresMan::hasher($membre_mail->ID, $membre_mail->email, $membre_mail->date_inscription);
		$lien_validation = SITE_ADDR . 'perso.php?id=' . $membre_mail->ID . '&amp;h=' . $hash;
		
		$adresse = $membre_mail->email;
		$objet = 'Validation de l\'adresse mail';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Pour valider votre nouvelle adresse mail, cliquez sur lien suivant : <a href="' . $lien_validation . '">' . $lien_validation . '</a>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}