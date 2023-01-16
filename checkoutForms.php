<?php
    session_start();
    $_SESSION['errors'] = [];

    function login(){
        //recogida de datos en Variables
        $email = $_POST["userlog"];
        $password = $_POST["passlog"];

        try {
            $dbh = new PDO('mysql:host=localhost; dbname=abp_poll', "root", "");
            $stmn = $dbh -> prepare("SELECT * FROM user WHERE user.email = :email AND user.password = SHA2(:pw,256);");
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


    if ( (isset($_POST["userlog"]) && (!empty($_POST["userlog"]))) && (isset($_POST["passlog"]) && (!empty($_POST["passlog"])))  ){
        login();
    }
?>