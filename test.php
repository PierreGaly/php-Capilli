<?php

require_once('session.php');

if($membre)
{
	if($membre->administrateur)
	{
		if(isset($_POST['creer']) && isset($_POST['ID_categorie']))
		{
			foreach($_POST as $key => $value)
			{
				if(substr($key, 0, 9) == 'cat_name_')
				{
					$ID = substr($key, 9);
					
					if(isset($_POST['cat_image_' . $ID]))
					{
						$req = $bdd->prepare('INSERT INTO sous_categories VALUES(\'\', :ID_categorie, :nom, :image, 0)');
						$req->execute(array('ID_categorie' => $_POST['ID_categorie'],
											'nom' => $value,
											'image' => $_POST['cat_image_' . $ID]));
						$req->closeCursor();
					}
				}
			}
			
			$_SESSION['creer_sous_categories'] = true;
			redirect();
		}
		
		new Page('test', $membre, $bdd);
	}
	else
		new Page('page_incorrecte', $membre, $bdd);
}
else
	new Page('connexion', $membre, $bdd, array('need_connec' => true));