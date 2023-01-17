<?php
session_start();
$_GET['titlePage'] = 'Login';
$_GET['bodyID'] = 'login';
$_GET['bodyClass'] = 'login';
?>
<!DOCTYPE html>
<html>
<?php include 'header.php'; ?>
<div id='containerLogin'>

    <h1 class="title">Inicia sessió</h1>

    <div class="messageBox">
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
    function loginClient() {
        let inputUser = $("[name='userlog']").val();
        let inputPass = $("[name='passlog']").val();

        if (!inputUser || !inputPass) {
            displayMessage('Omple tot el formulari, si us plau', $('.messageBox'), 2);
        } else {
            $(".form").submit();
        }
    }
</script>
<?php include 'footer.php'; ?>
<?php
if (isset($_SESSION['errors']) && (!empty($_SESSION["errors"]))) {
    foreach ($_SESSION['errors'] as $key => $value) {
        echo "
                    <script>
                        " . $value . "
                    </script>";
    }
    $_SESSION['errors'] = [];
}
?>
</body>



</html>