<?php
session_start();
include 'utilities.php'; 
$_GET['titlePage'] = 'LandingPage';
$_GET['bodyID'] = 'landing';
$_GET['bodyClass'] = 'landing';
$_SESSION['breadcrumb'] = [];
?><!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<div id='divLanding'>
    <div id='buttons'>
        <a class="button">ALUMNE</a>
        <a class="button breadcrumb" href="login.php">PROFESSOR</a>
    </div>
    <div class="plane-container">
        <img src="./images/paperPlane.png" alt="Paper Plane" />
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>