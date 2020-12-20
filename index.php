<?php

session_start();

require('src/connexion.php');

// On vérifie si les champs sont bien remplis
if(!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password']) && 
    !empty($_POST['password_confirm'])) {

    $pseudo = $_POST['pseudo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passConfirm = $_POST['password_confirm'];

    // Si mot de passe = confirmation du mot de passe 
    if($password != $passConfirm) {
        header("Location: index.php?error=1&pass=1");
        exit();
    }

    // On vérifie si l'email a déjà été utilisé
    $req = $db->prepare('SELECT COUNT(*) AS nbEmail FROM users WHERE email = ?'); 
    $req->execute(array($email));

    while($email_verification = $req->fetch()) {

        if($email_verification['nbEmail'] != 0) {
            header("Location: index.php?error=1&email=1");
            exit();
        }
    }

    // Hash
    $secret = sha1($email).rand();
    $secret = sha1($secret).rand();

    // Cyptage du mot de passe
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Enregistrer un nouvel utilisateur
    $req = $db->prepare("INSERT INTO users(pseudo, email, password, secret)
                        VALUES (?, ?, ?, ?)");

    $req->execute(array($pseudo, $email, $password, $secret));

    header("Location:index.php?success=1");
    exit();

} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Espace Membre</title>
</head>
<body>
    <header>
        <h1>Inscription</h1>
    </header>
    <div class="container">

        <?php 
        
        if(!isset($_SESSION['connect'])) { ?>
        
            <p id="info">Bienvenue sur mon site, pour en voir plus, inscrivez-vous.
                Sinon <a href="connexion.php">Connectez-vous</a></p>

            <?php

                // On vérifie que l'email soit enregistré et que le mot de passe corresponde à l'email enregistré
                if(isset($_GET['error'])){

                    if(isset($_GET['pass'])) {

                        echo'<p id="error"> Les mots de passe ne sont pas identiques !</p>';

                    }
                    elseif (isset($_GET['email'])) {
                        echo'<p id="error"> Cette adresse email est déjà enregistrée</p>';
                    }
                }

                if(isset($_GET['success'])) {
                    
                    echo'<p id="success"> Inscription effectuée ! </p>';
                }

            ?>

            <div id="form">
                <form class="inscription" method="post" action="index.php">
                    <table>
                        <tr>
                            <td>Pseudo</td>
                            <td><input type="text" name="pseudo" id="pseudo" placeholder="Ex : Marc" required/></td>
                        </tr>    
                        <tr>
                            <td>Email</td>
                            <td><input type="email" name="email" id="email" placeholder="Ex : Marc@gmail.com" required/></td>
                        </tr>
                        <tr>   
                            <td>Mot de passe</td> 
                            <td><input type="password" name="password" id="password" placeholder="*****" required/></td>
                        </tr>
                        <tr>
                            <td>Confirmer le mot de passe</td>    
                            <td><input type="password" name="password_confirm" id="confirmation" placeholder="*****" required/></td>   
                        </tr>
                    </table>
                        <div id="button">
                            <button>S'inscrire</button>
                        </div>
                </form>
            </div>
        <?php } else { ?>

            <p id="info" >Bonjour <?= $_SESSION['pseudo'] ?>, vous êtes connecté<br>
            <a href="deconnexion.php">Deconnexion</a>    
        </p>

        <?php } ?>   
    </div>
</body>
</html>

