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
}
getUsers();








?>



<div id='containerForgot'>

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

<?php include 'footer.php'; ?>
<?php
showErrors();
?>
</body>

</html>