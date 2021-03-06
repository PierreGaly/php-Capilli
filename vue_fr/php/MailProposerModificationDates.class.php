<?php

class MailProposerModificationDates extends Mail
{
	public function __construct(Transaction $transaction, Membre $emetteur, Membre $recepteur)
	{
		$adresse = $recepteur->email;
		$objet = SITE_NOM . ' : modification de la réservation #' . $transaction->ID;
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($recepteur->prenom) . ' ' . htmlspecialchars($recepteur->nom) . '</strong>,
			<br /><br />
			Le membre ' . $emetteur->sePresenter() . ' a proposé de nouvelles dates pour <a href="' . SITE_ADDR . 'reservation.php?id=' . $transaction->ID . '">la réservation #' . $transaction->ID . ' du ' . (new DateTime($transaction->date_debut_loc))->format('d/m/Y') . ' au ' . (new DateTime($transaction->date_fin_loc))->format('d/m/Y') . '</a>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}