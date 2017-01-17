<?php

function redimensionner($image_src, $ext, $largeur, $hauteur)// L'extension envoyée correspond au type MIME
{//La fonction retourne l'image redimensionnée, il faut ensuite l'enregistrer.
	switch($ext)
	{
		case 'jpg':
		$image_base = imagecreatefromjpeg($image_src);
		break;
		
		case 'png':
		$image_base = imagecreatefrompng($image_src);
		break;
		
		case 'gif':
		$image_base = imagecreatefromgif($image_src);
	}
	
	$tailleImage = getimagesize($image_src);
	
	if($tailleImage[0] > $largeur OR $tailleImage[1] > $hauteur)
	{
		$prop_src = $tailleImage[0] / $tailleImage[1];
		$prop_red = $largeur / $hauteur;
		
		if($prop_src > $prop_red)
		{
			$largeur_finale = $largeur;
			$hauteur_finale = $largeur / $tailleImage[0] * $tailleImage[1];
		}
		else
		{
			$hauteur_finale = $hauteur;
			$largeur_finale = $hauteur / $tailleImage[1] * $tailleImage[0];
		}
		
		if($ext == 'gif')
			$image_red = imagecreate($largeur_finale, $hauteur_finale);
		else
			$image_red = imagecreatetruecolor($largeur_finale, $hauteur_finale);
		
		//on garde la transparence
		imagealphablending($image_red, false);
		imagesavealpha($image_red, true);
		
		imagecopyresampled($image_red, $image_base, 0, 0, 0, 0, $largeur_finale, $hauteur_finale, $tailleImage[0], $tailleImage[1]);
		imagedestroy($image_base);
		
		return $image_red;
	}
	else
		return $image_base;
}