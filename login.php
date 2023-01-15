<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="https://kit.fontawesome.com/277f72a273.js" crossorigin="anonymous"></script>
    </head>

    <body class = "login">
    <script src="./login.js" ></script>

    <?php
        $errorMissageId = 0;
        function displayMissage($missageContent, $codeMessage = 3){
            global $errorMissageId;
            // CodeMessage: 0 => success, 1 => info,2 => warning,3 => error,
            switch ($codeMessage) {
                case 0:
                    echo"<div id='displayMessage".$errorMissageId."' class='displayMessage success'>
                    <i class='fa fa-check-circle'></i>
                    ".$missageContent."
                    <button onclick='removeMissage(displayMessage".$errorMissageId.")' class='closeMessageBtn' ><i class='fa fa-close'></i></button>
                </div>";
                    break;
                case 1:
                    echo"<div id='displayMessage".$errorMissageId."' class='displayMessage info'>
                    <i class='fa fa-info'></i>
                    ".$missageContent."
                    <button onclick='removeMissage(displayMessage".$errorMissageId.")' class='closeMessageBtn' ><i class='fa fa-close'></i></button>
                </div>";
                    break;
                case 2:
                    echo"<div id='displayMessage".$errorMissageId."' class='displayMessage warning'>
                    <i class='fa fa-warning'></i>
                    ".$missageContent."
                    <button onclick='removeMissage(displayMessage".$errorMissageId.")' class='closeMessageBtn' ><i class='fa fa-close'></i></button>
                </div>";
                    break;
                case 3:
                    echo"<div id='displayMessage".$errorMissageId."' class='displayMessage error'>
                    <i class='fa fa-exclamation-circle'></i>
                    ".$missageContent."
                    <button onclick='removeMissage(displayMessage".$errorMissageId.")' class='closeMessageBtn' ><i class='fa fa-close'></i></button>
                </div>";
                
                default:
                    # code...
                    break;
            }

            $errorMissageId = $errorMissageId + 1;

        }
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
                    $_SESSION["idUser"] = $idUser;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $idRole;

                    header("Location: dashboard.php");


                }

                else{
                    displayMissage("Credencials incorrectes",2);
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

        <div class = "missageBox">
            <?php
                if ( (isset($_POST["userlog"]) && (!empty($_POST["userlog"]))) && (isset($_POST["passlog"]) && (!empty($_POST["passlog"])))  ){
                    login();
                }
            ?>    
        </div>
        

    </body>



</html>