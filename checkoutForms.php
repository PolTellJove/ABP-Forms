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
function addTeachersToPoll($teachers, $pollID){
    $checkTeachersPoll = true;
    for($t = 0; $t < sizeof($teachers); $t++){
        $startSession = connToDB()->prepare("INSERT INTO `teacher_poll`(`teacherID`, `pollID`) VALUES (:teacherID, :pollID);");
        $startSession->bindParam(':teacherID', $teachers[$t]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `teacher_poll`(`teacherID`, `pollID`) VALUES ('".$teachers[$t]."', '".$pollID."');", $_SESSION["ID"]);
        $checkTeachersPoll = $startSession->execute();
        if($checkTeachersPoll){
            writeInLog("S", "'ID: ". $pollID." - Professor ".$teachers[$t]." ha esta afegit al formulari", $_SESSION["ID"]);
        }else{
            writeInLog("E", "Error:".$e->getMessage(), $_SESSION["ID"]);
            break;
        }
    };
    return $checkTeachersPoll;
}

function addQuestionsToPoll($questions, $pollID){
    $checkQuestionsPoll = true;
    for($q = 0; $q < sizeof($questions); $q++){
        $startSession = connToDB()->prepare("INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES (:questionID, :pollID);");
        $startSession->bindParam(':questionID', $questions[$q]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES ('".$questions[$q]."', '".$pollID."');", $_SESSION["ID"]);
        $checkQuestionsPoll = $startSession->execute();
        if($checkQuestionsPoll){
            writeInLog("S", "'ID: ". $pollID." - Pregunta: ".$questions[$q]." ha esta afegida al formulari", $_SESSION["ID"]);
        }else{
            writeInLog("E", "Error:".$e->getMessage(), $_SESSION["ID"]);
            break;
        }
        return $checkQuestionsPoll;
    };
}

function addStudentsToPoll($students, $pollID){
    $checkStudentssPoll = true;
    for($s = 0; $s < sizeof($students); $s++){
        $startSession = connToDB()->prepare("INSERT INTO `student_poll`(`studentID`, `pollID`) VALUES (:studentID, :pollID);");
        $startSession->bindParam(':studentID', $students[$s]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES ('".$students[$s]."', '".$pollID."');", $_SESSION["ID"]);
        $checkStudentsPoll = $startSession->execute();
        if($checkStudentsPoll){
            writeInLog("S", "'ID: ". $pollID." - Professor ".$students[$s]." ha esta afegit al formulari", $_SESSION["ID"]);
        }else{
            writeInLog("E", "Error:".$e->getMessage(), $_SESSION["ID"]);
            break;
        }
        return $checkStudentsPoll;
    };
}
    function savePoll(){
        try {
            //Teachers of Poll
            $teachers = $_POST['teachers'];

            //Questions of Poll
            $questions = [];
            if(isset($_POST['questions'])){$questions = $_POST['questions'];}

            //Students of Poll
            $students = [];
            if(isset($_POST['students'])){$students = $_POST['students'];}

            //CREATE POLL
            $startSession = connToDB()->prepare("INSERT INTO `poll`(`title`, `startDate`) VALUES (:title, :startDate);");
            $startDate = date("Y-m-d H:i:s");
            if(!empty($_POST['startDate'])){
                $startDate = str_replace("T"," ",$_POST['startDate']);
                $startDate = $startDate.':00';
            }
            if(!empty($_POST['finishDate'])){
                $finishDate = str_replace("T"," ",$_POST['finishDate']);
                $finishDate .= ':00';
                $startSession = connToDB()->prepare("INSERT INTO `poll`(`title`, `startDate`, `finishDate`) VALUES (:title, :startDate, :finishDate);");
                $startSession->bindParam(':finishDate', $finishDate);
            }
            $startSession->bindParam(':title', $_POST['pollTitle']);
            $startSession->bindParam(':startDate', $startDate);
            $checkPoll = $startSession->execute();
            writeInLog("SQL", "INSERT INTO `poll`(`title`, `startDate`, `finishDate`) VALUES (".$_POST['pollTitle'].", $startDate);", $_SESSION["ID"]);
            if($checkPoll){
                $startSession = connToDB()->prepare("select max(id) as id from poll;");
                $startSession->execute();
                $poll = $startSession->fetch();
                //ID of last poll -> $poll["id"]; //
                writeInLog("S", "ID: ". $poll["id"]." - La enquesta  ha estat enregistrada correctament", $_SESSION["ID"]);
                array_push($_SESSION['errors'], "displayMessage('La enquesta  ha estat enregistrada correctament',$('.messageBox'),0);");
                
                //Add teachers
                $teachersPoll = addTeachersToPoll($teachers, $poll["id"]);
                if($teachersPoll){
                    array_push($_SESSION['errors'], "displayMessage('Professor/s afegit/s a la enquesta correctament',$('.messageBox'),0);");
                    
                    //Add questions
                    $questionsPoll = addQuestionsToPoll($questions, $poll["id"]);
                    if($questionsPoll){
                        array_push($_SESSION['errors'], "displayMessage('Pregunta/es afegida/es al formulari correctament',$('.messageBox'),0);");

                        //Add students
                        $studentsPoll = addStudentsToPoll($students, $poll["id"]);
                        if($studentsPoll){
                            array_push($_SESSION['errors'], "displayMessage('Estudiant/s afegit/s a la enquesta correctament',$('.messageBox'),0);");
                        }
                    }
                }
            }
            header("Location: teacher.php");
        }catch (PDOException $e) {
            writeInLog("E", "Error:".$e->getMessage(), $_SESSION["ID"]);
            array_push($_SESSION['errors'],"displayMessage('Error:".$e->getMessage()."',$('.messageBox'),3);");
            header("Location: login.php");
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


//Create new poll
if(isset($_POST['pollTitle'])){
    savePoll();
    
}
?>