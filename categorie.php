<?php

require_once('session.php');

$categorie = false;

if(isset($_GET['c']))
{
	$categories_manager = new CategoriesMan($bdd);
	$categorie = $categories_manager->getCategorieByID($_GET['c']);
}

if($categorie)
	new Page('categorie', $membre, $bdd, array('categorie' => $categorie), $categorie->nom);
else
	redirect('/');