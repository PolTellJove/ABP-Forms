<?php   
    function connToDB(){
        try {
            $hostname = "localhost";
            $dbname = "abp_poll";
            $username = "root";
            $pw = "";
            $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
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
?>