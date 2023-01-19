<?php
session_start();
include 'utilities.php';
if (!isset($_SESSION["ID"])) {
    writeInLog("E", "Sessió no iniciada per entrar al dashboard");
    array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al dashboard',$('.messageBox'),3);");
    header("Location: login.php");
}
$user = logUser();
$_GET['titlePage'] = 'Dashboard';
$_GET['bodyID'] = 'dashboard';
$_GET['bodyClass'] = '';
?><!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<div id='divDashboard'>

    <br>
    <div class="titleContainer">
        <?php
        if ($user['role'] == 1) {
        ?>
            <h1>Admin: <?php echo $user['username'] ?></h1>
        <?php
        } else {
        ?>
            <h1>Professor: <?php echo $user['username'] ?></h1>
        <?php
        }
        ?>
    </div>

    <div class="messageBox"></div>
    
    <div id="divButtons">
        <a class="button" href="">ESTADÍSTIQUES</a>
        <?php
        if ($user['role'] == 1) {
        ?>
            <a class="button" href="">USUARIS</a>
            <a class="button" href="teacher.php">ENQUESTES</a>
        <?php
        } else {
        ?>
            <a class="button" href="">PERFIL</a>
        <?php
        }
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
<?php
if (isset($_SESSION["ID"])) {
    showErrors();
}
?>
</html>