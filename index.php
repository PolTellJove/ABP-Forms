<?php
$_GET['titlePage'] = 'LandingPage';
$_GET['bodyID'] = 'landing';
$_GET['bodyClass'] = 'landing';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<div id='divLanding'>
    <div id='buttons'>
        <a class="button" href="get_polls.php">ALUMNE</a>
        <a class="button" href="login.php">PROFESSOR</a>
    </div>
    <div class="plane-container">
        <img src="./images/paperPlane.png" alt="Paper Plane" />
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>