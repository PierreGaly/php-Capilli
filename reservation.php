<?php

require_once('session.php');

if(isset($_GET['id']))
{
	$transactions_manager = new TransactionsMan($bdd);
	$objets_manager = new ObjetsMan($bdd);
	$transaction = $transactions_manager->getTransactionByID($_GET['id']);
}

if(!empty($transaction))
{
	if($membre)
	{
		$objet = $objets_manager->getByID($transaction->ID_objet, 2);
		$is_proprio = $membre->administrateur || ($objet->ID_proprio == $membre->ID);
		$is_locataire = $membre->administrateur || ($transaction->ID_locataire == $membre->ID);
		
		if($is_proprio || $is_locataire)
		{
			if($transaction->annulation == 0)
			{
				$today = new DateTime((new DateTime('now'))->format('Y-m-d'));
				
				if($transaction->reponse == 1)
				{
					if(isset($_POST['reservation_submit_message']) && !empty($_POST['reservation_message']))
					{
						$transactions_manager->addTransaction_message($transaction, $membre->ID, $_POST['reservation_message']);
						redirect();
					}
					
					if($is_proprio && isset($_POST['reservation_code']))
					{
						$transaction_code = $transactions_manager->getTransaction_codeByID_transaction($transaction->ID);
						
						if($_POST['reservation_code'] == $transaction_code->code_reservation)
						{
							$transactions_manager->validerTransaction_code($transaction_code->ID);
							redirect();
						}
					}
					
					if($is_locataire && new DateTime($transaction->date_fin_loc) < $today)
					{
						if(!empty($_POST['reservation_commentaire']))
						{
							$transactions_manager->laisserCommentaire($transaction->ID, $membre->ID, $_POST['reservation_commentaire']);
							redirect();
						}
						
						if(!empty($_GET['np']))
						{
							$membres_manager = new MembresMan($bdd);
							$membres_manager->addNote($objet->ID_proprio, $transaction->ID, $membre->ID, $_GET['np']);
							redirect('reservation.php?id=' . $_GET['id']);
						}
						
						if(!empty($_GET['nb']))
						{
							$objets_manager->addNote($objet->ID_proprio, $transaction->ID, $membre->ID, $_GET['nb']);
							redirect('reservation.php?id=' . $_GET['id']);
						}
					}
				}
				
				if($transaction->reponse == 2 && isset($_POST['reservation_proposer']) && isset($_POST['reservation_choix']))
				{
					$transaction_dates = $transactions_manager->getTransactions_dates($transaction->ID);
					
					if($transaction_dates[0]->ID_membre != $membre->ID)
					{
						if($_POST['reservation_choix'] == 0)
							$transactions_manager->accepterTransaction($transaction, $membre->ID);
						else if($_POST['reservation_choix'] == 1)
						{
							$date_debut = substr($_POST['reservation_proposer'], 6, 4) . '-' . substr($_POST['reservation_proposer'], 3, 2) . '-' . substr($_POST['reservation_proposer'], 0, 2);
							$date_fin = substr($_POST['reservation_proposer'], 19, 4) . '-' . substr($_POST['reservation_proposer'], 16, 2) . '-' . substr($_POST['reservation_proposer'], 13, 2);
							
							if(new DateTime($date_debut) >= new DateTime($transaction->date_debut_loc) && new DateTime($date_fin) <= new DateTime($transaction->date_fin_loc))
								$_SESSION['membre'] = $transactions_manager->proposerTransaction($transaction, $membre, $date_debut, $date_fin);
						}
						else if($_POST['reservation_choix'] == 2)
							$transactions_manager->refuserTransaction($transaction->ID);
						
						redirect();
					}
				}
				
				if(isset($_POST['reservation_annuler']) && (new DateTime($transaction->date_debut_loc) > $today || (new DateTime($transaction->date_debut_loc) == $today && ($transaction->reponse != 1 || $transactions_manager->getTransaction_codeByID_transaction($transaction->ID)->date_validation == 0))))
				{
					$result = $transactions_manager->annulerTransaction($transaction, $membre->ID);
					
					if(is_array($result))
						$_SESSION['reservation_annuler_tranasction_lemon'] = $result;
					
					redirect();
				}
			}
			
			if(!empty($objet) && ($is_locataire || $is_proprio))
			{
				if($objet->ID_proprio == $membre->ID)
					$transactions_manager->updateVuTransactionAsProprio($transaction->ID, $membre->ID);
				
				if($transaction->ID_locataire == $membre->ID)
					$transactions_manager->updateVuTransactionAsLocataire($transaction->ID, $membre->ID);
				
				new Page('reservation', $membre, $bdd, array('objet' => $objet, 'transaction' => $transaction));
			}
			else
				new Page('page_incorrecte', $membre, $bdd);
		}
		else
			new Page('page_incorrecte', $membre, $bdd);
	}
	else
		new Page('connexion', $membre, $bdd, array('need_connec' => true));
}
else
	new Page('page_incorrecte', $membre, $bdd);