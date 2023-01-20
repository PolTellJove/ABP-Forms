<?php
session_start();
include 'utilities.php';
if (!isset($_SESSION["ID"])) {

    if (isset($_SESSION['errors']) || (!empty($_SESSION["errors"]))) {
        writeInLog("E", "Sessió no iniciada per entrar al teacher");
        array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al teacher',$('.messageBox'),3);");
    }
    else{
        writeInLog("E", "Sessió no iniciada per entrar al teacher");
        $_SESSION['errors'] = [];
        array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al teacher',$('.messageBox'),3);");
    }
    header("Location: login.php");
}

$user = logUser();
$_GET['titlePage'] = 'Teacher';
$_GET['bodyID'] = 'teacher';
$_GET['bodyClass'] = '';
?><!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<script type="text/javascript">

</script>
<div id='divTeacher'>

    <div class="containerGoBackAnchor">
        <a class="anchorGoBack" href='./dashboard.php'>
        <i class="fa-solid fa-arrow-left-long"></i>
        <div class="textGoBack">Dashboard</div>
        </a>
    </div>

    <?php if ($user['role'] == 1) {   ?>
        <br>
        <div class="messageBox"></div>
        <div id="divButtons">
            <a class="button" id='createQuestion'><i class="fa-regular fa-circle-question"></i> CREAR PREGUNTA</a>
            <a class="button active" id='createPoll'><i class="fa-solid fa-square-poll-vertical"></i> CREAR ENQUESTA</a>
            <a class="button" id='questionList'><i class="fa-solid fa-list"></i> LLISTAT PREGUNTES</a>
            <a class="button" id='pollList'><i class="fa-solid fa-list"></i> LLISTAT ENQUESTES</a>
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
            echo "\n" . '<option id="0" selected disabled>Tipus de pregunta</option><br>';
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

        function getTeachers(){  
            $startSession = connToDB()->prepare("SELECT `ID`, `username`, `email` FROM `user` WHERE user.roleID = 2;");
            $startSession->execute();
            $_SESSION['allTeachers'] = [];
            foreach ($startSession as $teacher) {
                array_push($_SESSION['allTeachers'], $teacher);
            }
        }

        function newQuestion(){
            echo '<form action="checkoutForms.php" method="POST" id="newQuestion" hidden>';
            getTypes();
            echo "<input type='text' name='questionTitle' id='questionTitle'><br>";
            echo '<textarea id="taQuestion" readonly></textarea><br>';
            getOptions();
            echo '<input id="saveQuestion" type="submit" value="Guardar"/>';
            echo '<input id="clearForm" type="reset" value="Cancel·lar"/>';
            echo "\n" . '</form>';
        }
        getPolls();
        getQuestions();
        getTeachers();
        newQuestion();
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
<script>
        function createInputText(id, parentID){
            var newInput = $('<input>');
            newInput.attr("type", "text");
            newInput.attr("id", id);
            newInput.attr("placeholder", "Titol de l'enquesta");
            $("#"+parentID+"").append(newInput);
        }

        function createInputDate(id, parentID){
            var newInput = $('<input>');
            newInput.attr("type", "date");
            newInput.attr("id", id);
            $("#"+parentID+"").append(newInput);
        }

        function createDiv(id, parentID){
            var div = $('<div/>');
            div.attr('id', id);
            $("#"+parentID+"").append(div);
        }

        function createP(id, text, className, parentID){
            var p = $("<p></p>").text(text);
            p.attr('id', id);
            p.addClass(className);
            $("#"+parentID+"").append(p);
        }

        function createButtons(text, id, className, parentID){
            var a = $("<a></a>").title(text);
            p.attr('id', id);
            p.addClass(className); 
            $("#"+parentID+"").append(p); 
        }

        function deleteDiv(id){
            $("#"+id+"").remove();
        }

        function newPoll(divID){
            createDiv('newPoll', 'divDinamic')
            createDiv('pollInfo', 'newPoll')
            createInputText('pollTitle', 'pollInfo')
            createInputDate('startDate', 'pollInfo')
            createInputDate('finishDate', 'pollInfo')
            createDiv('divTeachers', 'newPoll')
            createDiv('availableTeachers', 'divTeachers')
            createDiv('selectedTeachers', 'divTeachers')
            var teachers = <?php echo json_encode($_SESSION['allTeachers']); ?>;
            teachers.forEach(teacher => createP(teacher['ID'], teacher['username'], 'userTeacher', 'availableTeachers'));
            $('.userTeacher').on("click", function(){
                $('.userTeacher').css('background-color', 'white');
                $(this).css('background-color', 'red');
            });
            createButtons('AFEGEIX', 'add', 'teacherButton', 'divTeachers');
            createButtons('ELIMINA', 'delete', 'teacherButton', 'divTeachers');            
        }

    function newQuestion(){
        div = document.createElement("div");
        div.setAttribute("id", "newQuestion");

        input = document.createElement("input");
        input.setAttribute("type", "text");

        selectType = document.createElement("select");

        <?php $startSession = connToDB()->prepare("SELECT * FROM `type_of_question`;");
            $startSession->execute();
            foreach($startSession as $type){ ?>
                type_option = document.createElement("option");
                type_option.setAttribute("value", <?php echo $type["ID"];?>);
                type_option.setAttribute("text", 'Example text');
                selectType.appendChild(type_option);
        <?php }?>

        div.append(input);
        div.append(selectType);
        $("#divDinamic").append(div);
    }
    $('#radioGroup').hide();
    $("#taQuestion").hide();
    $("#saveQuestion").hide();

    function changeColor(button_id) {
        $('.button').css("background-color", "#62929E");
        $(button_id).css("background-color", "blue");
    }

    $(document).ready(function() {
        $("#questionList").click(function() {
            $("#polls").hide();
            $("#newQuestion").hide();
            deleteDiv('newPoll');
            $("#questions").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#createPoll").click(function() {
            $("#polls").hide();
            $("#newQuestion").hide();
            $("#questions").hide();
            if(!$('#newPoll').length){
                newPoll('newPoll');
            }
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#pollList").click(function() {
            $("#questions").hide();
            $("#newQuestion").hide();
            deleteDiv('newPoll');
            $("#polls").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#createQuestion").click(function() {
            $("#polls").hide();
            $("#questions").hide();
            deleteDiv('newPoll');
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
            } else if ($("#typeSelect option:selected").attr("id") == 0){
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

        $('#clearForm').on('click', function(e) {
            $('#radioGroup').hide();
            $("#taQuestion").hide();
        }); 
    });
</script>
<?php
if (isset($_SESSION["ID"])) {
    showErrors();
}
?>
</html>