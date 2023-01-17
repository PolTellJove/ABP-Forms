<?php
    session_start();
    include 'utilities.php';
    if(!isset($_SESSION["ID"])) {
        header("Location: login.php");
    }
    $user = logUser();
?>
<!DOCTYPE html>
<html lang="en" id="htmlDashboard">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/277f72a273.js" crossorigin="anonymous"></script>
</head>
<body id='dashboard'>
<?php include 'header.php';?>
    <div id='divDashboard'>
        <div class="containerLogoutBtn">
            <button  onclick="location.href = './logout.php'" class="btnLogout"> <i class="fa fa-solid fa-right-from-bracket"></i> LogOut</button> 
        </div>
    <?php   
        if($user['role'] == 1){
            ?>
            <h1>Admin: <?php echo $user['username']?></h1>
            <?php
        }else{
            ?>
            <h1>Professor: <?php echo $user['username']?></h1>
            <?php
        } 
        ?>
        
        <div id="divButtons">
            <a class="button" href="stats.php">ESTADISTÍQUES</a>
            <?php
            if($user['role'] == 1){
                ?>
                <a class="button" href="">USUARIS</a>
                <a class="button" href="teacher.php">ENQUESTES</a>
                <?php
            }else{
                ?>
                <a class="button" href="profile.php">PERFIL</a>
                <?php
            } 
        ?>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>