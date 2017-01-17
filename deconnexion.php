<?php

session_start();
session_destroy();

if(isset($_COOKIE['email']))
	setcookie('email');

if(isset($_COOKIE['mdp']))
	setcookie('mdp');

if(!empty($_GET['url']))
	header('Location: ' . urldecode($_GET['url']));
else
	header('Location: /');