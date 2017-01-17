<?php
date_default_timezone_set('Europe/Paris');

//inclusions et fonctions
require_once('defines.php');
require_once('modele/connexion_sql.php');

function redirect($page = '')
{
	if(empty($page))
		$page = $_SERVER['REQUEST_URI'];
	
	header('Location: ' . $page);
	exit(0);
}

// autoloader
function classInclude($className)
{
	if(file_exists('class/' . $className . '.class.php'))
		require_once('class/' . $className . '.class.php');
	elseif(file_exists('modele/' . $className . '.class.php'))
		require_once('modele/' . $className . '.class.php');
}

spl_autoload_register('classInclude'); // Doit impérativement se trouver AVANT le session_start();

// début de la session et connexion automatique
session_start();

$membre = false;
$post_connec = false;

if(!isset($_SESSION['panier']))
	$_SESSION['panier'] = array();

if(isset($_SESSION['membre']))
	$membre = $_SESSION['membre'];
else
{
	if(isset($_COOKIE['email']) && isset($_COOKIE['mdp']))
	{
		$membres_manager = new MembresMan($bdd);
		
		if(!$membres_manager->connect($_COOKIE['email'], $_COOKIE['mdp'], true))
			redirect();
	}
	
	if(isset($_POST['email_connec']) && isset($_POST['mdp_connec']) && isset($_POST['redirect_connec']))
	{
		$post_connec['email'] = $_POST['email_connec'];
		$post_connec['mdp'] = $_POST['mdp_connec'];
		$post_connec['cookies'] = isset($_POST['cookies_connec']) ? true : false;
		$post_connec['redirect'] = $_POST['redirect_connec'];
	}
	else if(isset($_POST['email_connec2']) && isset($_POST['mdp_connec2']) && isset($_POST['redirect_connec2']))
	{
		$post_connec['email'] = $_POST['email_connec2'];
		$post_connec['mdp'] = $_POST['mdp_connec2'];
		$post_connec['cookies'] = isset($_POST['cookies_connec2']) ? true : false;
		$post_connec['redirect'] = $_POST['redirect_connec2'];
	}

	if($post_connec)
	{
		$membres_manager = new MembresMan($bdd);
		$erreur_connexion = $membres_manager->connect($post_connec['email'], $post_connec['mdp'], false, $post_connec['cookies']);
		
		if(!$erreur_connexion)
			redirect($post_connec['redirect']);
		
		if($erreur_connexion == 'erreur_conn_email_valide')
			redirect('connexion.php?v=' . urlencode($post_connec['email']));
	}
}

// inclure ici tous les objets qui contiennent de la vue
if(!isset($_SESSION['dossier_vue']))
	$_SESSION['dossier_vue'] = 'vue_fr';

require_once($_SESSION['dossier_vue'] . '/php/Page.class.php');