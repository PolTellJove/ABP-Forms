<?php
 session_start();
 include 'utilities.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<?php include 'header.php';?>
<body id='dashboard'>
    <?php 
        $startSession = connToDB()->prepare("SELECT * FROM `user` WHERE user.username = 'Alex';");
        //$startSession->bindParam(':username', 'Alex');
        $startSession->execute();

        foreach($startSession as $user){
            $_SESSION['ID'] = $user['ID'];
            $user = logUser();
        }
    ?>
    <div id='dashButtons'>
        <button onclick="window.location.href='stats.php'">ESTADISTIQUES</button>
        <?php
        if($user['role'] == 1){
            ?>
            <button onclick="window.location.href='teacher.php'">PROFESORS</button>
            <button onclick="window.location.href='poll.php'">ENQUESTES</button>
            <?php
        }else{
            ?>
            <button onclick="window.location.href='profile.php'">PROFILE</button>
            <?php
        } 
        ?>
    </div>
</body>
<?php include 'footer.php';?>
</html>