<?php
session_start();
include 'utilities.php';
if (!isset($_SESSION["ID"])) {
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
    <div class="containerLogoutBtn">
        <button onclick="location.href = './logout.php'" class="btnLogout"> <i class="fa fa-solid fa-right-from-bracket"></i> LogOut</button>
    </div>
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

    <div id="divButtons">
        <a class="button" href="stats.php">ESTADISTÍQUES</a>
        <?php
        if ($user['role'] == 1) {
        ?>
            <a class="button" href="">USUARIS</a>
            <a class="button" href="teacher.php">ENQUESTES</a>
        <?php
        } else {
        ?>
            <a class="button" href="profile.php">PERFIL</a>
        <?php
        }
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>

</html>