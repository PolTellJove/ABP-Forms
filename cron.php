<?php 
    include 'utilities.php';

    function sendingPoll($userID, $pollID){
        try {
            $startSession = connToDB()->prepare("UPDATE abp_poll.student_poll as sp SET sp.send = 1 WHERE sp.studentID = :studentID AND sp.pollID = :pollID;");
            $startSession->bindParam(':studentID', $userID);
            $startSession->bindParam(':pollID', $pollID);
            $startSession->execute();
            writeInLog("SQL", "UPDATE `student_poll` as sp SET sp.send = 1 WHERE sp.studentID = $userID AND sp.pollID = $pollID;", 'CRON');
        }catch (PDOException $e) {
            writeInLog("E", "Error:" . $e->getMessage());
        }
    }

    function generateTokenToReplyPoll($studentID, $pollID){
        $pre = md5("REPLY");
        $userIDEncrypt = md5($studentID.$pollID);
        $post = md5("POLL");
        $token = $pre.$userIDEncrypt.$post;
        return $token;
    }

    function createURLtoReply($studentID, $pollID, $token){
        $parametersURL = "?s=".$studentID."&p=".$pollID."&k=".$token;
        $path = "alexlarios.es/view_poll.php";
        $URL = $path.$parametersURL;
        return $URL;
    }   

    function getPendingsPolls(){
        try {
            $startSession = connToDB()->prepare("SELECT u.email as email, u.username as user, p.title as poll, p.ID as pollID, u.ID as userID, tp.teacherID as teacherID FROM abp_poll.user u INNER JOIN abp_poll.student_poll sp ON u.ID = sp.studentID INNER JOIN abp_poll.poll p ON sp.pollID = p.ID INNER JOIN teacher_poll tp on p.ID = tp.pollID WHERE p.active = 0 AND sp.reply = 0 and sp.send = 0 LIMIT 5;");
            $startSession->execute();
            writeInLog("SQL", "SELECT u.email as email, u.username as user, p.title as poll, p.ID as pollID, u.ID as userID, tp.teacherID as teacherID FROM abp_poll.user u INNER JOIN abp_poll.student_poll sp ON u.ID = sp.studentID INNER JOIN abp_poll.poll p ON sp.pollID = p.ID INNER JOIN teacher_poll tp on p.ID = tp.pollID WHERE p.active = 0 AND sp.reply = 0 and sp.send = 0 LIMIT 5;", 'CRON');
            foreach($startSession as $user){
                sendingPoll($user['userID'], $user['pollID']);
                $token = generateTokenToReplyPoll($user['userID'], $user['pollID']);
                $URL = createURLtoReply($user['userID'], $user['pollID'], $token);
                $linkToPoll = '<a href="'.$URL.'">'.$user['poll'].'</a>';
                $message = "<html>
                <body>
                <div style='color: black !important'>Hola ".$user['user'].", encara no has respost aquesta enquesta:</div><br>"
                .$linkToPoll."
                </body>
                </html>";
                sendEmail($user['email'], 'Enquesta pendent', $message);
            }
        }catch (PDOException $e) {
            writeInLog("E", "Error:" . $e->getMessage());
        }
    }

    function noReplyPolls(){
        getPendingsPolls();
    }

    noReplyPolls();
?>