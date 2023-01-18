<?php
session_start();
$_GET['titlePage'] = 'Teacher';
$_GET['bodyID'] = 'teacher';
$_GET['bodyClass'] = '';
include 'utilities.php';
$user = logUser();
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<div id='divTeacher'>
    <?php if ($user['role'] == 1) {   ?>
        <br>
        <div class="messageBox"></div>
        <div id="divButtons">
            <a class="button" id='createQuestion'>CREAR PREGUNTA <i class="fa-regular fa-circle-question"></i></a>
            <a class="button">CREAR ENQUESTA <i class="fa-solid fa-square-poll-vertical"></i></a>
            <a class="button" id='questionList'>LLISTAT PREGUNTES <i class="fa-solid fa-list"></i></a>
            <a class="button active" id='pollList'>LLISTAT ENQUESTA <i class="fa-solid fa-list"></i></a>
        </div>
    <?php } ?>
    <div id="divDinamic">
        <?php
        function getPolls(){
            $polls = getTable('poll');
            echo '<div id="polls">';
            foreach ($polls as $poll) {
                echo "\n" . '<p id=' . $poll['ID'] . '>' . $poll['title'] . '</p>';
            }
            echo "\n" . '</div>';
        }

        function getQuestions(){
            $questions = getTable('question');
            echo '<div id="questions" hidden>';
            foreach ($questions as $question) {
                echo "\n" . '<p id=' . $question['ID'] . '>' . $question['question'] . '</p>';
            }
            echo "\n" . '</div>';
        }
        
        function getTypes(){
            echo '<select name="typeQuestion" id="typeSelect">';
            echo "\n" . '<option id="0" selected disabled>TIPUS DE PREGUNTA</option><br>';
            $typesQuestion = getTable('type_of_question');
            foreach ($typesQuestion as $type_option) {
                echo "\n" . '<option id=' . $type_option['ID'] . ' value=' . $type_option['ID'] . '>' . $type_option['name'] . '</option>';
            }
            echo "\n" . '</select><br>';
        }

        function getOptions(){
            echo '<div id="radioGroup">';
            $startSession = connToDB()->prepare("SELECT * FROM `option` WHERE ID <= 5;");
            $startSession->execute();
            $_SESSION['arrayOptions'] = [];
            foreach ($startSession as $opinion) {
                echo "\n" . '<a id="radioButton"><input type="radio" id="' . $opinion['ID'] . '" name="score" value="' . $opinion['ID'] . '" disabled><label for="' . $opinion['ID'] . '">' . $opinion['answer'] . '</label></a>';
                array_push($_SESSION['arrayOptions'], $opinion['ID']);
            }
            echo "\n" . '</div>';
        }

        function newQuestion(){
            echo '<form action="checkoutForms.php" method="POST" id="newQuestion" hidden>';
            getTypes();
            echo "<input type='text' name='questionTitle' id='questionTitle'><br>";
            echo '<textarea id="taQuestion" disabled></textarea><br>';
            getOptions();
            echo '<input id="saveQuestion" type="submit" value="Guardar"/>';
            echo '<input id="clearForm" type="reset" value="Cancelar"/>';
            echo "\n" . '</form>';
        }
        getPolls();
        getQuestions();
        newQuestion();
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
<script>
    $('#radioGroup').hide();
    $("#taQuestion").hide();
    $("#saveQuestion").hide();

    function changeColor(button_id) {
        $('.button').css("background-color", "#62929E");
        $(button_id).css("background-color", "blue");
    }

    //changeColor("#pollList");
    $(document).ready(function() {
        $("#questionList").click(function() {
            $("#polls").hide();
            $("#newQuestion").hide();
            $("#questions").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#pollList").click(function() {
            $("#questions").hide();
            $("#newQuestion").hide();
            $("#polls").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#createQuestion").click(function() {
            $("#polls").hide();
            $("#questions").hide();
            $("#newQuestion").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });;

        $('#typeSelect').on('change', function() {
            if ($("#typeSelect option:selected").attr("id") == 2) {
                $('#radioGroup').hide();
                $("#taQuestion").show();
            } else if ($("#typeSelect option:selected").attr("id") == 1) {
                $("#taQuestion").hide();
                $('#radioGroup').show();
            } else if ($("#typeSelect option:selected").attr("id") == 0) {
                $('#radioGroup').hide();
                $("#taQuestion").hide();
            }

            if (document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0) {
                $("#saveQuestion").show();
            } else {
                $("#saveQuestion").hide();
            }
        });

        $('#questionTitle').on('input', function(e) {
            if (/^\s/.test($('#questionTitle').val())) {
                $('#questionTitle').val('');
            }
            if (document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0) {
                $("#saveQuestion").show();
            } else {
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
                    " . $value . "
                </script>";
    }
    $_SESSION['errors'] = [];
}
?>

</html>