<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="https://kit.fontawesome.com/277f72a273.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

        <?php
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
                        echo "
                        <script>
                            displayMissage('Credencials incorrectes',$('.missageBox'),3);
                        </script>";
                    }
                }
                catch (PDOException $e) {
                    echo "
                    <script>
                        displayMissage('Error:".$e->getMessage()."',$('.missageBox'),3);
                    </script>";
                    die();
                }
            }
        ?>
    </head>



    <body class = "login">
        <?php include 'header.php';?>

        <script src="./utilities.js" ></script>
        <div id='containerLogin'>

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
                    else{
                        echo "
                        <script>
                            displayMissage(' Omple tot el formulari, si us plau',$('.missageBox'),2);
                        </script>";
                    }
                ?>    
            </div>
        </div>

        <?php include 'footer.php';?>
    </body>



</html>