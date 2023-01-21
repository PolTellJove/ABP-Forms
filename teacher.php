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
            <a class="button" id='createPoll'><i class="fa-solid fa-square-poll-vertical"></i> CREAR ENQUESTA</a>
            <a class="button" id='questionList'><i class="fa-solid fa-list"></i> LLISTAT PREGUNTES</a>
            <a class="button active" id='pollList'><i class="fa-solid fa-list"></i> LLISTAT ENQUESTES</a>
        </div>
    <?php } ?>
    <div id="divDinamic">
        <?php

        
        function getPolls(){
            $startSession = connToDB()->prepare("SELECT * FROM `poll`;");
            $startSession->execute();
            $_SESSION['allPolls'] = [];
            foreach ($startSession as $poll) {
                array_push($_SESSION['allPolls'], $poll);
            }
        }

        function getQuestions(){
            $startSession = connToDB()->prepare("SELECT * FROM `question`;");
            $startSession->execute();
            $_SESSION['allQuestions'] = [];
            foreach ($startSession as $question) {
                array_push($_SESSION['allQuestions'], $question);
            }
        }

        function getTypes(){

        }

        function getOptions(){

        }

        function getTeachers(){  
            $startSession = connToDB()->prepare("SELECT `ID`, `username`, `email` FROM `user` WHERE user.roleID = 2;");
            $startSession->execute();
            $_SESSION['allTeachers'] = [];
            foreach ($startSession as $teacher) {
                array_push($_SESSION['allTeachers'], $teacher);
            }
        }

        function getAdmins(){  
            $startSession = connToDB()->prepare("SELECT `ID`, `username`, `email` FROM `user` WHERE user.roleID = 1;");
            $startSession->execute();
            $_SESSION['allAdmins'] = [];
            foreach ($startSession as $admin) {
                array_push($_SESSION['allAdmins'], $admin);
            }
        }

        function newQuestion(){
            
        }
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
            var a = $("<a></a>").text(text);
            a.attr('id', id);
            a.addClass(className); 
            $("#"+parentID+"").append(a); 
        }

        function deleteDiv(id){
            $("#"+id+"").remove();
        }
         
        //VIEW QUESTIONS
        function showQuestions(){
            createDiv('questions', 'divDinamic')
            var questions = <?php echo json_encode($_SESSION['allQuestions']); ?>;
            questions.forEach(question => createP(question['ID'], question['question'], '', 'questions'));

        }

        //VIEW POLLS
        function showPolls(){
            createDiv('polls', 'divDinamic')
            var polls = <?php echo json_encode($_SESSION['allPolls']); ?>;
            polls.forEach(poll => createP(poll['ID'], poll['title'], '', 'polls'));
        }
        
        //NEW POLL
        function createManagementButtons(addID, deleteID, className, parentID, addText, deleteText){
            if (typeof(addText)==='undefined') addText = '';
            if (typeof(deleteText)==='undefined') deleteText = '';
            createButtons(addText, addID, className, parentID);
            createButtons(deleteText, deleteID, className, parentID);
            $('#'+addID+'').append('<i class="fa-sharp fa-solid fa-square-caret-right"></i>')
            $('#'+deleteID+'').append('<i class="fa-sharp fa-solid fa-square-caret-left"></i>')
        }

        //Select teacher for add or delete of a poll
        function clickTeachers(){
            $('.userTeacher').on("click", function(){
                $('.teacherButton').removeData("IDteacher").css('background-color', '#62929e');
                $('.userTeacher').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('avalible')){
                    $("#addTeacher").css('background-color', '#46e89f').data( "IDteacher", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteTeacher").css('background-color', '#f38585').data( "IDteacher", $(this).attr('id'));
                }
            });
        }

        //Add or delete teachers of poll
        function clickManagementButtonsTeacher(){
            var teachers = <?php echo json_encode($_SESSION['allTeachers']); ?>;

            $('#addTeacher').on("click", function(){
                teachers.forEach(function(teacher){
                    if(teacher['ID'] == $('#addTeacher').data("IDteacher")){
                        createP(teacher['ID'], teacher['username'], 'userTeacher selected', 'selectedTeachers');
                        clickTeachers();
                        $("#"+teacher['ID']+".avalible").remove();
                        $('.teacherButton').removeData("IDteacher").css('background-color', '#62929e');
                    }
                });     
                if($('.userTeacher.selected').length == 1){questionsForPoll()}       
            });

            $('#deleteTeacher').on("click", function(){
                teachers.forEach(function(teacher){
                    if(teacher['ID'] == $('#deleteTeacher').data("IDteacher")){
                        createP(teacher['ID'], teacher['username'], 'userTeacher avalible', 'availableTeachers');
                        clickTeachers();
                        $("#"+teacher['ID']+".selected").remove();
                        $('.teacherButton').removeData("IDteacher").css('background-color', '#62929e');
                    }
                    if(!$('.userTeacher.selected').length){$('#divQuestions').remove()}
                });            
            });
        }

        //Select question for add or delete of a poll
        function clickQuestions(){
            $('.question').on("click", function(){
                $('.questionButton').removeData("IDquestion").css('background-color', '#62929e');
                $('.question').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('avalible')){
                    $("#addQuestion").css('background-color', '#46e89f').data( "IDquestion", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteQuestion").css('background-color', '#f38585').data( "IDquestion", $(this).attr('id'));
                }
            });
        }

        //Add or delete question of poll
        function clickManagementButtonsQuestions(){
            var questions = <?php echo json_encode($_SESSION['allQuestions']); ?>;

            $('#addQuestion').on("click", function(){
                questions.forEach(function(question){
                    if(question['ID'] == $('#addQuestion').data("IDquestion")){
                        createP(question['ID'], question['question'], 'question selected', 'selectedQuestions');
                        clickQuestions();
                        $("#"+question['ID']+".avalible").remove();
                        $('.questionButton').removeData("IDquestion").css('background-color', '#62929e');
                    }
                });     
                if($('.question.selected').length == 1){studentsForPoll()}       
            });

            $('#deleteQuestion').on("click", function(){
                questions.forEach(function(question){
                    if(question['ID'] == $('#deleteQuestion').data("IDquestion")){
                        createP(question['ID'], question['question'], 'question avalible', 'availableQuestions');
                        clickQuestions();
                        $("#"+question['ID']+".selected").remove();
                        $('.questionButton').removeData("IDquestion").css('background-color', '#62929e');
                    }
                    if(!$('.question.selected').length){$('#divStudents').remove()}
                });            
            });
        }

        //Select student for add or delete of a poll
        function clickStudents(){
            $('.userStudent').on("click", function(){
                $('.studentButton').removeData("IDstudent").css('background-color', '#62929e');
                $('.userStudent').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('avalible')){
                    $("#addStudent").css('background-color', '#46e89f').data( "IDstudent", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteStudent").css('background-color', '#f38585').data( "IDstudent", $(this).attr('id'));
                }
            });
        }

        //Add or delete students of poll
        function clickManagementButtonsStudent(){
            var students = <?php echo json_encode($_SESSION['allAdmins']); ?>;

            $('#addStudent').on("click", function(){
                students.forEach(function(student){
                    if(student['ID'] == $('#addStudent').data("IDstudent")){
                        createP(student['ID'], student['username'], 'userStudent selected', 'selectedStudents');
                        clickStudents();
                        $("#"+student['ID']+".avalible").remove();
                        $('.studentButton').removeData("IDstudent").css('background-color', '#62929e');
                    }
                });     
                //if($('.selected').length == 1){students();}       
            });

            $('#deleteStudent').on("click", function(){
                students.forEach(function(student){
                    if(student['ID'] == $('#deleteStudent').data("IDstudent")){
                        createP(student['ID'], student['username'], 'userStudent avalible', 'availableStudents');
                        clickStudents();
                        $("#"+student['ID']+".selected").remove();
                        $('.studentButton').removeData("IDstudent").css('background-color', '#62929e');
                    }
                    //if(!$('.selected').length){$('#divStudents').remove()}
                });            
            });
        }

        //View TEACHERS for new poll
        function teachersForPoll(){
            createDiv('divTeachers', 'newPoll')
            createDiv('availableTeachers', 'divTeachers')
            createDiv('managementButtonsTeacher', 'divTeachers')
            createDiv('selectedTeachers', 'divTeachers')
            var teachers = <?php echo json_encode($_SESSION['allTeachers']); ?>;
            teachers.forEach(teacher => createP(teacher['ID'], teacher['username'], 'userTeacher avalible', 'availableTeachers'));
            createManagementButtons('addTeacher', 'deleteTeacher', 'teacherButton', 'managementButtonsTeacher');
            clickManagementButtonsTeacher();
            clickTeachers();
        }

        //View STUDENTS for new poll
        function studentsForPoll(){
            createDiv('divStudents', 'newPoll')
            createDiv('availableStudents', 'divStudents')
            createDiv('managementButtonsStudent', 'divStudents')
            createDiv('selectedStudents', 'divStudents')
            var students = <?php echo json_encode($_SESSION['allAdmins']); ?>;
            students.forEach(students => createP(students['ID'], students['username'], 'userStudent avalible', 'availableStudents'));
            createManagementButtons('addStudent', 'deleteStudent', 'studentButton', 'managementButtonsStudent');
            clickManagementButtonsStudent();
            clickStudents();
        }

        //View QUESTIONS for new poll
        function questionsForPoll(){
            createDiv('divQuestions', 'newPoll')
            createDiv('availableQuestions', 'divQuestions')
            createDiv('managementButtonsQuestion', 'divQuestions')
            createDiv('selectedQuestions', 'divQuestions')
            var questions = <?php echo json_encode($_SESSION['allQuestions']); ?>;
            questions.forEach(question => createP(question['ID'], question['question'], 'question avalible', 'availableQuestions'));
            createManagementButtons('addQuestion', 'deleteQuestion', 'questionButton', 'managementButtonsQuestion');
            clickManagementButtonsQuestions();
            clickQuestions();
        }

        //Information for new poll
        function infoOfPoll(){
            createDiv('pollInfo', 'newPoll')
            createInputText('pollTitle', 'pollInfo')
            createInputDate('startDate', 'pollInfo')
            createInputDate('finishDate', 'pollInfo')
        }

        //Create new poll
        function newPoll(){
            createDiv('newPoll', 'divDinamic')
            infoOfPoll();
            teachersForPoll();     
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

    $(document).ready(function() {
        $("#questionList").click(function() {
            if(!$('#questions').length){
                $("#divDinamic").empty();
                showQuestions();
            }
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#createPoll").click(function() {
            if(!$('#newPoll').length){
                $("#divDinamic").empty();
                newPoll();
            }
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#pollList").click(function() {
            if(!$('#polls').length){
                $("#divDinamic").empty();
                showPolls();
            }
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#createQuestion").click(function() {
            $("#divDinamic").empty();
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

        //START PAGE
        showPolls();
    });
</script>
<?php
if (isset($_SESSION["ID"])) {
    showErrors();
}
?>
</html>