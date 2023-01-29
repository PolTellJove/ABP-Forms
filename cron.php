<?php 
    include 'utilities.php';
    function sendingPoll($userID, $pollID){
        try {
            $startSession = connToDB()->prepare("UPDATE abp_poll.student_poll as sp SET sp.send = 1 WHERE sp.studentID = :studentID AND sp.pollID = :pollID;");
            $startSession->bindParam(':studentID', $userID);
            $startSession->bindParam(':pollID', $pollID);
            $startSession->execute();
            writeInLog("SQL", "UPDATE `student_poll` as sp SET sp.send = 1 WHERE sp.studentID = $userID AND sp.pollID = $pollID;");
        }catch (PDOException $e) {
            writeInLog("E", "Error:" . $e->getMessage());
        }
    }

    function getPendingsPolls(){
        $startSession = connToDB()->prepare("SELECT u.email as email, u.username as user, p.title as poll, p.ID as pollID, u.ID as userID FROM abp_poll.user u INNER JOIN abp_poll.student_poll sp ON u.ID = sp.studentID INNER JOIN abp_poll.poll p ON sp.pollID = p.ID WHERE sp.reply = 0 and sp.send = 0 LIMIT 5;");
        $startSession->execute();
        writeInLog("SQL", "SELECT u.email as email, u.username as user, p.title as poll, p.ID as pollID, u.ID as userID FROM abp_poll.user u INNER JOIN abp_poll.student_poll sp ON u.ID = sp.studentID INNER JOIN abp_poll.poll p ON sp.pollID = p.ID WHERE sp.reply = 0 and sp.send = 0 LIMIT 5;");
        foreach($startSession as $user){
            sendingPoll($user['userID'], $user['pollID']);
            sendEmail($user['email'], $user['user'], $user['poll']);
        }
    }

    function noReplyPolls(){
        getPendingsPolls();
    }

    noReplyPolls();
?>