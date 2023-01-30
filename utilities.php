<?php
require __DIR__ . '/log.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
    
    function connToDB(){
        try {
            $hostname = "127.0.0.1";
            $dbname = "abp_poll";
            $username = "root";
            $pw = "";
            $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                exit;
            }
            return $pdo;
    }

    function logUser(){
        $startSession = connToDB()->prepare("SELECT * FROM `user` WHERE user.ID = :id;");
        $startSession->bindParam(':id', $_SESSION['ID']);
        $startSession->execute();
        $userInformation = [];
        foreach($startSession as $user){
            $userInformation['username'] = $user['username'];
            $userInformation['role'] = $user['roleID'];
            $userInformation['email'] = $user['email'];
        }
        return $userInformation;
    }

    function getTable($tableName){
        $startSession = connToDB()->prepare("SELECT * FROM $tableName;");
        $startSession->execute();
        $rows = [];
        foreach ($startSession as $row) {
            array_push($rows, $row);
        }
        return $rows;
    }

    function showErrors(){
        if (isset($_SESSION['errors']) && (!empty($_SESSION["errors"]))) {
            foreach ($_SESSION['errors'] as $key => $value) {
                echo "
                        <script>
                            " . $value . "
                        </script>";
            }
            $_SESSION['errors'] = [];
        }
    }

    function writeInLog($type,$message,$id = "No Logged"){
        $log = new Log("logs/log".date('dmY'));
        $log->writeLine($type, $message,$id);
    }

    function sendEmail($to, $subject, $messageContent){
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Mailer = "smtp";
        $mail->SMTPDebug  = 1;  
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "alariosalmendros.cf@iesesteveterradas.cat";
        $mail->Password   = "";
        $mail->IsHTML(true);
        $mail->AddAddress($to);
        $mail->SetFrom("alariosalmendros.cf@iesesteveterradas.cat", "Alex Larios");
        $mail->Subject  = $subject;
        $mail->Body = $messageContent;
        $mail->send();

        writeInLog("EMAIL", "To: ".$to);
    }
?>