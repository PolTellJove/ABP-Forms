<?php
session_start();
include 'utilities.php';
$_SESSION['errors'] = [];

function login()
{

    $dbh = new PDO('mysql:host=localhost; dbname=abp_poll', "root", "");
    //recogida de datos en Variables
    $email = $_POST["userlog"];
    $password = $_POST["passlog"];

    try {
        $stmn = connToDB()->prepare("SELECT * FROM user WHERE user.email = :email AND user.password = SHA2(:pw,256);");
        $stmn->bindParam(':email', $email);
        $stmn->bindParam(':pw', $password);
        $stmn->execute();

        while ($row = $stmn->fetch()) {
            $idUser = $row["ID"];
            $username = $row["username"];
            $idRole = $row["roleID"];
        }

        if (isset($idUser) && empty($idUser) == false) {
            $_SESSION["ID"] = $idUser;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $idRole;
            writeInLog("I", "Sessió iniciada", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Sessió iniciada',$('.messageBox'),1);");
            header("Location: dashboard.php");
        } else {
            writeInLog("E", "Credencials incorrectes");
            array_push($_SESSION['errors'], "displayMessage('Credencials incorrectes',$('.messageBox'),3);");
            header("Location: login.php");
        }
    } catch (PDOException $e) {
        writeInLog("E", "Error:" . $e->getMessage());
        array_push($_SESSION['errors'], "displayMessage('Error:" . $e->getMessage() . "',$('.messageBox'),3);");
        header("Location: login.php");
    }
}


function saveQuestions()
{
    try {
        $title = $_POST["questionTitle"];
        $typeQuestion = $_POST["typeQuestion"];

        $dbh = connToDB();
        $startSession = $dbh->prepare("INSERT INTO question (question,typeID) values (:title, :typeQuestion);");
        $startSession->bindParam(':title', $title);
        $startSession->bindParam(':typeQuestion', $typeQuestion);
        $done = $startSession->execute();

        $lastId = $dbh->lastInsertId();

        if ($done) {
            writeInLog("S", "La pregunta ha sigut guardada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta ha sigut guardada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "La pregunta no ha sigut guardada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta no ha sigut guardada correctament',$('.messageBox'),2);");
        }
        return $lastId;
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }
}

function saveOptionsofQuestions($lastId, $arrayOptions)
{
    try {
        foreach ($arrayOptions as $key => $value) {
            $startSession = connToDB()->prepare("INSERT INTO question_option (questionID,optionID) values (:questionID, :optionID);");
            $startSession->bindParam(':questionID', $lastId);
            $startSession->bindParam(':optionID', $value["ID"]);
            $done = $startSession->execute();
        }
        if ($done) {
            writeInLog("S", "Les opcions han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions han sigut guardades correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "Les opcions no han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions no han sigut guardades correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades: " . $th . ",$('.messageBox'),3);");
    }
}
function saveSimpleQuestion()
{
    try {
        $startSession = connToDB()->prepare("INSERT INTO question (question, typeID) values (:questionText, 3);");
        $startSession->bindParam(':questionText', $_POST['questionTitle']);
        // $startSession->bindParam(':typeID', 3);
        $done = $startSession->execute();

        if ($done) {
            writeInLog("S", "La pregunta ha sigut guardada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta ha sigut guardada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "La pregunta no ha sigut guardada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta no ha sigut guardada correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades: " . $th . ",$('.messageBox'),3);");
    }
}

function addSimpleOptions($arrayOptions)
{
    try {
        $lastQuestionId = connToDB()->prepare("select max(id) as id from question;");
        $lastQuestionId->execute();
        $lastIdQuestion = $lastQuestionId->fetch();
        $lastOptionId = connToDB()->prepare("select max(id) as id from option");
        $lastOptionId->execute();
        $lastIdOption = $lastOptionId->fetch();

        // $lastIdOption["id"];

        // $lastIdQuestion["id"];

        foreach ($arrayOptions as $key => $value) {
            $startSession = connToDB()->prepare("INSERT INTO option (answer) values (:answer);");
            $startSession->bindParam(":answer", $value);
            $done = $startSession->execute();
        }

        if ($done) {
            writeInLog("S", "Les opcions han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions han sigut guardades correctament',$('.messageBox'),0);");
            saveOptionsofSimpleQuestions($lastIdQuestion["id"], $lastIdOption["id"] + 1);
        } else {
            writeInLog("W", "Les opcions no han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions no han sigut guardades correctament',$('.messageBox'),2);");
        }

    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }
}

    function savePoll(){
        try {
            //Teachers of Poll
            $teachers = [];
            foreach($_POST['teachers'] as $teacher){
                $startSession = connToDB()->prepare("SELECT ID FROM `user` where user.username = :username;");
                $startSession -> bindParam(':username', $teacher);
                $startSession->execute();
                $teachers = $startSession->fetch();
            }

            //Questions of Poll
            $questions = $_POST['questions'];

            //Questions of Poll
            $students = [];
            if(isset($_POST['students'])){
                foreach($_POST['students'] as $student){
                    $startSession = connToDB()->prepare("SELECT ID FROM `user` where user.username = :username;");
                    $startSession -> bindParam(':username', $student);
                    $startSession->execute();
                    $students = $startSession->fetch();
                }
            }

            echo var_dump($teachers);
            echo var_dump($questions);
            echo var_dump($students);
        }catch (PDOException $e) {
            writeInLog("E", "Error:".$e->getMessage());
            array_push($_SESSION['errors'],"displayMessage('Error:".$e->getMessage()."',$('.messageBox'),3);");
            //header("Location: login.php");
        }
    }
function saveOptionsofSimpleQuestions($questionId, $lastId)
{
    try {

        $currentOptionId = connToDB()->prepare("select max(id) as id from option");
        $currentOptionId->execute();
        $currentOptionId = $currentOptionId->fetch();

        for ($i = $lastId; $i <= $currentOptionId["id"]; $i++) {
            $startSession = connToDB()->prepare("INSERT INTO question_option (questionID,optionID) values (:questionID, :optionID);");
            // echo "INSERT INTO question_option (questionID,optionID) values (".$questionId.", ".$i.");";
            $startSession->bindParam(':questionID', $questionId);
            $startSession->bindParam(':optionID', $i);
            $done = $startSession->execute();
        }

        if ($done) {
            writeInLog("S", "Les opcions de les preguntes han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions han sigut guardades correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "Les opcions de les preguntes no han sigut guardades correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Les opcions no han sigut guardades correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades: " . $th . ",$('.messageBox'),3);");
    }
}

if ((isset($_POST["userlog"]) && (!empty($_POST["userlog"]))) && (isset($_POST["passlog"]) && (!empty($_POST["passlog"])))) {
    login();
}

if ((isset($_POST["questionTitle"]) && (!empty($_POST["questionTitle"]))) && (isset($_POST["typeQuestion"]) && (!empty($_POST["typeQuestion"])))) {
    switch ($_POST["typeQuestion"]) {
        case 1:
            saveOptionsofQuestions(saveQuestions(), $_SESSION["arrayOptions"]);
            break;
        case 2:
            saveQuestions();
            break;
        case 3:
            saveSimpleQuestion();
            addSimpleOptions($_POST['options']);
            break;
    }
    header("Location: teacher.php");
}
?>