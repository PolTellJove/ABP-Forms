<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>

    <body class = "login">
    <?php

        function login(){
            //recogida de datos en Variables
            $email = $_POST["userlog"];
            $password = $_POST["passlog"];

            try {
                $dbh = new PDO('mysql:host=localhost; dbname=abp_poll', "admin", "admin123");
                $stmn = $dbh -> prepare("SELECT * FROM user WHERE user.email = :email AND user.password = SHA2(:pw,256);");
                $stmn -> bindParam(':email', $email);
                $stmn -> bindParam(':pw', $password);
                $stmn -> execute();
                while ($row = $stmn->fetch()) {
                    $idUser =  $row["ID"];
                    $username = $row["username"];
                    $idRole =  $row["roleID"];
                    echo "IdUser:".$idUser."- name;".$username."- IDRole:".$idRole;
                }

                if (isset($idUser) && empty($idUser) == false){
                    $_SESSION["idUser"] = $idUser;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $idRole;

                    echo "Redireccion";

                    header("Location: dashboard.php");


                }
                else{
                    echo "<p>Credencials incorrectes</p>";

                }
            }
            catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        ?>

        <h1 class="title" >Inicia sessió</h1>

        <div class="loginForm">
            <form method="POST" class="form">

                <div class="inputContainer">
                    <input type="text" name="userlog" class="input" placeholder=" ">
                    <label for="" class="label">Correu</label>
                </div>

                <div class="inputContainer">
                    <input type="password" name="passlog" class="input" placeholder=" ">
                    <label for="" class="label">Contrasenya</label>
                </div>

                <input type="submit" class="submitBtn" value="Entra">
            </form>
        </div>

        <div class="Errors">
            <h1>Errors:</h1>
            <?php
                if ( (isset($_POST["userlog"]) && (empty($_POST["userlog"]) == false)) && (isset($_POST["passlog"]) && (empty($_POST["passlog"]) == false)) ){
                    login();
                }
            ?>
        </div>


    </body>



</html>