<?php

function copyright($type,$nom,$texte)
{
    switch($type)
    {
        case "jpeg" : $im =  imagecreatefromjpeg ($nom);break;
        case "png" : $im =  imagecreatefrompng ($nom);break;
        case "gif" : $im =  imagecreatefromgif ($nom);break;
    }


    $noir = imagecolorallocate($im, 0,0,0);

    // Utiliser imagecolorallocatealpha pour définir une couleur semi-transparente
    $noir = imagecolorallocatealpha($im, 0,0,255,20);



    // ON ajoute un texte à travers en partant de 10% du coin bas gauche
    // On calcule la longueur du texte en taille 10 pour savoir quelle taille choisir
    // index 0 et 1 : position en bas gauche X,Y
    // index 2 et 3 : position en bas droite
    // index 4 et 5 : position en haut droite ...

    putenv('GDFONTPATH=' . realpath('../ressources/polices'));
    $font = "unispace";

    // Utiliser $font = "./ressources/polices/kartoons.ttf"; si ça ne fonctionne pas

    // récupère les points de la boite englobant le texte (sans l'afficher), si on l'écrit en taille 10



    /* Array (
            [0] => -2
            [1] => 2
            [2] => 119
            [3] => 2
            [4] => 119
            [5] => -10
            [6] => -2
            [7] => -10 )
    */
    /*
    imageftbbox() retourne un tableau contenant 8 éléments représentant les 4 points du rectangle entourant le texte :
    0 Coin en bas, à gauche, position en X
    1 Coin en bas, à gauche, position en Y
    2 Coin en bas, à droite, position en X
    3 Coin en bas, à droite, position en Y
    4 Coin en haut, à droite, position en X
    5 Coin en haut, à droite, position en Y
    6 Coin en haut, à gauche, position en X
    7 Coin en haut, à gauche, position en Y
    */

    // A compléter : on souhaite afficher le texte centré sur l'image, pour qu'il prenne 80% de la largeur de l'image
    // à l'aide de la fonction imagettftext

    // 1) récupérer la taille (hauteur,largeur) de l'image où écrire
    // -> sw et sh
    $largeurImage = imagesx($im);
    $hauteurImage = imagesy($im);
    // 2) calculer la largeur du texte à produire (80% de la taille de l'image)
    $largeurTexteAProduire=0.8 * $largeurImage;
    // 3) calculer la largeur d'un texte en police 10
    // utiliser le tableau imageftbbox
    // ou $font = "./ressources/polices/kartoons.ttf";

    $boxPolice10 = imageftbbox( 10 , 0, $font , $texte);
    $largeurTexteP10 = ($boxPolice10[2]-$boxPolice10[0]);
    //echo "largeur texte police 10 : " . $largeurTexteP10;
    // 4) en déduire la taille de police à utiliser pour obtenir le texte final
    // 		(80% de la taille de l'image)
    $taillePolicefinale = (10 * $largeurTexteAProduire)/$largeurTexteP10;

    // 5) calculer la taille (hauteur, largeur) de ce texte
    //		dans cette taille de police
    // imageftbbox avec la taille finale calculée précédemment
    $boxPoliceFinale = imageftbbox( $taillePolicefinale , 0, $font , $texte);
    $largeurTexteFinal = ($boxPoliceFinale[2]-$boxPoliceFinale[0]);  //l
    $hauteurTexteFinal = ($boxPoliceFinale[3]-$boxPoliceFinale[7]); //h
    // 6) en déduire la position du point BAS, GAUCHE du premier caractère
    //		du texte pour qu'il soit centré dans l'image
    $x = ($largeurImage -  $largeurTexteFinal) /2;
    $y = ($hauteurImage + $hauteurTexteFinal)*(2/3);
    // 7) Afficher le texte dans l'image
    imagettftext ( $im , $taillePolicefinale , 0 , $x ,  $y ,$noir , $font , $texte );



    switch($type)
    {
        case "jpeg" :  imagejpeg($im);break;
        case "png" : imagepng($im);break;
        case "gif" : imagegif($im);break;
    }
    imagedestroy($im);
}


if (!isset($_GET["debug"]))
    header("Content-type: image/jpeg");

copyright("jpeg","../ressources/images/bruno.jpeg","Vous n'etes\npas premium :)")

?>