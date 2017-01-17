<?php

class MailOubliMdp extends Mail
{
	public function __construct(Membre $membre_mail, $mdp)
	{
		$adresse = $membre_mail->email;
		$objet = 'Mot de passe de connexion à ' . SITE_NOM . ' oublié';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Vous avez demandé la réinitialisation de votre mot de passe.
			<br />
			Voici votre nouveau mot de passe généré aléatoirement : <strong>' . $mdp . '</strong> que vous devez entré dans la <a href="' . SITE_ADDR . 'connexion.php">page de connexion</a>.
			<br /><br />
			Nous vous conseillons vivement de le modifier sans attendre en vous rendant <a href="' . SITE_ADDR . 'perso.php?compte">dans les paramètres de votre comte</a>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}