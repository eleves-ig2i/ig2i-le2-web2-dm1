<?php
/*
 * Identification d'un utilisateur
 */

session_start();
if( !empty($_SESSION["id"]) )   // utilisateur déjà connecté => on le redirige vers explorateur.php
    header("Location:explorateur.php" );
?>

<html>
    <style>

        .error
        {
            color : red;
        }
    </style>
    <script language="JavaScript" type="text/javascript" src="jquery-3.3.1.min.js"></script>

    <script>
        $(document).ready(function() {

            // Par défaut, on se connecte.
            $("#inscription").hide();


            // Changer de formulaire.
            $("#changeForm").click(
                function()
                {
                    if( $(this).val() === "S'inscrire" ) {
                        console.log("L'utilisateur veut changer de formulaire : il veut s'inscrire.");
                        $("#connexion").hide();
                        $("#inscription").show();
                        $(this).val("Se connecter");
                    }
                    else
                    {
                        console.log("L'utilisateur veut changer de formulaire : il veut se connecter.");
                        $("#connexion").show();
                        $("#inscription").hide();
                        $(this).val("S'inscrire");
                    }


                    $(".error").html("");
                }
            );


            // S'inscrire.
            $('input[value="Inscription"]').click(
                function()
                {
                    console.log("L'utilisateur veut s'inscrire. Premium : " + $("#premium").val() );

                    var aboPremium = "off";
                    if( $("#premium").prop("checked") )
                    {
                        aboPremium = "on";
                    }
                    $.getJSON(
                        "requests.php",
                        {
                            action:"inscription",
                            pseudo: $("#pseudoInscription").val(),
                            code: $("#codeInscription").val(),
                            premium: aboPremium    // premium vaut soit "on" soit "off"
                        },
                        function(oRep)
                        {
                            console.log("Résultat de la requête ajax :");
                            console.log(oRep);
                            if( oRep.feedback === "ok" )
                            {
                                document.location.href = "explorateur.php";
                            }
                            else
                            {
                                $(".error").html( oRep.erreur );
                            }
                        }
                    );
                }
            );


            // Se connecter.
            $('input[value="Connexion"]').click(
                function()
                {
                    console.log("L'utilisateur veut se connecter. Pseudo : " + $("#pseudoConnexion").val());
                    $.getJSON(
                        "requests.php",
                        {
                            action:"connexion",
                            pseudo: $("#pseudoConnexion").val(),
                            code: $("#codeConnexion").val()
                        },
                        function(oRep)
                        {
                            console.log("Résultat de la requête ajax :");
                            console.log(oRep);
                            if( oRep.feedback === "ok" )
                            {
                                document.location.href = "explorateur.php";
                            }
                            else
                            {
                                $(".error").html( oRep.erreur );
                            }
                        }
                    )
                }
            );


            // Revenir à l'explorateur de fichiers
            $("#goToGalerie").click(function() {
                    console.log("L'utilisateur veut revenir à la galerie.");
                    window.location.href = "explorateur.php";
                }
            );

        });
    </script>
    <body>
        <h2>Veuillez vous identifier.</h2>
        <div id="connexion">
            <h3>Se connecter</h3>
            <label>Pseudo : </label>
            <input type="text" id="pseudoConnexion"/>
            <br />
            <label>Mot de passe : </label>
            <input type="password"  id="codeConnexion"/>
            <br />
            <input type="button" value="Connexion" />
            <hr/>
        </div>
        <div id="inscription">
            <h3>S'inscrire</h3>
            <label>Pseudo : </label>
            <input type="text" id="pseudoInscription"/>
            <br />
            <label>Mot de passe : </label>
            <input type="password"  id="codeInscription"/>
            <br />
            <label>Abonnement premium :</label>
            <input type="checkbox" id="premium" />
            <br/>
            <input type="button" value="Inscription" />
            <hr/>
        </div>
        <input type="button" id="changeForm" value="S'inscrire" />
        <input type="button" id="goToGalerie" value="Revenir à l'explorateur" />
        <p class="error"></p>


    </body>
</html>
