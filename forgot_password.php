<?php session_start();
$_GET['titlePage'] = 'Recuperar contrasenya';
$_GET['bodyID'] = 'forgotPassword';
$_GET['bodyClass'] = 'forgotPassword';
include 'utilities.php'; 
?><!DOCTYPE html>
<?php include 'header.php'; ?>


<?php
function getUsers()
{
    $startSession = connToDB()->prepare("SELECT email FROM abp_poll.user where roleID != 3;");
    $startSession->execute();
    $_SESSION['users'] = [];
    foreach ($startSession as $students) {
        array_push($_SESSION['users'], $students);
    }
    writeInLog("SQL", "SELECT email FROM abp_poll.user where roleID != 3;");
}
getUsers();
?>

<?php
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(isset($_SESSION['token'])){
        $userToken = $_SESSION['token'];
        if(str_contains($actual_link, $userToken)){

            echo '
            <div id="containerForgot2">
    
        <h1 class="title">Nova contrasenya</h1>
    
        <div class="messageBox"></div>
    
        <div class="forgotForm">
            <form method="POST" class="form" action="./checkoutForms.php">
    
                <div class="inputContainer">
                    <input type="password" name="recoverPassword1" class="input" placeholder="Nova contrasenya">
                    <label for="" class="label">Nova contrasenya</label>
                </div>

                <div class="inputContainer">
                    <input type="password" name="recoverPassword2" class="input" placeholder="Repeteix la contrasenya">
                    <label for="" class="label">Repeteix la contrasenya</label>
                </div>
    
                <input type="submit" class="submitBtn" value="Canviar contrasenya">
            </form>
        </div>
    </div>
            ';
        }
        else{
            echo '
            <div id="containerForgot">

    <h1 class="title">Recuperar contrasenya</h1>

    <div class="messageBox"></div>

    <div class="forgotForm">
        <form method="POST" class="form" action="./checkoutForms.php">

            <div class="inputContainer">
                <input type="email" name="emailFor" class="input" placeholder="Correu electrònic">
                <label for="" class="label">Correu electrònic</label>
            </div>

            <input type="submit" class="submitBtn" value="Envia">
        </form>
    </div>
</div>
            ';

        }
    }
    else{
        echo '
        <div id="containerForgot">

<h1 class="title">Recuperar contrasenya</h1>

<div class="messageBox"></div>

<div class="forgotForm">
    <form method="POST" class="form" action="./checkoutForms.php">

        <div class="inputContainer">
            <input type="email" name="emailFor" class="input" placeholder="Correu electrònic">
            <label for="" class="label">Correu electrònic</label>
        </div>

        <input type="submit" class="submitBtn" value="Envia">
    </form>
</div>
</div>
        ';

    }
?>


<?php include 'footer.php'; ?>
<?php
showErrors();
?>
</body>

</html>