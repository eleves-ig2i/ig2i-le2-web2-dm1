<?php
    session_start();
    include("data.php");
    include("maLibForms.php");
?>

<html>
<head>
    <style>

    .mini
    {
        position:relative;
        width:200px;
        height:400px;
        float:left;
        border:1px black solid;
        margin-right:5px;
        margin-bottom:5px;
    }
    div img
    {
        margin : 0 auto 0 auto;
        border : none;
    }
    div div
    {
        position:absolute;
        bottom:0px;
        width:100%;
        background-color:lightgrey;
        border-top:1px black solid;
        text-align:center;
    }

    .copyright
    {
        width:150px;
        height:150px;
        border:2px solid orange;
        display:block;
    }


    .renommer
    {
        width:150px;
    }
    .btn_renommer
    {

        width:35px;
    }

    </style>
    <script language="JavaScript" type="text/javascript" src="jquery-3.3.1.min.js"></script>
    <script>

        $(document).ready( function()
        {

            // Se déconnecter
            $("#deconnexion").click(
                function()
                {
                    console.log("L'utilisateur veut se déconnecter.");
                    $.getJSON(
                        "requests.php",
                        {
                            action: "deconnexion"
                        },
                        function(oRep)
                        {
                            console.log("Résultat de la requête ajax :");
                            console.log(oRep);
                            document.location.href = "explorateur.php";
                        }

                    )
                }
            );
        });


    </script>
</head>

<body>

<?php
 echo "Bienvenue ";
 if( isset($_SESSION["id"]) )
 {
     echo $_SESSION["pseudo"];
     if( isset($_SESSION["premium"]) && $_SESSION["premium"] == 1 )
     {
         echo ", vous avez un abonnement premium.";
     }
     echo "<input type=\"button\" value=\"Se déconnecter\" id='deconnexion'/>";
 }
 else
 {
     echo "utilisateur anonyme.";
     echo "<br/><a href='connexion.php'>Se connecter</a>";
 }

if( !(isset($_SESSION["premium"]) && $_SESSION["premium"] == 1) )
{
    echo "<iframe src=\"pictures.php\" class=\"copyright\"> </iframe>";
}

?>


<hr/>
<h1>Gestion des répertoires </h1>
<form>
<label>Créer un nouveau répertoire : </label>
<input type="text" name="nomRep"/>
<input type="submit" name="action" value="Creer" />
</form>

<form>
<label>Choisir un répertoire : </label>
<select name="nomRep">
<?php
	$rep = opendir("./"); // ouverture du repertoire 
	while ( $fichier = readdir($rep))
	{
		// On �limine le r�sultat '.' (r�pertoire courant) 
		// et '..' (r�pertoire parent)

		if (($fichier!=".") && ($fichier!=".."))
		{
			// Pour �liminer les autres fichiers du menu d�roulant, 
			// on dispose de la fonction 'is_dir'
			if (is_dir("./" . $fichier))
				printf("<option value=\"$fichier\">$fichier</option>");
		}
	}
	closedir($rep);
?>
</select>
<input type="submit" value="Explorer"> <input type="submit" name="action" value="Supprimer Repertoire">
</form>

<?php
	if (!$nomRep)  die("Choisissez un répertoire");
	// interrompt imm�diatement l'ex�cution du code php
?>

<hr />
<h2> Contenu du répertoire '<?php echo$_REQUEST["nomRep"]?>' </h2>

<form enctype="multipart/form-data" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php if( isset($_SESSION["premium"]) && $_SESSION["premium"] == 1 ) echo "10000000"; else echo "100000"; ?>">
	<input type="hidden" name="nomRep" value="<?php echo $nomRep; ?>">
	<label>Ajouter un fichier image : </label>
	<input type="file" name="FileToUpload">
	<input type="submit" value="Uploader" name="action">
</form>

<?php include("galerie.php"); ?>


</body>
