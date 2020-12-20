<?php

session_start();
session_unset(); // Désactive la session
session_destroy(); // Détruit la session
setcookie('log'); // Détruit le cookie

header("Location:index.php");