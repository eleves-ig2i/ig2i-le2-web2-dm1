<?php


/**
 * @brief : Ajoute un texte semi-transparent en bas d'une image ainsi qu'un logo (dont on conserve le rapport entre sa largeur et sa hauteur) en haut à droite.
 * @param $nomLogo "Chemin de l'image représentant le logo."
 * @param $typeLogo "Type du logo (png, jpeg ou gif)"
 * @param $nomCible "Chemin de l'image cible."
 * @param $typeCible "Type de l'image cible (png, jpeg ou gif)"
 * @param $texte "Le texte semi-transparent à ajouter (chaîne vide par défaut ou en cas d'erreur)."
 * @param $echelle "Diviseur des dimensions du logo. Si l'echelle vaut 1, alors on conserve les dimensions de l'image. Si l'echelle vaut 2, alors on obtient un logo dont les dimensions sont réduites de moitié.
 */
function logo_copyright($typeLogo, $nomLogo,$texte="",$typeCible,$nomCible,$echelle = 1)
{
    // 0 : on vérifie que le texte est bien une chaine de caractères et que l'échelle est correcte.
    if( !is_string($texte) )
        $texte = "";
    if( !is_numeric($echelle) || $echelle <= 0)
        $echelle = 1;



    // I : on crée les 2 images (logo + source)
    switch($typeLogo)
    {
        case "jpeg" : $imLogo =  imagecreatefromjpeg ($nomLogo);break;
        case "png" : $imLogo =  imagecreatefrompng ($nomLogo);break;
        case "gif" : $imLogo =  imagecreatefromgif ($nomLogo);break;
        default : die("Erreur : Type du logo non connu.");  // on arrete l'exécution.
    }

    switch($typeCible)
    {
        case "jpeg" : $imCible =  imagecreatefromjpeg ($nomCible);break;
        case "png" : $imCible =  imagecreatefrompng ($nomCible);break;
        case "gif" : $imCible =  imagecreatefromgif ($nomCible);break;
        default : die("Erreur : Type de l'image cible non connu.");
    }



    // II : on crée le texte semi transparent et on l'insère en bas de l'image cible
    // 1 : on commence par la couleur et la transparence du texte..
    $noir = imagecolorallocatealpha($imCible, 255,255,255,80);  // On utilise imagecolorallocatealpha pour définir une couleur semi-transparente


    // 2 : on définit la police utilisée pour le texte.
    putenv('GDFONTPATH=' . realpath('./ressources/polices'));
    // pour cela, on utilise la variable globale bash GDFONTPATH afin de charger la police.
    $font = "kartoons";


    // 3 : on positionne le texte (dont la taille sera de 10)
    // On s'inspire de la correction de la fonction copyright,
    // afin d'avoir un texte centré, occupant 50% de la largeur de l'image cible.
    $largeurImageCible = imagesx($imCible);
    $hauteurImageCible = imagesy($imCible);
    $largeurTexteAProduire=0.6 * $largeurImageCible;

    $boxPolice10 = imageftbbox( 10 , 0, $font , $texte);
    $largeurTexteP10 = ($boxPolice10[2]-$boxPolice10[0]);
    $taillePolicefinale = (10 * $largeurTexteAProduire)/$largeurTexteP10;


    $boxPoliceFinale = imageftbbox( $taillePolicefinale , 0, $font , $texte);
    $largeurTexteFinal = ($boxPoliceFinale[2]-$boxPoliceFinale[0]);
    $hauteurTexteFinal = ($boxPoliceFinale[3]-$boxPoliceFinale[7]);

    $x = ($largeurImageCible -  $largeurTexteFinal) /2;
    // On place le texte au 7/8e de la hauteur de l'image.
    $y = (7*$hauteurImageCible - $hauteurTexteFinal)/8;
    imagettftext ( $imCible , $taillePolicefinale , 0 , $x ,  $y ,$noir , $font , $texte );



    // III : on incruste le logo en haut à droite
    // On s'inspire de la correction de la fonction incrustation_logo
    $largeurImageLogo = imagesx($imLogo);
    $hauteurImageLogo = imagesy($imLogo);

    $largeurImageLogoIncrustee = $largeurImageLogo/$echelle;
    $hauteurImageLogoIncrustee = ($hauteurImageLogo*$largeurImageLogoIncrustee)/$largeurImageLogo;

    // On incruste le logo en entier.
    $src_x= 0;
    $src_y= 0;
    $src_w= $largeurImageLogo;
    $src_h= $hauteurImageLogo;

    // On le place en haut à droite de l'image cible.
    $dst_x= (31*$largeurImageCible)/40;
    $dst_y= $largeurImageCible/40;
    $dst_w= $largeurImageLogoIncrustee;
    $dst_h= $hauteurImageLogoIncrustee;

    imagecopyresized ($imCible, $imLogo, $dst_x , $dst_y  , $src_x  , $src_y  , $dst_w  , $dst_h  , $src_w  , $src_h);


    // IV : on affiche l'image finale.
    switch($typeCible)
    {
        case "jpeg" : imagejpeg($imCible);break;
        case "png" : imagepng($imCible);break;
        case "gif" : imagegif($imCible);break;
    }



    // V : on libère la mémoire allouée aux 2 images.
    imagedestroy($imLogo);
    imagedestroy($imCible);
}


if (!isset($_GET["debug"]))
    header("Content-type: image/jpeg");

logo_copyright("jpeg","./ressources/images/isig.jpeg",null,"jpeg","./ressources/images/etudiants2016.jpeg",null);

?>