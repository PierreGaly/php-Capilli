<?php

class MailVersementVersBanque extends Mail
{
	public function __construct(Membre $membre_mail, $somme)
	{
		$adresse = $membre_mail->email;
		$objet = SITE_NOM . ' : versement vers votre compte bancaire';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Vous avez choisi de vider votre tirelire contenant une somme de <strong class="rose_custom">' . number_format($somme, 2, ',', ' ') . ' €</strong>. Nous vous ferons le virement dans un délai maximal de 48h. Nous vous remercions pour votre confiance.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}