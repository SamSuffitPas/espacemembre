<?php

session_start();

require("src/connexion.php");

if(isset($_SESSION['connect'])) {
    header("Location:index.php");
    exit();
}

// Connexion
if(!empty($_POST['email']) && !empty($_POST['password'])) {
   
    $email = $_POST['email'];
    $password = $_POST['password'];

    // On sélectionne tous les éléments quand email = email de l'utilisateur
    $req = $db->prepare('SELECT * FROM users WHERE email = ?');
    $req->execute(array($email));

    while($user = $req->fetch()) {

        // On vérifie s'il s'agit du bon mot de passe
        if (password_verify($password, $user['password'])) {

            $_SESSION['connect'] = 1;
            $_SESSION['pseudo'] = $user['pseudo'];

            if(isset($_POST['connect'])) {

                setcookie('log', $user['secret'], time()+31536000);
            }

            header("Location:connexion.php?success=1");
            exit();

        // Si le mot de passe est incorrect :    
        } else {
            header("Location:connexion.php?error=1");
            exit();
        }
    
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Connexion</title>
</head>
<body>
    <header>
        <h1>Connexion</h1>
    </header>
    <div class="container">
        <p id="info">Bienvenue sur mon site, si vous n'êtes pas inscrit,
            <a href="index.php">Inscrivez vous</a></p>
        <?php

            // Message en cas d'erreur et en cas de succès :
            if(isset($_GET['error'])) {

                echo'<p id="error"> Authentification incorrect </p>';
            }
            elseif(isset($_GET['success'])) {

                echo'<p id="success"> Vous êtes connectés </p>';
            }
            
        ?>
        
        <div id="form">
            <form class="inscription" method="post" action="connexion.php">
                <table>
                        <td>Email</td>
                        <td><input type="email" name="email" id="email" placeholder="Ex : Marc@gmail.com" required/></td>
                    </tr>
                    <tr>   
                        <td>Mot de passe</td> 
                        <td><input type="password" name="password" id="password" placeholder="********" required/></td>
                    </tr>
                </table>
                <p><label><input type="checkbox" name="connect" checked>Connexion automatique</label></p>
                <div id="button">
                    <button>Se connecter</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
