<?php

require_once('session.php');

$transactions_manager = new TransactionsMan($bdd);
$paiements_manager = new PaiementsMan($bdd);

// paiement transactions
print_r($paiements_manager->payerTransactions());

// envoi des mails pour informer les locataires
print_r($transactions_manager->sendMailInformationLocation(array(7, 3, 1)));
