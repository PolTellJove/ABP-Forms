<?php
    include 'utilities.php';
    session_start();
    writeInLog("I", "Sessió finalitzada", $_SESSION["ID"]);
    session_destroy();

    session_start();
    $_SESSION['errors'] = [];
    array_push($_SESSION['errors'],"displayMessage('Sessió finalitzada',$('.messageBox'),1);");
    
    header("Location: login.php");
?>