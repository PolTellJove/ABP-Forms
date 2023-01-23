<?php
session_start();
include 'utilities.php';
if (!isset($_SESSION["ID"])) {

    if (isset($_SESSION['errors']) || (!empty($_SESSION["errors"]))) {
        writeInLog("E", "Sessió no iniciada per entrar al teacher");
        array_push($_SESSION['errors'], "displayMessage('Has d\'iniciar sessió per entrar al teacher',$('.messageBox'),3);");
    } else {
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
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<script type="text/javascript">

</script>
<div id='divTeacher'>

    <?php if ($user['role'] == 1) { ?>
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

        function getUsers(){  
            $startSession = connToDB()->prepare("SELECT `ID`, `username`, `email`, `roleID` FROM `user`;");
            $startSession->execute();
            $_SESSION['allAdmins'] = [];
            $_SESSION['allTeachers'] = [];
            $_SESSION['allStudents'] = [];
            foreach ($startSession as $user){
                if($user['roleID'] == 1){array_push($_SESSION['allAdmins'], $user);}
                elseif($user['roleID'] == 2){array_push($_SESSION['allTeachers'], $user);}
                elseif($user['roleID'] == 3){array_push($_SESSION['allStudents'], $user);}
            }
        }

        function newQuestion(){
            
        }
        getQuestions();
        getPolls();
        getUsers();
        function getTypes()
        {
            $typesQuestion = getTable('type_of_question');
            $_SESSION['arrayTypes'] = $typesQuestion;
        }

        function getOptions()
        {
            $startSession = connToDB()->prepare("SELECT * FROM `option` WHERE ID <= 5;");
            $startSession->execute();
            $_SESSION['arrayOptions'] = [];
            foreach ($startSession as $opinion) {
                array_push($_SESSION['arrayOptions'], $opinion);
            }
        }

        getPolls();
        getTypes();
        getOptions();
        getQuestions();

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

        function createInputOnlyRead(id, text, className, parentID, group = ''){
            var newInput = $('<input>');
            newInput.attr("type", "text");
            newInput.attr("id", id);
            newInput.val(text);
            newInput.addClass(className);
            newInput.attr('name', group);
            newInput.attr('readonly', true);
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

        function createP(id, text, className, parentID, group = ''){
            var p = $("<p/>").text(text);
            p.attr('id', id);
            p.addClass(className);
            p.attr('name', group);
            $("#"+parentID+"").append(p);
        }

        function createButtons(text, id, className, parentID){
            var a = $("<a/>").text(text);
            a.attr('id', id);
            a.addClass(className); 
            $("#"+parentID+"").append(a); 
        }

        function deleteDiv(id){
            $("#"+id+"").remove();
        }

        function clickSavePoll(){
            $( "#saveButton" ).click(function() {
                elememts = $("form#newPollForm :input")
                console.log(elememts);
                elememts.each(function(){
                    $(this).val($(this).attr('id'))
                });
                $("#newPollForm").submit();
            });


        }

        function savePoll(){
            createDiv('divSave', 'newPoll');

            createButtons('Guardar', 'saveButton', 'createPoll', 'divSave');
            createButtons('Cancelar', 'cancelButton', 'createPoll', 'divSave');
            clickSavePoll();
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

        function clickManagementButtons(idButton, currentStatus, nextStatus, divID, generalClassButton, statusName){
            $("#"+idButton).on("click", function(){
                element = $("#"+$("#"+idButton).data("elementID")+"."+currentStatus).clone();
                element.removeClass(currentStatus).addClass(nextStatus).css('background-color', '');
                element.attr('name', statusName)
                element.appendTo("#"+divID);
                
                $("#"+$("#"+idButton).data("elementID")+"."+currentStatus).remove();
                $("."+generalClassButton).removeData("elementID").css('background-color', '');
            });
        }

        //Select teacher for add or delete of a poll
        function clickTeachers(){
            $('.userTeacher').on("click", function(){
                $('.teacherButton').removeData("elementID").css('background-color', '#62929e');
                $('.userTeacher').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('available')){
                    $("#addTeacher").css('background-color', '#46e89f').data( "elementID", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteTeacher").css('background-color', '#f38585').data( "elementID", $(this).attr('id'));
                }
            });
        }


        //Add or delete teachers of poll
        function clickManagementButtonsTeacher(){
            clickManagementButtons('addTeacher', 'available', 'selected', 'selectedTeachers', 'teacherButton', 'teachers[]');
            clickManagementButtons('deleteTeacher', 'selected', 'available', 'availableTeachers', 'teacherButton', '');
            $('.teacherButton').on("click", function(){
                if($('.userTeacher.selected').length == 1 && $('#divQuestions').length == 0){questionsForPoll();};
                if(!$('.userTeacher.selected').length){$('#divQuestions').remove();$('#divSave').remove();$('#divStudents').remove()};  
                clickTeachers();
            });
        }

        //Select question for add or delete of a poll
        function clickQuestions(){
            $('.question').on("click", function(){
                $('.questionButton').removeData("elementID").css('background-color', '#62929e');
                $('.question').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('available')){
                    $("#addQuestion").css('background-color', '#46e89f').data( "elementID", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteQuestion").css('background-color', '#f38585').data( "elementID", $(this).attr('id'));
                }
            });
        }

        //Add or delete question of poll
        function clickManagementButtonsQuestions(){
            clickManagementButtons('addQuestion', 'available', 'selected', 'selectedQuestions', 'questionButton', 'questions[]');
            clickManagementButtons('deleteQuestion', 'selected', 'available', 'availableQuestions', 'questionButton', '');
            $('.questionButton').on("click", function(){
                if($('.question.selected').length == 1 && $('#divStudents').length == 0){savePoll();studentsForPoll();} 
                if(!$('.question.selected').length){$('#divSave').remove();$('#divStudents').remove()}
                clickQuestions();
            });
        }

        //Select student for add or delete of a poll
        function clickStudents(){
            $('.userStudent').on("click", function(){
                $('.studentButton').removeData("elementID").css('background-color', '#62929e');
                $('.userStudent').css('background-color', 'white');
                $(this).css('background-color', 'red');
                
                if($(this).hasClass('available')){
                    $("#addStudent").css('background-color', '#46e89f').data( "elementID", $(this).attr('id'));
                }

                if($(this).hasClass('selected')){
                    $("#deleteStudent").css('background-color', '#f38585').data( "elementID", $(this).attr('id'));
                }
            });
        }

        //Add or delete students of poll
        function clickManagementButtonsStudent(){
            clickManagementButtons('addStudent', 'available', 'selected', 'selectedStudents', 'studentButton', 'students[]');
            clickManagementButtons('deleteStudent', 'selected', 'available', 'availableStudents', 'studentButton', '');
            $('.studentButton').on("click", function(){
                if($('.userStudent.selected').length == 1 && $('#divSave').length == 0){} 
                if(!$('.userStudent.selected').length){}
                clickStudents();
            });
        }

        //View TEACHERS for new poll
        function teachersForPoll(){
            createDiv('divTeachers', 'newPoll')
            createDiv('availableTeachers', 'divTeachers')
            createDiv('managementButtonsTeacher', 'divTeachers')
            createDiv('selectedTeachers', 'divTeachers')
            var teachers = <?php echo json_encode($_SESSION['allTeachers']); ?>;         
            teachers.forEach(teacher => createInputOnlyRead(teacher['ID'], teacher['username'], 'userTeacher available', 'availableTeachers', ''));
            createManagementButtons('addTeacher', 'deleteTeacher', 'teacherButton', 'managementButtonsTeacher');
            clickManagementButtonsTeacher();
            clickTeachers();
        }

        //View STUDENTS for new poll
        function studentsForPoll(){
            $('<div/>').attr('id','divStudents').insertBefore($("#divSave"));
            createDiv('availableStudents', 'divStudents')
            createDiv('managementButtonsStudent', 'divStudents')
            createDiv('selectedStudents', 'divStudents')
            var students = <?php echo json_encode($_SESSION['allStudents']); ?>;
            students.forEach(students => createInputOnlyRead(students['ID'], students['username'], 'userStudent available', 'availableStudents', ''));
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
            questions.forEach(question => createInputOnlyRead(question['ID'], question['question'], 'question available', 'availableQuestions', ''));
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
            $form = $("<form/>");
            $form.attr('id', 'newPollForm')
            $form.attr('action', 'checkoutForms.php');
            $form.attr("method", "POST");
            $form.appendTo('#divDinamic')
            createDiv('newPoll', 'newPollForm')
            teachersForPoll();
        }

        function deleteDiv(id) {
        $("#" + id + "").remove();
    }

    function createDiv(id, parentID) {
        var div = $('<div/>');
        div.attr('id', id);
        $("#" + parentID + "").append(div);
    }

    function createForm(id, parentID, action, method) {
        var form = $('<form/>');
        form.attr('id', id);
        form.attr('action', action);
        form.attr('method', method)
        $("#" + parentID + "").append(form);
    }

    function createSelectForAddQuestion(arrayOptions, parentID) {
        var select = $("<select>").attr('name', 'typeQuestion').attr('id', 'typeSelect');

        var options = arrayOptions;

        select.append($("<option id='0' selected disabled>Tipus de pregunta</option><br>"));

        $.each(options, function (index, value) {
            select.append($("<option>").attr('id', options[index]['ID']).attr('value', options[index]['ID']).text(options[index]['name']))
        })

        $("#" + parentID).append(select);

    }

    function createInput(type, name, parentID, id, value, placeholder, elementInsertBefore, className = "") {
        var input = $("<input>");
        input.attr("type", type);
        input.attr("name", name);
        input.attr("id", id);
        input.addClass(className);
        if (value) {
            input.val(value);
        }
        if (placeholder) {
            input.attr("placeholder", placeholder);
        }
        if (elementInsertBefore) {
            input.insertBefore("#addOption")
        }
        else {
            $("#" + parentID).append(input);
        }

    }

    function createRadioButtons(arrayOptions, insertBeforeThat) {
        var radioGroup = $("<div>").attr("id", "radioGroup");

        $.each(arrayOptions, function (index, value) {
            radioGroup.append($('<a id="radioButton"><input type="radio" id="' + arrayOptions[index]['ID'] + '" name="score" value="' + arrayOptions[index]['ID'] + '" disabled><label for="' + arrayOptions[index]['ID'] + '">' + arrayOptions[index]['answer'] + '</label></a><br>'));
        })

        radioGroup.insertBefore("#" + insertBeforeThat);
    }

    function createTextArea(id, insertBeforeThat, parentID) {
        var textArea = $("<textarea>");
        textArea.attr("id", id);
        textArea.attr("disabled", "disabled");
        space = $("<br>");
        textArea.insertBefore("#" + insertBeforeThat);
        space.insertBefore("#" + insertBeforeThat);
    }

    function newQuestion(divID) {
        createDiv("newQuestion", "divDinamic");
        createForm("formNewQuestion", "newQuestion", "checkoutForms.php", "POST");
        var types = <?php echo json_encode($_SESSION['arrayTypes']); ?>;
        createSelectForAddQuestion(types, "formNewQuestion");
        $("#formNewQuestion").append("<br>");
        createInput("text", "questionTitle", "formNewQuestion", "questionTitle", null, "Títol de la pregunta", null, null);
        checkSelect();
        $("#formNewQuestion").append("<br>");
        $("#formNewQuestion").append("<br>");
        createInput("reset", null, "formNewQuestion", "clearForm", "Cancel·lar", null, null, null);
        $('#questionTitle').on('input', function (e) {
            if (/^\s/.test($('#questionTitle').val())) {
                $('#questionTitle').val('');
            }
            if ($("#questionTitle").val().length && $("#typeSelect option:selected").attr("id") != 0) {
                if (!$("#saveQuestion").length) {
                    createInput("submit", "buttonSaveQuestion", "formNewQuestion", "saveQuestion", null, null, null);
                    $("#saveQuestion").insertBefore("#clearForm");
                }
            } else {
                $("#saveQuestion").remove();
            }

        });
    }

    function checkSelect() {
        $('#typeSelect').on('change', function () {
            if ($("#typeSelect option:selected").attr("id") == 2) {
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
                createTextArea("taQuestion", "clearForm", "formNewQuestion");
            } else if ($("#typeSelect option:selected").attr("id") == 1) {
                deleteDiv("taQuestion");
                deleteDiv("simpleOption");
                var options = <?php echo json_encode($_SESSION['arrayOptions']); ?>;
                createRadioButtons(options, "clearForm");
            } else if ($("#typeSelect option:selected").attr("id") == 0) {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
            }
            else if ($("#typeSelect option:selected").attr("id") == 3) {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                $("<div>").attr("id", "simpleOption").insertBefore("#clearForm");
                createInput("text", "options[]", "simpleOption", "optionTitle1", null, "Escrigui la opció", null, "inputsForAddOption");
                createInput("text", "options[]", "simpleOption", "optionTitle2", null, "Escrigui la opció", null, "inputsForAddOption");
                $("#simpleOption").append("<br>");
                $("#simpleOption").append("<i id='addOption' class='fa fa-plus' aria-hidden='true'></i>");
                addEventForAddInputs();
                deleteInputs();
            }
        });
    }

    function deleteInputs() {
        $(".deleteOption").click(function () {
            var numOption = $(this).attr('id');
            toDelete = "optionTitle" + numOption;
            $("#" + toDelete).remove();
            $(this).remove();
        });
    }

    function addEventForAddInputs() {

        $("#addOption").click(function () {
            var numOption = $(".inputsForAddOption").length + 1;
            createInput("text", "options[]", "simpleOption", "optionTitle" + numOption, null, "Escrigui la opció", true, "inputsForAddOption");
            $("#simpleOption").append("<i id='" + numOption + "' class='fa fa-minus deleteOption' aria-hidden='true'></i>");
            deleteInputs();
        })
    }

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


        //START PAGE
        // showPolls();
        newPoll();
        $("#createQuestion").click(function () {
            if (!$("#newQuestion").length) {
                $("#divDinamic").empty();
                newQuestion('newQuestion');
            }
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $('#clearForm').on('click', function (e) {
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