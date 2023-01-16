<?php
    session_start();
    include 'utilities.php';
    $_SESSION['errors'] = [];

    function login(){
        
        $dbh = new PDO('mysql:host=localhost; dbname=abp_poll', "root", "");
        //recogida de datos en Variables
        $email = $_POST["userlog"];
        $password = $_POST["passlog"];

        try {
            $stmn  = connToDB()->prepare("SELECT * FROM user WHERE user.email = :email AND user.password = SHA2(:pw,256);");
            $stmn -> bindParam(':email', $email);
            $stmn -> bindParam(':pw', $password);
            $stmn -> execute();

            while ($row = $stmn->fetch()) {
                $idUser =  $row["ID"];
                $username = $row["username"];
                $idRole =  $row["roleID"];
            }

            if (isset($idUser) && empty($idUser) == false){
                $_SESSION["ID"] = $idUser;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $idRole;
                header("Location: dashboard.php");
            }
            else{
                array_push($_SESSION['errors'],"displayMessage('Credencials incorrectes',$('.messageBox'),3);");
                header("Location: login.php");

            }
        }
        catch (PDOException $e) {
            array_push($_SESSION['errors'],"displayMessage('Error:".$e->getMessage()."',$('.messageBox'),3);");
            header("Location: login.php");

        }
    }


    function saveQuestions(){
        try {
            $title = $_POST["questionTitle"];
            $typeQuestion = $_POST["typeQuestion"];

            $startSession  = connToDB()->prepare("INSERT INTO question (question,typeID) values (:title, :typeQuestion);");
            $startSession -> bindParam(':title', $title);
            $startSession -> bindParam(':typeQuestion', $typeQuestion);
            $done = $startSession->execute();
            
            if ($done) {
                array_push($_SESSION['errors'],"displayMessage('La pregunta ha sigut guardada correctament',$('.messageBox'),0);");
            }
            else {
                array_push($_SESSION['errors'],"displayMessage('La pregunta no ha sigut guardada correctament',$('.messageBox'),2);");
            }
        } catch (\Throwable $th) {
            array_push($_SESSION['errors'],"displayMessage('Error en la conexió amb la base de dades:".$th."',$('.messageBox'),3);");
        }

    }

    function getQuestionIdByQuestion($question){
        $startSession  = connToDB()->prepare("SELECT ID FROM question WHERE question = :question;");
        $startSession -> bindParam(':question', $question);
        $startSession->execute();
        while ($row = $startSession->fetch()) {
            $idQuestion =  $row["ID"];
        }
        if ($idQuestion) {
            return $idQuestion;
        }
        return "";
    }

    function saveOptions(){
        try {
            $idQuestion = getQuestionIdByQuestion($_POST["questionTitle"]);
            $arrayOptions = $_SESSION["arrayOptions"];

            $stringRes = "";
            foreach ($arrayOptions as $key => $value) {
                $stringRes = $stringRes .",". $value;
            }

            $startSession  = connToDB()->prepare("INSERT INTO question_option (questionID,optionID) values (:questionID, :optionID);");
            $startSession -> bindParam(':questionID', $idQuestion);
            $startSession -> bindParam(':optionID', $arrayOptions);
            $done = $startSession->execute();
            
            if ($done) {
                array_push($_SESSION['errors'],"displayMessage('La o les opcions han sigut guardades correctament',$('.messageBox'),0);");
            }
            else {
                array_push($_SESSION['errors'],"displayMessage('La o les opcions no han sigut guardades correctament',$('.messageBox'),2);");
            }
        } catch (\Throwable $th) {
            array_push($_SESSION['errors'],"displayMessage('Error en la conexió amb la base de dades: optionID: ".$stringRes." tamaño:".count($arrayOptions).", idQues:".$idQuestion." ".$th."',$('.messageBox'),3);");
        }
    }


    if ( (isset($_POST["userlog"]) && (!empty($_POST["userlog"]))) && (isset($_POST["passlog"]) && (!empty($_POST["passlog"])))  ){
        login();
    }

    if ((isset($_POST["questionTitle"]) && (!empty($_POST["questionTitle"]))) && (isset($_POST["typeQuestion"]) && (!empty($_POST["typeQuestion"])))) {
        switch ($_POST["typeQuestion"]) {
            case 1:
                // saveQuestions();
                saveOptions();
                break;
            case 2:
                saveQuestions();
            break;
        }
        header("Location: teacher.php");

    }
?>