<?php
/*
 * Gestion des des actions associées aux images d'un répertoire
 */

if (isset($_REQUEST["nomRep"]))  $nomRep = $_REQUEST["nomRep"];
else $nomRep = false;

if (isset($_REQUEST["action"]))
{
    switch($_REQUEST["action"])
    {
        case 'Creer' :
            if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
                if (!is_dir("./" . $_GET["nomRep"]))
                {
                    // A compl�ter : Code de cr�ation d'un r�pertoire
                    mkdir("./" . $_GET["nomRep"]);
                }
            break;

        case 'Supprimer' :
            if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
                if (isset($_GET["fichier"]) && ($_GET["fichier"] != ""))
                {
                    $nomRep = $_GET["nomRep"];
                    $fichier = $_GET["fichier"];

                    // A compl�ter : Supprime le fichier image
                    unlink($nomRep . "/" . $fichier);

                    // A compl�ter : Supprime aussi la miniature si elle existe
                    unlink($nomRep . "/thumbs/" . $fichier);
                }
            break;

        case 'Renommer' :
            if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
                if (isset($_GET["fichier"]) && ($_GET["fichier"] != ""))
                    if (isset($_GET["nomFichier"]) && ($_GET["nomFichier"] != ""))
                    {
                        $nomRep = $_GET["nomRep"];
                        $fichier = $_GET["fichier"];
                        $nomFichier = $_GET["nomFichier"]; // nouveau nom

                        // A compl�ter : renomme le fichier et sa miniature si elle existe
                        if (file_exists("./$nomRep/$fichier"))
                            rename("./$nomRep/$fichier","./$nomRep/$nomFichier");

                        if (file_exists("./$nomRep/thumbs/$fichier"))
                            rename("./$nomRep/thumbs/$fichier","./$nomRep/thumbs/$nomFichier");


                    }
            break;

        case 'Uploader' :
            if (!empty($_FILES["FileToUpload"]))
            {
                if (is_uploaded_file($_FILES["FileToUpload"]["tmp_name"]))
                {
                    print("Quelques informations sur le fichier récupéré :<br>");
                    print("Nom : ".$_FILES["FileToUpload"]["name"]."<br>");
                    print("Type : ".$_FILES["FileToUpload"]["type"]."<br>");
                    print("Taille : ".$_FILES["FileToUpload"]["size"]."<br>");
                    print("Tempname : ".$_FILES["FileToUpload"]["tmp_name"]."<br>");
                    echo "<hr/>";

                    if( $_FILES["FileToUpload"]["size"] < $tailleMaxValide ) {
                        $name = $_FILES["FileToUpload"]["name"];
                        copy($_FILES["FileToUpload"]["tmp_name"], "./$nomRep/$name");

                        // cr�er le r�pertoire miniature s'il n'existe pas
                        if (!is_dir("./$nomRep/thumbs")) {
                            mkdir("./$nomRep/thumbs");
                        }

                        $dataImg = getimagesize("./$nomRep/$name");
                        $type = substr($dataImg["mime"], 6);// on enleve "image/"

                        // cr�er la miniature dans ce r�pertoire
                        miniature($type, "./$nomRep/$name", 200, "./$nomRep/thumbs/$name");
                    }
                    else
                    {
                        echo "Fichier impossible à uploader.";
                    }
                }
                else
                {
                    echo "pb";
                }
            }

            break;

        case 'Supprimer Repertoire':
            // On ne peut supprimer que des r�pertoires vide !
            if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
            {
                // A compl�ter : Supprime le r�pertoire des miniatures s'il existe, puis le r�pertoire principal

                if (is_dir("./$nomRep/thumbs"))
                {
                    $rep = opendir("./$nomRep/thumbs"); 		// ouverture du repertoire
                    while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce r�pertoire
                    {

                        if (($fichier!=".") && ($fichier!=".."))
                        {
                            // Pour �liminer les autres r�pertoires du menu d�roulant,
                            // on dispose de la fonction 'is_dir'
                            if (!is_dir("./$nomRep/thumbs/" . $fichier))
                            {
                                unlink("./$nomRep/thumbs/" . $fichier);
                            }
                        }
                    }
                    rmdir("./$nomRep/thumbs");
                }

                // r�pertoire principal
                $rep = opendir("./$nomRep"); 		// ouverture du repertoire
                while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce r�pertoire
                {

                    if (($fichier!=".") && ($fichier!=".."))
                    {
                        // Pour �liminer les autres r�pertoires du menu d�roulant,
                        // on dispose de la fonction 'is_dir'
                        if (!is_dir("./$nomRep/" . $fichier))
                        {
                            unlink("./$nomRep/" . $fichier);
                        }
                    }
                }

                rmdir("./$nomRep");
                $nomRep = false;
            }
            break;


        case "Telecharger" :
            if( isset( $_REQUEST["imagesChoisies"]) && is_array( $_REQUEST["imagesChoisies"]) ) {
                $tabFichiersATelecharger = $_REQUEST["imagesChoisies"];
                $repZip = "../downloads/";
                $nomZip = $repZip.$nomRep."_".count($tabFichiersATelecharger)."_".date("dmYHi").".zip";   // on utilise l'horodatage pour obtenir un zip unique
                //die($nomZip);
                // On construit un .zip
                $zip = new ZipArchive();
                // Puis on ouvre le fichier pour le modifier
                $zip->open($nomZip, ZipArchive::CREATE);
                foreach ($tabFichiersATelecharger as $data) {
                    //on ajoute chaque fichier sélectionné dans le .zip
                    $zip->addFile($nomRep."/".$data);
                }
                //On ferme l'archive pour le sauvegarder dans l'arborescence.
                $zip->close();

                if( count($tabFichiersATelecharger) > 0) {
                    header("Content-type: application/zip"); // on indique que c'est une archive
                    header("Content-Transfer-Encoding: binary"); // transfert en binaire
                    header("Content-Disposition: attachment; filename=\"$nomZip\""); // nom de l'archive
                    header("Content-Length: " . filesize($nomZip)); // taille de l'archive
                    header("location:" . $nomZip); // redirection vers le téléchargement de l'archive
                }
            }

            break;

    }
}





function miniature($type,$nom,$dw,$nomMin)
{
    // Cr�e une miniature de l'image $nom
    // de largeur $dw
    // et l'enregistre dans le fichier $nomMin


    // lecture de l'image d'origine, enregistrement dans la zone m�moire $im
    switch($type)
    {
        case "jpeg" : $im =  imagecreatefromjpeg ($nom);break;
        case "png" : $im =  imagecreatefrompng ($nom);break;
        case "gif" : $im =  imagecreatefromgif ($nom);break;
    }

    $sw = imagesx($im); // largeur de l'image d'origine
    $sh = imagesy($im); // hauteur de l'image d'origine
    $dh = $dw * $sh / $sw;

    $im2 = imagecreatetruecolor($dw, $dh);

    $dst_x= 0;
    $dst_y= 0;
    $src_x= 0;
    $src_y= 0;
    $dst_w= $dw ;
    $dst_h= $dh ;
    $src_w= $sw ;
    $src_h= $sh ;

    imagecopyresized ($im2,$im,$dst_x , $dst_y  , $src_x  , $src_y  , $dst_w  , $dst_h  , $src_w  , $src_h);


    switch($type)
    {
        case "jpeg" : imagejpeg($im2,$nomMin);break;
        case "png" : imagepng($im2,$nomMin);break;
        case "gif" : imagegif($im2,$nomMin);break;
    }

    imagedestroy($im);
    imagedestroy($im2);
}
?>