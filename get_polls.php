<?php
session_start();
$_GET['titlePage'] = 'Obtenir enquestes pendents';
$_GET['bodyID'] = 'getPolls';
$_GET['bodyClass'] = 'getPolls';
include 'utilities.php'; 
?><!DOCTYPE html>
<html>
<?php include 'header.php'; ?>
<?php
function getStudents()
{
    $startSession = connToDB()->prepare("SELECT ID, email, username FROM abp_poll.user where roleID = 3;");
    $startSession->execute();
    $_SESSION['emailStudents'] = [];
    foreach ($startSession as $students) {
        array_push($_SESSION['emailStudents'], $students);
    }
    writeInLog("SQL", "SELECT email FROM abp_poll.user where roleID = 3;");
}
getStudents();
?>
<div id='containergetPolls'>

    <h1 class="title">Rebre enquestes pendents</h1>

    <div class="messageBox"></div>

    <div class="getPollsForm">
        <form method="POST" class="form" action="./checkoutForms.php">

            <div class="inputContainer">
                <input type="email" name="userGetPoll" class="input" placeholder="Correu electrònic">
                <label for="" class="label">Correu electrònic</label>
            </div>

            <input type="submit" class="submitBtn" value="Enviar"></input>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
showErrors();
?>
</body>



</html>