<?php
session_start();
$_GET['titlePage'] = 'Login';
$_GET['bodyID'] = 'login';
$_GET['bodyClass'] = 'login';
include 'utilities.php'; 
?><!DOCTYPE html>
<html>
<?php include 'header.php'; ?>
<div id='containerLogin'>

    <h1 class="title">Inicia sessió</h1>

    <div class="messageBox"></div>

    <div class="loginForm">
        <form method="POST" class="form" action="./checkoutForms.php">

            <div class="inputContainer">
                <input type="text" name="userlog" class="input" placeholder=" ">
                <label for="" class="label">Correu</label>
            </div>

            <div class="inputContainer">
                <input type="password" name="passlog" class="input password" placeholder=" ">
                <label for="" class="label">Contrasenya</label>
            </div>

            <a href="#">Heu olvidat la contrasenya?</a>

            <button type="button" onclick="loginClient()" class="submitBtn">Entra</button>
        </form>
    </div>
</div>

<script>
    function loginClient() {
        let inputUser = $("[name='userlog']").val();
        let inputPass = $("[name='passlog']").val();

        if (!inputUser || !inputPass) {
            displayMessage('Omple tot el formulari, si us plau', $('.messageBox'), 2);
        } else {
            $(".form").submit();
        }
    }
    
    $(".password").keyup(function(event) {
    if (event.keyCode === 13) {
        $(".submitBtn").click();
    }
    });
</script>
<?php include 'footer.php'; ?>
<?php
showErrors();
?>
</body>



</html>