<?php

try
{
	//$bdd = new PDO('mysql:host=localhost;dbname=whebo', 'admin', 'bob');
	$bdd = new PDO('mysql:host=localhost;dbname=Capilli', 'root', 'root');
	$bdd->exec("SET CHARACTER SET utf8");
}
catch(Exception $e)
{
	die($e->getMessage());
	echo 'bug';
}
