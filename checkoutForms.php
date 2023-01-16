<?php
    session_start();
    include 'utilities.php';
    $_SESSION['errors'] = [];

    function login(){
        
        $dbh = new PDO('mysql:host=localhost; dbname=abp_poll', "root", "");
        //recogida de datos en Variables
        $email = $_POST["userlog"];
        $password = $_POST["passlog"];

        try {
            $stmn  = connToDB()->prepare("SELECT * FROM user WHERE user.email = :email AND user.password = SHA2(:pw,256);");
            $stmn -> bindParam(':email', $email);
            $stmn -> bindParam(':pw', $password);
            $stmn -> execute();

            while ($row = $stmn->fetch()) {
                $idUser =  $row["ID"];
                $username = $row["username"];
                $idRole =  $row["roleID"];
            }

            if (isset($idUser) && empty($idUser) == false){
                $_SESSION["ID"] = $idUser;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $idRole;
                header("Location: dashboard.php");
            }
            else{
                array_push($_SESSION['errors'],"displayMessage('Credencials incorrectes',$('.messageBox'),3);");
                header("Location: login.php");

            }
        }
        catch (PDOException $e) {
            array_push($_SESSION['errors'],"displayMessage('Error:".$e->getMessage()."',$('.messageBox'),3);");
            header("Location: login.php");

        }
    }


    function saveQuestions(){
        try {
            $startSession  = connToDB()->prepare("INSERT INTO question (question,typeID) values (:title, :typeQuestion);");
            $stmn -> bindParam(':title', $_POST["questionTitle"]);
            $stmn -> bindParam(':typeQuestion', $_POST["typeQuestion"]);
            $startSession->execute();

            echo "HOLA";
            array_push($_SESSION['errors'],"displayMessage('La pregunta ha sigut guardada correctament',$('.messageBox'),0);");
            header("Location: teacher.php");
 
                // array_push($_SESSION['errors'],"displayMessage('La pregunta no ha sigut guardada correctament',$('.messageBox'),3);");
                // header("Location: teacher.php");
            
        } catch (\Throwable $th) {
            array_push($_SESSION['errors'],"displayMessage('Error en la conexió amb la base de dades',$('.messageBox'),3);");
            header("Location: teacher.php");
        }

    }

    // function saveOptions(){

    // }


    if ( (isset($_POST["userlog"]) && (!empty($_POST["userlog"]))) && (isset($_POST["passlog"]) && (!empty($_POST["passlog"])))  ){
        login();
    }

    if ((isset($_POST["questionTitle"]) && (!empty($_POST["questionTitle"]))) && (isset($_POST["typeQuestion"]) && (!empty($_POST["typeQuestion"])))) {
        switch ($_POST["typeQuestion"]) {
            case 1:
                break;
            case 2:
                saveQuestions();
            # code...
            break;
        }
    }
?>