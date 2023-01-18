<?php
session_start();
include 'utilities.php';
if (!isset($_SESSION["ID"])) {

    if (isset($_SESSION['errors']) || (!empty($_SESSION["errors"]))) {
        array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al dashboard',$('.messageBox'),3);");
    }
    else{
        $_SESSION['errors'] = [];
        array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al dashboard',$('.messageBox'),3);");
    }
    header("Location: login.php");
}

$user = logUser();
$_GET['titlePage'] = 'Dashboard';
$_GET['bodyID'] = 'dashboard';
$_GET['bodyClass'] = '';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<div id='divDashboard'>

    <div class="containerLogoutBtn">
        <a class="buttonLogout" href='./logout.php'>
            <i class="fa fa-solid fa-right-from-bracket"></i>
            <div class="logout">SORTIR</div>
        </a>
    </div>
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
    <div id="divButtons">
        <a class="button" href="">ESTADISTÍQUES</a>
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

</html>