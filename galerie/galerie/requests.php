<?php
// Gestionnaire des reuqêtes AJAX côté backend
include_once "maLibSQL.pdo.php";

session_start();

function inscrire($pseudo,$code,$premium)
{
    $SQL= " INSERT INTO utilisateurs(pseudo,code,connecte,premium) VALUES('$pseudo','$code',1,$premium)";
    return SQLInsert($SQL);
}


function connecter($pseudo,$code)
{
    $SQL = "SELECT * FROM utilisateurs WHERE pseudo='$pseudo' AND code='$code'";
    return parcoursRs(SQLSelect($SQL));
}

function deconnecter($id)
{
    $SQL = "UPDATE utilisateurs SET connecte=0 WHERE id ='$id'";
    return SQLUpdate($SQL);
}

// die(json_encode($_GET));     // A decommenter lorsqu'on veut savoir quels sont les paramètres envoyées par la requete ajax

$data["feedback"] = "ko";
if( isset($_GET["action"]) )
{
    switch( $_GET["action"] )
    {
        case "inscription" :
            if( !empty($_GET["code"]) && !empty($_GET["pseudo"]) && isset($_GET["premium"] ) )
            {
                $premium = 0;
                if($_GET["premium"] == "on" )
                    $premium = 1;

                $data["id"] = inscrire($_GET["pseudo"],$_GET["code"],$premium);


                $_SESSION["id"] = $data["id"];
                $_SESSION["pseudo"] = $_GET["pseudo"];
                $_SESSION["code"] = $_GET["code"];
                $_SESSION["premium"] = $premium;
                $_SESSION["connecte"] = 1;


                $data["feedback"] = "ok";
            }
            else
            {
                $data["erreur"] = "Mot de passe ou pseudo vide ou problème avec le checkbox.";
            }
        break;


        case "connexion" :
           if( !empty($_GET["code"]) && !empty($_GET["pseudo"]) )
           {
                $varAnnexe = connecter($_GET["pseudo"],$_GET["code"]);
                if( !empty($varAnnexe) )
                {
                    foreach($varAnnexe[0] as $key => $value)
                    {
                        $data["utilisateur"][$key] = $value;
                        $_SESSION[$key] = $value;
                    }

                    $data["feedback"] = "ok";
                }
                else
                {
                    $data["erreur"] = "Identifiants invalides.";
                }
           }
           else
           {
               $data["erreur"] = "Mot de passe ou pseudo vide.";
           }
           break;


        case "deconnexion" :
            if( !empty($_SESSION["id"]) ) {
                deconnecter($_SESSION["id"]);
                $data["feedback"] = "ok";
            }
            else
            {
                $data["erreur"] = "L'utilisateur est déjà déconnecté.";
            }
            session_destroy();
            break;


        default : $data["erreur"] = "Action non prévue.";
    }


}

echo json_encode($data);
?>