<?php

require_once('session.php');

/*
$fichier = fopen('ex.txt', 'r');

while($string = fgets($fichier))
{
	$res = explode("\t", $string);
	
	if($res[2] != '- ')
		echo '\'' . substr($res[0], 0, strlen($res[0])-1) . '\' => \'' . substr($res[2], 0, strlen($res[2])-1) . '\',<br />';
}

fclose($fichier);
exit(0);
*/

// Paiement
// Virement
// Versement de wallet à wallet
// Enregistrement IBAN

// Inscription
// Compte x 3
// Connexion
// Desinscription

/*
if($membre && $membre->ID == 2)
{
*/

/*
$membres_manager = new MembresMan($bdd);
$membre_lemon = $membres_manager->getMembreByID(1);

$lemon_result = LemonWay::RegisterWallet(array('wallet' => -1,
											   'clientMail' => 'clubdelok2@gmail.com',
											   'clientTitle' => $membre_lemon->civilite ? 'F' : 'M',
											   'clientFirstName' => $membre_lemon->prenom,
											   'clientLastName' => $membre_lemon->nom,
											   'street' => $membre_lemon->street_number . ' ' . $membre_lemon->route,
											   'postCode' => $membre_lemon->postal_code,
											   'city' => $membre_lemon->locality,
											   'ctry' => strtoupper(substr($membre_lemon->country, 0, 3)),
											   'phoneNumber' => $membre_lemon->tel_fixe,
											   'mobileNumber' => $membre_lemon->tel_portable,
											   'birthdate' => $membre_lemon->date_naissance,
											   'isCompany' => $membre_lemon->type,
											   'isOneTimeCustomer' => '0'));

print_r($lemon_result);
exit(0);
*/

/*
$membres_manager = new MembresMan($bdd);
$membre_lemon = $membres_manager->getMembreByID(1);

$lemon_result = LemonWay::RegisterWallet(array('wallet' => -2,
											   'clientMail' => 'clubdelok2@gmail.com',
											   'clientTitle' => $membre_lemon->civilite ? 'F' : 'M',
											   'clientFirstName' => $membre_lemon->prenom,
											   'clientLastName' => $membre_lemon->nom,
											   'street' => $membre_lemon->street_number . ' ' . $membre_lemon->route,
											   'postCode' => $membre_lemon->postal_code,
											   'city' => $membre_lemon->locality,
											   'ctry' => strtoupper(substr($membre_lemon->country, 0, 3)),
											   'phoneNumber' => $membre_lemon->tel_fixe,
											   'mobileNumber' => $membre_lemon->tel_portable,
											   'birthdate' => $membre_lemon->date_naissance,
											   'isCompany' => $membre_lemon->type,
											   'isOneTimeCustomer' => '0'));

print_r($lemon_result);
exit(0);
*/

/*
$membres_manager = new MembresMan($bdd);
$membre_lemon = $membres_manager->getMembreByID(71);

$lemon_result = LemonWay::RegisterWallet(array('wallet' => $membre_lemon->ID,
											   'clientMail' => $membre_lemon->email,
											   'clientTitle' => $membre_lemon->civilite ? 'F' : 'M',
											   'clientFirstName' => $membre_lemon->prenom,
											   'clientLastName' => $membre_lemon->nom,
											   'street' => $membre_lemon->street_number . ' ' . $membre_lemon->route,
											   'postCode' => $membre_lemon->postal_code,
											   'city' => $membre_lemon->locality,
											   'ctry' => LemonWay::country2ISO3($membre_lemon->country),
											   'phoneNumber' => $membre_lemon->tel_fixe,
											   'mobileNumber' => $membre_lemon->tel_portable,
											   'birthdate' => $membre_lemon->date_naissance,
											   'isCompany' => $membre_lemon->type,
											   'isOneTimeCustomer' => '0'));

print_r($lemon_result);
exit(0);
*/

//mettre à jour LWID
// enregistrer les comptes bancaires
// mettre à jour les ID_LEMON		

/*
$lemon_result = LemonWay::RegisterIBAN(array('wallet' => 2,
											'holder' => 'MR PETITPIED Titouan',
											'bic' => 'AGRIFRPP833',
											'iban' => 'FR7613306001040477555400090',
											'dom1' => '',
											'dom2' => ''));
print_r($lemon_result);
exit(0);
*/
/*
											
											/*

$lemon_result = LemonWay::RegisterIBAN(array('wallet' => -2,
														   'holder' => 'Club de Lok',
														   'bic' => 'AGRIFRPP833',
														   'iban' => 'FR7613306001040477555400090',
														   'dom1' => '',
														   'dom2' => ''));
print_r($lemon_result);
exit(0);
*/
/*
print_r(LemonWay::SendPayment(array('debitWallet' => '20',
												 'creditWallet' => '1',
												 'amount' => '105.00',
												 'message' => 'ok')));

*/
/*
	for($i=0; $i<1; $i++)
	{
		print_r(LemonWay::GetWalletDetails(array('wallet' => 73 + $i)));
	}
*/
/*
}
*/

if(!empty($_POST['paiement_montant']))
	$montant = preg_replace('/[^0-9,]/', '', $_POST['paiement_montant']);

if(!empty($montant) && ctype_digit(substr($montant, 0, -3)) && substr($montant, -3, 1) == ',' && ctype_digit(substr($montant, -2)) && $montant >= LemonWay::PRIX_MIN && $montant <= LemonWay::PRIX_MAX)
	$montant = substr($montant, 0, -3) . '.' . substr($montant, -2);
else
	redirect(empty($_POST['paiements_return_path']) ? 'perso.php?revenus' : $_POST['paiements_return_path']);

if($membre)
{
	if(LemonWay::LEMON_DISABLED)
		new Page('paiement_indisponible', $membre, $bdd);
	else
	{
		$erreurs = false;
		
		if(isset($_POST['paiement_cb']) && isset($_POST['paiement_crypto']) && isset($_POST['paiement_date']))
		{
			$carte_numero = preg_replace('/[^0-9]/', '', $_POST['paiement_cb']);
			$carte_date = preg_replace('/[^0-9]/', '', $_POST['paiement_date']);
			
			if(strlen($_POST['paiement_crypto']) == 3 && ctype_digit($_POST['paiement_crypto']))
			{
				$carte_crypto = $_POST['paiement_crypto'];
				$erreurs['carte_crypto'] == false;
			}
			else
				$erreurs['carte_crypto'] == true;
			
			function isValidCardNumber($num)
			{
				$num = preg_replace('/[^\d]/', '', $num);
				$sum = '';

				for ($i = strlen($num) - 1; $i >= 0; -- $i)
					$sum .= $i & 1 ? $num[$i] : $num[$i] * 2;
				
				return array_sum(str_split($sum)) % 10 === 0;
			}

			if(strlen($carte_date) == 4)
			{
				$current_year = date('y');
				$current_month = date('m');
				
				$supposed_year = substr($carte_date, 2, 2);
				$supposed_month = substr($carte_date, 0, 2);
				
				if($supposed_year > $current_year || ($supposed_year == $current_year && $supposed_month >= $current_month))
				{
					$carte_date = $supposed_month . '/20' . $supposed_year;
					$erreurs['carte_date'] == false;
				}
				else
					$erreurs['carte_date'] == true;
			}
			else
				$erreurs['carte_date'] == true;
			
			if(isValidCardNumber($carte_numero))
			{
				$erreurs['carte_numero'] = false;
				
				if(preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $carte_numero))
					$carte_type = 1;// visa
				else if(preg_match('/^5[1-5][0-9]{14}$/', $carte_numero))
					$carte_type = 2;// mastercard
				else
					$carte_type = 0;// other
			}
			else
				$erreurs['carte_numero'] = true;
			
			if(!in_array(true, $erreurs))
			{
				$carte = LemonWay::RegisterCard(array('wallet' => $membre->ID,
													'cardType' => $carte_type,
													'cardNumber' => $carte_numero,
													'cardCode' => $carte_crypto,
													'cardDate' => $carte_date));
				
				if(!$carte['error'])
				{
					$cards_manager = new CardsMan($bdd);
					$paiements_manager = new PaiementsMan($bdd);
					
					$cards_manager->save($membre->ID, $carte['result']['ID'], $carte['result']['EXTRA']->IS3DS, $carte['result']['EXTRA']->CTRY, $carte['result']['EXTRA']->AUTH, $carte['result']['EXTRA']->NUM, $carte['result']['EXTRA']->EXP);
					
					$paiement = LemonWay::MoneyInWithCardId(array('wallet' => $membre->ID,
																'cardId' => $carte['result']['ID'],
																'amountTot' => $montant,
																'comment' => 'Transaction vers la tirelire',
																'autoCommission' => 0,
																'wkToken' => ''));
					
					if(!$paiement['error'])
					{
						$paiements_manager->verser($membre->ID, $montant);
						$_SESSION['revenus_valide_paiement_reel'] = $montant;
						
						redirect(empty($_POST['paiements_return_path']) ? 'perso.php?revenus' : $_POST['paiements_return_path']);
					}
					else
						$erreurs['paiement'] = $paiement['result'];
				}
				else
					$erreurs['carte'] = $carte['result'];
			}
		}
		
		new Page('paiement', $membre, $bdd, array('montant' => $montant, 'erreurs' => $erreurs));
	}
}