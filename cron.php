<?php 
    include 'utilities.php';
    function getNoSendEmails(){
        $startSession = connToDB()->prepare("SELECT u.email as email, u.username as user, u.ID as userID, sp.pollID as poll FROM abp_poll.user u INNER JOIN abp_poll.student_poll sp ON u.ID = sp.studentID WHERE sp.reply = 0 and sp.reply = 0;");
        $startSession->execute();
        foreach ($startSession as $user) {
            var_dump($user);
        }
    }
    function noReplyPolls(){
        getNoSendEmails();
    }
    noReplyPolls();
?>