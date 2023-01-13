<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>

    <body class = "login">
        <h1 class="title" >Inicia sessió</h1>

        <div class="loginForm">
            <form action="" class="form">

                <div class="inputContainer">
                    <input type="text" name="userlog" class="input" placeholder=" ">
                    <label for="" class="label">Correu</label>
                </div>

                <div class="inputContainer">
                    <input type="text" name="passlog" class="input" placeholder=" ">
                    <label for="" class="label">Contransenya</label>
                </div>

                <input type="submit" class="submitBtn" value="Entra">
            </form>
        </div>

        <?php

        function login(){

            //recogida de datos en Variables
            $username = $_POST["userlog"];
            $password = $_POST["passlog"];

            try {
                $dbh = new PDO('mysql:host=localhost; dbname=ABP_forms', "root", "");
                $stmn = $dbh -> prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?;");
                $stmn -> bindParam(1, $username, PDO::PARAM_STR,10);
                $stmn -> bindParam(2, $password, PDO::PARAM_STR,255);
                $stmn -> execute();
                while ($row = $stmn->fetch()) {
                    $idUser =  $row["id"];
                    $username = $row["username"];
                    $idRole =  $row["roleId"];

                }

                if (isset($idUser) && empty($idUser) == false){
                    $_SESSION["idUser"] = $idUser;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $idRole;

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


        if ( (isset($_POST["userlog"]) && (empty($_POST["userlog"]) == false)) && (isset($_POST["passlog"]) && (empty($_POST["passlog"]) == false)) ){
            login();
        }

        ?>


    </body>



</html>