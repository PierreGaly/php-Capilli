<?php

class MailCommande extends Mail
{
	public function __construct(Membre $membre_mail, $produits)
	{
		$total = 0;
		
		foreach($produits as $produit)
			$total += ((double) $produit['quantite']) * ((double) $produit['prix_unitaire']);
		
		$adresse = $membre_mail->email;
		$objet = SITE_NOM . ' : votre commande';
		$message_html = '
		<p style="text-align: justify;">
			Bonjour <strong>' . htmlspecialchars($membre_mail->prenom) . ' ' . htmlspecialchars($membre_mail->nom) . '</strong>,
			<br /><br />
			Merci d\'avoir passé votre comande sur notre site pour un montant total de <strong class="rose_custom">' . number_format($total, 2, ',', ' ') . ' €</strong>.
		</p>';
		
		parent::__construct($adresse, $objet, $message_html);
	}
}