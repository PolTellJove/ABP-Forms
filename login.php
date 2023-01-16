<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="https://kit.fontawesome.com/277f72a273.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    </head>

    <body class = "login">
        <?php include 'header.php';?>
        <script src="./utilities.js"></script>

        <div id='containerLogin'>

            <h1 class="title" >Inicia sessió</h1>

            <div class = "messageBox">
            </div>

            <div class="loginForm">
                <form method="POST" class="form" action="./checkoutForms.php">

                    <div class="inputContainer">
                        <input type="text" name="userlog" class="input" placeholder=" ">
                        <label for="" class="label">Correu</label>
                    </div>

                    <div class="inputContainer">
                        <input type="password" name="passlog" class="input" placeholder=" ">
                        <label for="" class="label">Contrasenya</label>
                    </div>

                    <a href="#">Heu olvidat la contrasenya?</a>

                    <button type="button" onclick="loginClient()" class="submitBtn">Entra</button>
                </form>
            </div>
        </div>

        <script>
            function loginClient(){
                let inputUser = $("[name='userlog']").val();
                let inputPass = $("[name='passlog']").val();

                if (!inputUser || !inputPass) {
                    displayMessage('Omple tot el formulari, si us plau',$('.messageBox'),2);
                }
                else{
                    $(".form").submit();
                }
            }
        </script>
        <?php include 'footer.php';?>
        <?php
            if (isset($_SESSION['errors']) && (!empty($_SESSION["errors"]))) {
                foreach ($_SESSION['errors'] as $key => $value) {
                    echo "
                    <script>
                        ".$value."
                    </script>";
                }
                $_SESSION['errors'] = [];
            }
        ?>
    </body>



</html>