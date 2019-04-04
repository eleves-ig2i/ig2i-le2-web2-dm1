<?php
/*
 * Affichage des images d'un répertoire.
 */

	$numImage = 0;
	$tabNomFichiers = array();
	$rep = opendir("./$nomRep"); 		// ouverture du repertoire
	while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce r�pertoire
    {

        if (($fichier!=".") && ($fichier!=".."))
        {
            // Pour �liminer les autres r�pertoires du menu d�roulant,
            // on dispose de la fonction 'is_dir'
            if (!is_dir("./$nomRep/" . $fichier))
            {
                // Un fichier... est-ce une image ?
                // On ne liste que les images ...
                // Ajouter la prise en charge des fichiers bmp
                $formats = ".jpeg.jpg.gif.png";
                if (strstr($formats,strrchr($fichier,".")))
                {
                    $dataImg = getimagesize("./$nomRep/$fichier");

                    $width= $dataImg[0];
                    $height= $dataImg[1];
                    $type= substr($dataImg["mime"],6);


                    $tailleMaxValide = 0;
                    if( isset($_SESSION["premium"]) && $_SESSION["premium"] == 1 )
                    {
                        $tailleMaxValide = 10000000000;
                    }
                    else
                    {
                        $tailleMaxValide = 1000000;
                    }

                    if( $dataImg[0]*$dataImg[1] < $tailleMaxValide ) {
                        $numImage++;
                        $tabNomFichiers[] = $fichier;
                        echo "<div class=\"mini\">\n";
                        echo "<a target=\"_blank\" href=\"$nomRep/$fichier\"><img src=\"$nomRep/thumbs/$fichier\"/></a>\n";
                        echo "<div>$fichier \n";
                        echo "<a href=\"?nomRep=$nomRep&fichier=$fichier&action=Supprimer\" >Supp</a>\n";
                        echo "<br />($width * $height $type)\n";
                        echo "<br />\n";

                        echo "<form>\n";
                        echo "<input type=\"hidden\" name=\"fichier\" value=\"$fichier\" />\n";
                        echo "<input type=\"hidden\" name=\"nomRep\" value=\"$nomRep\" />\n";
                        echo "<input type=\"hidden\" name=\"action\" value=\"Renommer\" />\n";
                        echo "<input type=\"text\" class=\"renommer\" name=\"nomFichier\" value=\"$fichier\" onclick=\"this.select();\" />\n";
                        echo "<input type=\"submit\" class=\"btn_renommer\" value=\">\" />\n";
                        echo "</form>\n";

                        echo "</div></div>\n";

                        if (($numImage % 5) == 0)
                            echo "<br style=\"clear:left;\" />";
                    }
                }
            }
        }


    }
	closedir($rep);

	// A compl�ter : afficher un message lorsque le r�pertoire est vide
	if ($numImage==0) echo "<h3>Aucune image dans le répertoire</h3>";
	else {
        echo "<hr style=\"clear:left;\" />";
        echo "<h3>Télécharger les images :</h3>";
        mkForm("explorateur.php", "post");
        mkSelectNonAssociatif("imagesChoisies", $tabNomFichiers);
        mkInput("hidden", "nomRep", $nomRep);
        mkInput("submit", "action", "Telecharger");
        endForm();
    }

?>