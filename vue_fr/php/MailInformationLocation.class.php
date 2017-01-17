<?php

class MailInformationLocation extends Mail
{
	public function __construct(Membre $membre_mail, Transaction $transaction, $nbr_jours)
	{
		$adresse = $membre_mail->email;
		
		$jour = ($nbr_jours == 1) ? 'jour' : 'jours';
		
		$objet = $nbr_jours . ' ' . $jour . ' restant avant votre location';
		
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Nous vous rappelons qu\'il ne reste plus que <strong>' . $nbr_jours . ' ' . $jour . '</strong> avant <a href="' . SITE_ADDR . 'reservation.php?id=' . $transaction->ID . '">votre réservation #' . $transaction->ID . ' du ' . (new DateTime($transaction->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction->date_fin_loc))->format('d/m/Y') . '</a>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}