<?php
session_start();
include 'utilities.php';


$_SESSION['errors'] = [];
$_SESSION['breadcrumbs '] = [];

 function login(){

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
    }
}




function updateOptions()
{
    try {
        for ($i = 0; $i < count($_POST['optionsQuestion']); $i++) {
            $startSession = connToDB()->prepare("UPDATE abp_poll.option SET answer = :title WHERE ID = :id;");
            $startSession->bindParam(':title', $_POST['optionsQuestion'][$i]);
            $startSession->bindParam(':id', $_POST['idsOptions'][$i]);
            echo "UPDATE abp_poll.option SET answer = ".$_POST['optionsQuestion'][$i]." WHERE ID = ".$_POST['idsOptions'][$i].";";
            $done = $startSession->execute();
        }
        $startSession->close();
        if ($done) {
            writeInLog("S", "La pregunta ha sigut editada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta ha sigut editada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "La pregunta no ha sigut editada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta no ha sigut editada correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }

}

if (isset($_POST['optionsQuestion'])) {
    updateQuestionNumericText($_POST['titleSimpleOption'], $_POST['idSimpleOption']);
    updateOptions();
    header("Location: teacher.php");
}

function updateQuestionNumericText($title, $id)
{
    try {
        $startSession = connToDB()->prepare("UPDATE question SET question = :title WHERE ID = :id;");
        $startSession->bindParam(':title', $title);
        $startSession->bindParam(':id', $id);
        $done = $startSession->execute();
        if ($done) {
            writeInLog("S", "La pregunta ha sigut editada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta ha sigut editada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "La pregunta no ha sigut editada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta no ha sigut editada correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }

}

if (isset($_POST['idQuestionEdit']) && isset($_POST['titleEditQuestion'])) {
    updateQuestionNumericText($_POST['titleEditQuestion'], $_POST['idQuestionEdit']);
    header("Location: teacher.php");
}

function getAllOptions($id)
{
    $startSession = connToDB()->prepare("SELECT * FROM `question_option` WHERE questionID = :id;");
    $startSession->bindParam(':id', $id);
    $startSession->execute();
    $_SESSION['arrayAllOptions'] = [];
    foreach ($startSession as $opinion) {
        array_push($_SESSION['arrayAllOptions'], $opinion['optionID']);
    }
    $_SESSION['optionsQuestion'] = [];
    foreach ($_SESSION['arrayAllOptions'] as $option) {
        $startSession = connToDB()->prepare("SELECT * FROM option where ID = :id");
        $startSession->bindParam(':id', $option);
        $startSession->execute();
        foreach ($startSession as $query) {
            array_push($_SESSION['optionsQuestion'], $query);
        }
    }
}

if (isset($_POST['idSimpleQuestionEdit'])) {
    getAllOptions($_POST['idSimpleQuestionEdit']);
    header("Location: teacher.php");
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
        $lastQuestionId = connToDB()->prepare("select max(id) as id from abp_poll.question;");
        $lastQuestionId->execute();
        $lastIdQuestion = $lastQuestionId->fetch();
        $lastOptionId = connToDB()->prepare("select max(id) as id from abp_poll.option");
        $lastOptionId->execute();
        $lastIdOption = $lastOptionId->fetch();

        // $lastIdOption["id"];

        // $lastIdQuestion["id"];

        foreach ($arrayOptions as $key => $value) {
            $startSession = connToDB()->prepare("INSERT INTO abp_poll.option (answer) values (:answer);");
            $startSession->bindParam(":answer", $value);
            $done = $startSession->execute();
            writeInLog("S", "INSERT INTO abp_poll.option (answer) values (".$value.");", $_SESSION["ID"]);
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
function addTeachersToPoll($teachers, $pollID)
{
    $checkTeachersPoll = true;
    for ($t = 0; $t < sizeof($teachers); $t++) {
        $startSession = connToDB()->prepare("INSERT INTO `teacher_poll`(`teacherID`, `pollID`) VALUES (:teacherID, :pollID);");
        $startSession->bindParam(':teacherID', $teachers[$t]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `teacher_poll`(`teacherID`, `pollID`) VALUES ('" . $teachers[$t] . "', '" . $pollID . "');", $_SESSION["ID"]);
        $checkTeachersPoll = $startSession->execute();
        if ($checkTeachersPoll) {
            writeInLog("S", "'ID: " . $pollID . " - Professor " . $teachers[$t] . " ha esta afegit al formulari", $_SESSION["ID"]);
        }
    }
    ;
    return $checkTeachersPoll;
}

function addQuestionsToPoll($questions, $pollID)
{
    $checkQuestionsPoll = true;
    for ($q = 0; $q < sizeof($questions); $q++) {
        $startSession = connToDB()->prepare("INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES (:questionID, :pollID);");
        $startSession->bindParam(':questionID', $questions[$q]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES ('" . $questions[$q] . "', '" . $pollID . "');", $_SESSION["ID"]);
        $checkQuestionsPoll = $startSession->execute();
        if ($checkQuestionsPoll) {
            writeInLog("S", "'ID: " . $pollID . " - Pregunta: " . $questions[$q] . " ha esta afegida al formulari", $_SESSION["ID"]);
        }
    }
    ;
    return $checkQuestionsPoll;
}

function addStudentsToPoll($students, $pollID)
{
    $checkStudentssPoll = true;
    for ($s = 0; $s < sizeof($students); $s++) {
        $startSession = connToDB()->prepare("INSERT INTO `student_poll`(`studentID`, `pollID`) VALUES (:studentID, :pollID);");
        $startSession->bindParam(':studentID', $students[$s]);
        $startSession->bindParam(':pollID', $pollID);
        writeInLog("SQL", "INSERT INTO `poll_question`(`questionID`, `pollID`) VALUES ('" . $students[$s] . "', '" . $pollID . "');", $_SESSION["ID"]);
        $checkStudentsPoll = $startSession->execute();
        if ($checkStudentsPoll) {
            writeInLog("S", "'ID: " . $pollID . " - Estudiant " . $students[$s] . " ha esta afegit al formulari", $_SESSION["ID"]);
        }
    }
    ;
    return $checkStudentsPoll;
}
function savePoll()
{
    try {
        //Teachers of Poll
        $teachers = $_POST['teachers'];

        //Questions of Poll
        $questions = [];
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $question) {
                array_push($questions, $question);
            }
        }

        //Students of Poll
        $students = [];
        if (isset($_POST['students'])) {
            foreach ($_POST['students'] as $student) {
                array_push($students, $student);
            }
        }

        //CREATE POLL
        $startSession = connToDB()->prepare("INSERT INTO `poll`(`title`, `startDate`) VALUES (:title, :startDate);");
        $startDate = date("Y-m-d H:i:s");
        if (!empty($_POST['startDate'])) {
            $startDate = str_replace("T", " ", $_POST['startDate']);
            $startDate = $startDate . ':00';
        }
        if (!empty($_POST['finishDate'])) {
            $finishDate = str_replace("T", " ", $_POST['finishDate']);
            $finishDate .= ':00';
            $startSession = connToDB()->prepare("INSERT INTO `poll`(`title`, `startDate`, `finishDate`) VALUES (:title, :startDate, :finishDate);");
            $startSession->bindParam(':finishDate', $finishDate);
        }
        $startSession->bindParam(':title', $_POST['pollTitle']);
        $startSession->bindParam(':startDate', $startDate);
        $checkPoll = $startSession->execute();
        writeInLog("SQL", "INSERT INTO `poll`(`title`, `startDate`, `finishDate`) VALUES (" . $_POST['pollTitle'] . ", $startDate);", $_SESSION["ID"]);
        if ($checkPoll) {
            $startSession = connToDB()->prepare("select max(id) as id from poll;");
            $startSession->execute();
            $poll = $startSession->fetch();
            //ID of last poll -> $poll["id"]; //
            writeInLog("S", "ID: " . $poll["id"] . " - La enquesta  ha estat enregistrada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La enquesta  ha estat enregistrada correctament',$('.messageBox'),0);");

            //Add teachers
            $teachersPoll = addTeachersToPoll($teachers, $poll["id"]);
            if ($teachersPoll) {
                array_push($_SESSION['errors'], "displayMessage('Professor/s afegit/s a la enquesta correctament',$('.messageBox'),0);");

                //Add questions
                $questionsPoll = addQuestionsToPoll($questions, $poll["id"]);
                if ($questionsPoll) {
                    array_push($_SESSION['errors'], "displayMessage('Pregunta/es afegida/es al formulari correctament',$('.messageBox'),0);");

                    //Add students
                    $studentsPoll = addStudentsToPoll($students, $poll["id"]);
                    if ($studentsPoll) {
                        array_push($_SESSION['errors'], "displayMessage('Estudiant/s afegit/s a la enquesta correctament',$('.messageBox'),0);");
                    }
                }
            }
        }
        header("Location: teacher.php");
    } catch (PDOException $e) {
        writeInLog("E", "Error:" . $e->getMessage(), $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error:" . $e->getMessage() . "',$('.messageBox'),3);");
        header("Location: login.php");
    }
}

function editPoll()
{
    $startSession = connToDB()->prepare("DELETE FROM `poll_question` WHERE pollID = :pollID;");
    $startSession->bindParam(':pollID', $_POST['IDpoll']);
    $checkPoll = $startSession->execute();

    $startSession = connToDB()->prepare("DELETE FROM `teacher_poll` WHERE pollID = :pollID;");
    $startSession->bindParam(':pollID', $_POST['IDpoll']);
    $startSession->execute();

    $startSession = connToDB()->prepare("DELETE FROM `student_poll` WHERE pollID = :pollID;");
    $startSession->bindParam(':pollID', $_POST['IDpoll']);
    $startSession->execute();

    $startSession = connToDB()->prepare("DELETE FROM `poll_question` WHERE pollID = :pollID;");
    $startSession->bindParam(':pollID', $_POST['IDpoll']);
    $startSession->execute();

    $startSession = connToDB()->prepare("UPDATE `poll` SET `title`= :title,`startDate`= :startDate,`finishDate`= :finishDate WHERE poll.ID = :pollID");
    $startSession->bindParam(':title', $_POST['pollTitle']);
    $startSession->bindParam(':startDate', $_POST['startDate']);
    $startSession->bindParam(':finishDate', $_POST['finishDate']);
    $startSession->bindParam(':pollID', $_POST['IDpoll']);
    $startSession->execute();

    //Teachers of Poll
    $teachers = $_POST['teachers'];

    //Questions of Poll
    $questions = [];
    if (isset($_POST['questions'])) {
        $questions = $_POST['questions'];
    }

    //Students of Poll
    $students = [];
    if (isset($_POST['students'])) {
        $students = $_POST['students'];
    }

    addTeachersToPoll($teachers, $_POST['IDpoll']);
    addQuestionsToPoll($questions, $_POST['IDpoll']);
    addStudentsToPoll($students, $_POST['IDpoll']);

    array_push($_SESSION['errors'], "displayMessage('Enquesta actualitzada correctament',$('.messageBox'),0);");
    header("Location: teacher.php");
}

function saveOptionsofSimpleQuestions($questionId, $lastId)
{
    try {

        $currentOptionId = connToDB()->prepare("select max(id) as id from abp_poll.option");
        $currentOptionId->execute();
        $currentOptionId = $currentOptionId->fetch();

        for ($i = $lastId; $i <= $currentOptionId["id"]; $i++) {
            $startSession = connToDB()->prepare("INSERT INTO question_option (questionID,optionID) values (:questionID, :optionID);");
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

function removeQuestion($id)
{
    try {
        $startSession = connToDB()->prepare("UPDATE `question` SET active = 1 WHERE ID = :idOption");
        $startSession->bindParam(':idOption', $id);
        $done = $startSession->execute();

        if ($done) {
            writeInLog("S", "La pregunta ha sigut esborrada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta ha sigut esborrada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "La pregunta no ha sigut esborrada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('La pregunta no ha sigut esborrada correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }
}


if (isset($_POST['idQuestionToDelete'])) {
    removeQuestion($_POST['idQuestionToDelete']);
    header("Location: teacher.php");
}

function removePoll($id)
{
    try {
        $startSession = connToDB()->prepare("UPDATE `poll` SET active = 1 where ID = :idPoll");
        $startSession->bindParam(":idPoll", $id);
        $done = $startSession->execute();

        if ($done) {
            writeInLog("S", "Enquesta esborrada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Enquesta esborrada correctament',$('.messageBox'),0);");
        } else {
            writeInLog("W", "Enquesta no ha sigut esborrada correctament", $_SESSION["ID"]);
            array_push($_SESSION['errors'], "displayMessage('Enquesta no ha sigut esborrada correctament',$('.messageBox'),2);");
        }
    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }
}

if (isset($_POST['idPollToDelete'])) {
    removePoll($_POST['idPollToDelete']);
    header("Location: teacher.php");
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
if (isset($_POST['pollTitle'])) {
    if ($_POST['IDpoll']) {
        editPoll();
    } else {
        savePoll();
    }
}

function getPolls($email, $reply){
    try {
        $startSession = connToDB()->prepare("SELECT p.title from poll p INNER JOIN student_poll sp on p.ID=sp.pollID where sp.studentID = :id and sp.reply = :reply");
        $startSession->bindParam(":id", getIDUserRecoveredPassword($email));
        $startSession->bindParam(":reply", $reply);
        $done = $startSession->execute();

        if($reply == 0){
            $_SESSION['pollsNoReply'] = [];
            foreach ($startSession as $poll) {
                array_push($_SESSION['pollsNoReply'], $poll);
            }
        }
        else{
            $_SESSION['pollsReply'] = [];
            foreach ($startSession as $poll) {
                array_push($_SESSION['pollsReply'], $poll);
            }
        }

        if ($done) {
            if($reply == 0){
                writeInLog("SQL", "SELECT p.title from poll p INNER JOIN student_poll sp on p.ID=sp.pollID where sp.studentID = ".getIDUserRecoveredPassword($email)." and sp.reply =".$reply."", $_SESSION["ID"]);
                writeInLog("S", "Enquestes no realitzades trobades correctament");
            }
            else{
                writeInLog("SQL", "SELECT p.title from poll p INNER JOIN student_poll sp on p.ID=sp.pollID where sp.studentID = ".getIDUserRecoveredPassword($email)." and sp.reply =".$reply."", $_SESSION["ID"]);
                writeInLog("S", "Enquestes realitzades trobades correctament");
            }
            
        } else {
            if($reply == 0){
                writeInLog("SQL", "SELECT p.title from poll p INNER JOIN student_poll sp on p.ID=sp.pollID where sp.studentID = ".getIDUserRecoveredPassword($email)." and sp.reply =".$reply."", $_SESSION["ID"]);
                writeInLog("S", "Enquestes no realitzades no trobades correctament");
            }
            else{
                writeInLog("SQL", "SELECT p.title from poll p INNER JOIN student_poll sp on p.ID=sp.pollID where sp.studentID = ".getIDUserRecoveredPassword($email)." and sp.reply =".$reply."", $_SESSION["ID"]);
                writeInLog("S", "Enquestes realitzades no trobades correctament");
            }
        }

    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
    }

}

if(isset($_POST['userGetPoll'])){
    $isUserStudent = false;
    foreach ($_SESSION['emailStudents'] as $key => $value) {
        if ($_SESSION['emailStudents'][$key]["email"] == $_POST['userGetPoll']) {
            $isUserStudent = true;
        }
    }
    if($isUserStudent) {
        getPolls($_POST['userGetPoll'],0);
        getPolls($_POST['userGetPoll'],1);

        $listNoReply = "<ul>";
        foreach ($_SESSION['pollsNoReply'] as $key => $value) {
            $listNoReply .= "<li><a href='#'>".$_SESSION['pollsNoReply'][$key]['title']."</a></li>";
        };
        $listNoReply .= "</ul>";

        $listReply = "<ul>";
        foreach ($_SESSION['pollsReply'] as $key => $value) {
            $listReply .= "<li><a href='#'>".$_SESSION['pollsReply'][$key]['title']."</a></li>";
        };
        $listReply .= "</ul>";
        $message = "<html>
        <body>
        <div>Enquestes pendents: </div><div></div>".
        $listNoReply
        ."
        <div>Enquestes realitzades:</div><div></div>".
        $listReply
        ."
        </body>
        </html>";
        sendEmail($_POST['userGetPoll'], "Enquestes pendents", $message);
    }
    array_push($_SESSION['errors'], "displayMessage('Si el correu electònic introduit és correcte, rebràs un correu automàticament. ',$('.messageBox'),1);");
    header("Location: get_polls.php");
}



if (isset($_POST['emailFor'])) {
    $userExist = false;
    foreach ($_SESSION['users'] as $key => $value) {
        if ($_SESSION['users'][$key]["email"] == $_POST['emailFor']) {
            $userExist = true;
        }
    }
    if($userExist){
        $preToken = md5("forgot");
        $userIDEncrypt = md5(getIDUserRecoveredPassword($_POST['emailFor']));
        $postToken = md5("finish");
        $token = $preToken.$userIDEncrypt.$postToken;
        $path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
        $_SESSION['token'] = $token;
        $_SESSION['emailUser'] = $_POST['emailFor'];
        $linkToRecoverPassword = "<a href='$path/forgot_password.php?".$token."'>Canviar contrasenya</a>";
        $message = "<html>
        <body>
        <div>Clica per canviar la contrasenya: </div><br>".$linkToRecoverPassword."
        </body>
        </html>";
        sendEmail($_POST['emailFor'], "Canviar contrasenya", $message);
        writeInLog("I", $_POST['emailFor']."Correu electrònic trobat al intentar recuperar contrasenya");
    } 
    else {
        writeInLog("I", $_POST['emailFor']."Correu electrònic no trobat al intentar recuperar contrasenya");
    }
    array_push($_SESSION['errors'], "displayMessage('Si el correu electònic introduit és correcte, rebràs un correu automàticament. ',$('.messageBox'),1);");
    header("Location: forgot_password.php");
}

function getIDUserRecoveredPassword($email){
    try {
        $startSession = connToDB()->prepare("SELECT ID FROM abp_poll.user where email = :email");
        $startSession->bindParam(":email", $email);
        $done = $startSession->execute();

        if ($done) {
            writeInLog("SQL", "SELECT ID FROM abp_poll.user where email = ".$email."", $_SESSION["ID"]);
            writeInLog("S", "ID d'usuari que vol recuperar contrasenya trobat");
        } else {
            writeInLog("W", "ID d'usuari que vol recuperar contrasenya no trobat");
        }
        $idUserRecovered = $startSession->fetch();

        return $idUserRecovered['ID'];    

    } catch (\Throwable $th) {
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
    }
}



function updatePassword($id, $password){
    try{
        $startSession = connToDB()->prepare("UPDATE abp_poll.user SET password = :password1 where ID =:id");
        $startSession->bindParam(":password1", $password);
        $startSession->bindParam(":id", $id);
        $done = $startSession->execute();

        if ($done) {
            writeInLog("SQL", "UPDATE abp_poll.user SET password = ".$password." where ID =".$id."", $_SESSION["ID"]);
            writeInLog("S", "Contrasenya actualitzada correctament", $id);
        } else {
            writeInLog("W", "Contrasenya no actualitzada correctament", $id);
        }
    }
    catch (\Throwable $th){
        writeInLog("E", "Error en la conexió amb la base de dades:" . $th, $_SESSION["ID"]);
        array_push($_SESSION['errors'], "displayMessage('Error en la conexió amb la base de dades:" . $th . "',$('.messageBox'),3);");
    }
}

if(isset($_POST['recoverPassword1']) && isset($_POST['recoverPassword2'])){
    $specialChar = false;
    $minusChar = false;
    $mayusChar = false;
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if($_POST['recoverPassword1'] == $_POST['recoverPassword2']){

        if(strlen($_POST['recoverPassword1']) >= 8){
            if(preg_match("/[A-Z]/", $_POST['recoverPassword1'])){
                $mayusChar = true;
            }
            if(preg_match("/[a-z]/", $_POST['recoverPassword1'])){
                $minusChar = true;
            }
            if(preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $_POST['recoverPassword1'])){
                $specialChar = true;
            }
        }
        else{
            array_push($_SESSION['errors'], "displayMessage('La contrasenya mínim ha de tenir 8 caràcters. ',$('.messageBox'),2);");
            header("Location: forgot_password.php?".$_SESSION['token']);
        }
        if($specialChar == true && $minusChar == true && $mayusChar == true){
            updatePassword(getIDUserRecoveredPassword($_SESSION['emailUser']), hash('sha256',$_POST['recoverPassword1']));
            array_push($_SESSION['errors'], "displayMessage('Contrasenya actualitzada correctament',$('.messageBox'),0);");
            header("Location: login.php");
        }
        
        else{
            array_push($_SESSION['errors'], "displayMessage('La contrasenya ha de tenir mínuscules i majúscules i al menys un caràcter especial. ',$('.messageBox'),2);");
            header("Location: forgot_password.php?".$_SESSION['token']);
        }

    }
    else if ($_POST['recoverPassword1'] != $_POST['recoverPassword2']){
        array_push($_SESSION['errors'], "displayMessage('Les contrasenyes no coincideixen. ',$('.messageBox'),2);");
        header("Location: forgot_password.php?".$_SESSION['token']);
    }

}

if(isset($_POST['replyPoll'])){
    $questionsID = [];
    foreach ($_POST as $key => $value) {
        $question = explode("-", $key);
        if($question[2]){
            try{
                if($question[1] == 'n2'){ //OPEN ANSWER
                    writeInLog("SQL", "INSERT INTO abp_poll.user_answer(pollID, questionID,  answer, studentID`, teacherID) VALUES (".$_POST['poll'].", ".$question[2].",".$value.", ".$_POST['student'].", ".$_POST['teacher'].");", $_POST['student']);

                    $startSession = connToDB()->prepare("INSERT INTO abp_poll.user_answer(pollID, questionID,  answer, studentID`, teacherID) VALUES (:poll, :question, :answer, :student, :teacher);");
                    $startSession->bindParam(":question", $question[2]);
                    $startSession->bindParam(":teacher", $_POST['teacher']);
                    $startSession->bindParam(":student", $_POST['student']);
                    $startSession->bindParam(":student", $_POST['poll']);
                    $startSession->bindParam(":answer", $value);
                    $startSession->execute();


                }else if($question[1] == 'n1' || $question[1] == 'n3'){ //OPTION ANSWER
                    writeInLog("SQL", "INSERT INTO abp_poll.user_answer(`pollID`, `questionID`, `optionID`, `studentID`, `teacherID`) VALUES (".$_POST['poll'].", ".$question[2].",".$value.", ".$_POST['student'].", ".$_POST['teacher'].");", $_POST['student']);

                    $startSession = connToDB()->prepare("INSERT INTO abp_poll.user_answer(`pollID`, `questionID`, `optionID`, `studentID`, `teacherID`) VALUES (:poll, :question, :answer, :student, :teacher);");
                    $startSession->bindParam(":question", $question[2]);
                    $startSession->bindParam(":teacher", $_POST['teacher']);
                    $startSession->bindParam(":student", $_POST['student']);
                    $startSession->bindParam(":student", $_POST['poll']);
                    $startSession->bindParam(":answer", $value);
                    $startSession->execute();
                }
            }catch (\Throwable $th){
                writeInLog("E", "NO S'HA POGUT DESAR LA RESPOSTA DEL ALUMNE" . $th, $_SESSION["ID"]);
                array_push($_SESSION['errors'], "displayMessage('Preguntes no desades',$('.messageBox'),3);");
            }
        }
    }
}
?>