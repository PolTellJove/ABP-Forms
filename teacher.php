<?php
 session_start();
 include 'utilities.php';
 $user = logUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>
<body id='teacher'>
<?php include 'header.php';?>
    <div id='divTeacher'>
    <?php if($user['role'] == 1){   ?>
        <div id="divButtons">
            <button onclick="">CREAR PREGUNTA</button>
            <button onclick="">CREAR ENQUESTA</button>
            <button id='questionList'>LLISTAT PREGUNTES</button>
            <button id='pollList'>LLISTAT ENQUESTA</button>
        </div>
    <?php }?>
        <div id="divDinamic">
        <?php 
            $startSession = connToDB()->prepare("SELECT * FROM `poll`;");
            $startSession->execute();
            echo '<div id="polls">';
            foreach($startSession as $poll){
                echo "\n".'<p id='.$poll['ID'].'>'.$poll['title'].'</p>';
            }
            echo "\n".'</div>';

            $startSession = connToDB()->prepare("SELECT * FROM `question`;");
            $startSession->execute();
            echo '<div id="questions" hidden>';
            foreach($startSession as $question){
                echo "\n".'<p id='.$question['ID'].'>'.$question['question'].'</p>';
            }
            echo "\n".'</div>'
        ?>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
<script>
    function changeColor(button_id){
        $('button').css("background-color","#62929E");
        $(button_id).css("background-color","blue");
    }
    changeColor("#pollList");
        $(document).ready(function(){
            $("#questionList").click(function(){
                $("#polls").hide();
                $("#questions").show();
                changeColor('#questionList');
            });
        });

        $(document).ready(function(){
            $("#pollList").click(function(){
                $("#polls").show();
                $("#questions").hide();
                changeColor('#pollList');
            });
        });
    </script>
</html>