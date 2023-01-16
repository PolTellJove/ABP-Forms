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
            <div class = "messageBox"></div>
            <div id="divButtons">
                <button id='createQuestion'>CREAR PREGUNTA</button>
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
            echo "\n".'</div>';

            echo '<form action="checkoutForms.php" method="POST" id="newQuestion" hidden>';
                echo "<input name='questionTitle' type='text' id='questionTitle'><br>";
                echo '<select name="typeQuestion" id="typeSelect">';
                    echo "\n".'<option id="0" selected></option><br>';
                    $startSession = connToDB()->prepare("SELECT * FROM `type_of_question`;");
                    $startSession->execute();
                    foreach($startSession as $type_option){
                        echo "\n".'<option id='.$type_option['ID'].' value='.$type_option['ID'].'>'.$type_option['name'].'</option>';
                    }
                echo "\n".'</select><br><br>';
                echo '<textarea id="taQuestion" rows="5" cols="33" disabled></textarea>';
                echo '<div id="radioGroup">';
                    $startSession = connToDB()->prepare("SELECT * FROM `option` WHERE ID <= 5;");
                    $startSession->execute();
                    $_POST['arrayOptions'] = [];
                    foreach($startSession as $opinion){
                        echo "\n".'<a><input type="radio" id="'.$opinion['ID'].'" name="score" value="'.$opinion['ID'].'" disabled><label for="'.$opinion['ID'].'">'.$opinion['answer'].'</label></a>';
                        array_push($_POST['arrayOptions'], $opinion['ID']);
                    }
                echo "\n".'</div>';
                echo '<br><input id="saveQuestion" type="submit" value="Guardar"/>';
                echo '<input id="clearForm" type="reset" value="Cancelar"/>';
            echo "\n".'</form>';
        ?>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
<script>
    $('#radioGroup').hide();
    $("#taQuestion").hide();
    $("#saveQuestion").hide();

    function changeColor(button_id){
        $('button').css("background-color","#62929E");
        $(button_id).css("background-color","blue");
    }

    changeColor("#pollList");
        $(document).ready(function(){
            $("#questionList").click(function(){
                $("#polls").hide();
                $("#newQuestion").hide();
                $("#questions").show();
                changeColor('#questionList');
            });

            $("#pollList").click(function(){
                $("#questions").hide();
                $("#newQuestion").hide();
                $("#polls").show();
                changeColor('#pollList');
            });

            $("#createQuestion").click(function(){
                $("#polls").hide();
                $("#questions").hide();
                $("#newQuestion").show();
                changeColor('#createQuestion');
            });;

            $('#typeSelect').on('change', function() {
                if($("#typeSelect option:selected").attr("id") == 2){
                    $('#radioGroup').hide();
                    $("#taQuestion").show();
                }else if($("#typeSelect option:selected").attr("id") == 1){
                    $("#taQuestion").hide();
                    $('#radioGroup').show();
                }else if($("#typeSelect option:selected").attr("id") == 0){
                    $('#radioGroup').hide();
                    $("#taQuestion").hide();
                }

                if(document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0){
                    $("#saveQuestion").show();
                }else{
                    $("#saveQuestion").hide();
                }
            });

            $('#questionTitle').on('input',function(e){
                if(/^\s/.test($('#questionTitle').val())){
                   $('#questionTitle').val('');
                }
                if(document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0){
                    $("#saveQuestion").show();
                }else{
                    $("#saveQuestion").hide();
                }
            });
        });     
    </script>
    <?php
        if (isset($_SESSION['errors']) && (!empty($_SESSION["errors"]))) {
            foreach ($_SESSION['errors'] as $key => $value) {
                echo "
                <script>
                    ".$value."
                </script>";
            }
            $_SESSION['errors'] = [];
        }
    ?>
</html>