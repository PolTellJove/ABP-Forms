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

function getOptionsOfActiveQuestions(){
    $startSession = connToDB()->prepare("SELECT * FROM abp_poll.question_option qo INNER JOIN abp_poll.question q on qo.questionID = q.ID INNER JOIN abp_poll.option o on qo.optionID=o.ID WHERE q.active = 0;");
    $startSession->execute();
    $_SESSION['optionsQuestion'] = [];
    foreach ($startSession as $options) {
        array_push($_SESSION['optionsQuestion'], $options);
    }
}

getOptionsOfActiveQuestions();
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
        <!-- <div class="messageBox"></div> -->
        <div id="divButtons">
            <a class="button" id='createQuestion'><i class="fa-regular fa-circle-question"></i> CREAR PREGUNTA</a>
            <a class="button" id='createPoll'><i class="fa-solid fa-square-poll-vertical"></i> CREAR ENQUESTA</a>
            <a class="button" id='questionList'><i class="fa-solid fa-list"></i> LLISTAT PREGUNTES</a>
            <a class="button active" id='pollList'><i class="fa-solid fa-list"></i> LLISTAT ENQUESTES</a>
        </div>
    <?php } ?>
    <div class="messageBox"></div>
    <div id="divDinamic">
        <?php

        
        function getPolls(){
                $startSession = connToDB()->prepare("SELECT * FROM `poll` where active = 0;");
                $startSession->execute();
                $_SESSION['allPolls'] = [];
                foreach ($startSession as $poll) {
                    array_push($_SESSION['allPolls'], $poll);
                }
        }
        function getPoll($ID){
            $startSession = connToDB()->prepare("SELECT * FROM `poll` WHERE poll.ID = :pollID;");
            $startSession->bindParam(':pollID', $ID);
            $startSession->execute();
            $_SESSION['edtiPoll'] = [];
            foreach ($startSession as $poll) {
                array_push($_SESSION['allPolls'], $poll);
            }
        }

        function getTeachersOfActivePolls(){
            $startSession = connToDB()->prepare("SELECT teacherID, pollID FROM `teacher_poll` tp RIGHT JOIN poll p on tp.pollID = p.ID  WHERE p.active = 0;");
            $startSession->execute();
            $_SESSION['teachersPolls'] = [];
            foreach ($startSession as $teachersOfPoll) {
                array_push($_SESSION['teachersPolls'], $teachersOfPoll);
            }
        }

        function getStudentsOfActivePolls(){
            $startSession = connToDB()->prepare("SELECT studentID, pollID FROM `student_poll` sp RIGHT JOIN poll p on sp.pollID = p.ID  WHERE p.active = 0;");
            $startSession->execute();
            $_SESSION['studentsPolls'] = [];
            foreach ($startSession as $studentsOfPoll) {
                array_push($_SESSION['studentsPolls'], $studentsOfPoll);
            }
        }

        function getQuestions(){
            $startSession = connToDB()->prepare("SELECT * FROM `question` where active = 0;");
            $startSession->execute();
            $_SESSION['allQuestions'] = [];
            foreach ($startSession as $question) {
                array_push($_SESSION['allQuestions'], $question);
            }
        }

        function getQuestionsOfActivePolls(){
            $startSession = connToDB()->prepare("SELECT questionID, pollID FROM `poll_question` pq RIGHT JOIN poll p on pq.pollID = p.ID  WHERE p.active = 0;");
            $startSession->execute();
            $_SESSION['questionsPolls'] = [];
            foreach ($startSession as $questionsOfPoll) {
                array_push($_SESSION['questionsPolls'], $questionsOfPoll);
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

        //For edit polls
        getTeachersOfActivePolls();
        getQuestionsOfActivePolls();
        getStudentsOfActivePolls();

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
            $("#"+parentID).append(newInput);
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

        function createTextAreaOnlyRead(id, text, className, parentID, group = ''){
            var newInput = $('<textarea>');
            newInput.attr("type", "text");
            newInput.attr("id", id);
            newInput.val(text);
            newInput.addClass(className);
            newInput.attr('name', group);
            newInput.attr('readonly', true);
            $("#"+parentID+"").append(newInput);
            while (newInput.clientHeight < newInput.scrollHeight) {
                $(newInput).height($(newInput).height()+5);
            }
        };

        function createInputDate(id, parentID){
            var newInput = $('<input>');
            newInput.attr("type", "datetime-local");
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
                if($("#pollTitle").val()){
                    selectedElements = $("form#newPollForm :input.selected")
                    selectedElements.each(function(){
                        $(this).val($(this).attr('id'))
                    });
                    $("#newPollForm").submit();
                }
            });


        }

        function savePoll(){
            createDiv('divSave', 'newPoll');
            createButtons('Cancelar', 'cancelButton', 'createPoll', 'divSave');
            if($("#pollTitle").val()){createButtons('Guardar', 'saveButton', 'createPoll', 'divSave');}
            clickSavePoll();
        }
         
        //MENU QUESTIONS
        function editQuestion(questionID){
            $('.button').removeClass('active');
            $('#createQuestion').addClass('active');
            $("#questions").remove();
            var questions = <?php echo json_encode($_SESSION['allQuestions']); ?>;
            questions.forEach(question => {
                if(question['ID'] == questionID){
                    questionToEdit = question;
                }
            });

            if(questionToEdit['typeID'] == '1'){
                typeQuestion = "numèric";
            }
            else if (questionToEdit['typeID'] == '2'){
                typeQuestion = "text";
            }
            else{
                typeQuestion = "opció simple";
            }
            
            createDiv("newQuestion", "divDinamic");
            var types = [{0: questionToEdit['typeID'], 1: typeQuestion, ID: questionToEdit['typeID'], name: typeQuestion}];
            createSelectForEditQuestion(types, "newQuestion");
            $("#newQuestion").append("<br>");
            createInput("text", "questionEditTitle", "newQuestion", "questionTitle", null, "Títol de la pregunta", null, null);
            $("#questionTitle").val(questionToEdit['question']);
            createDiv('buttonsOfNewQuestion', "newQuestion");
            
            
            if (questionToEdit['typeID'] == '2') {
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
                createTextArea("taQuestion", "buttonsOfNewQuestion", "newQuestion");
                createInput("submit", null ,"buttonsOfNewQuestion", "saveEditQuestion", "Enviar", null, null, null);
                

                $("#saveEditQuestion").on("click", function() {
                    newTitle = $("#questionTitle").val();
                    $("#teacher").append("<form id='formEditQuestion' action='checkoutForms.php' method='POST'><input hidden type='text' name='idQuestionEdit' id='idQuestionEdit'><input hidden type='text' name='titleEditQuestion' id='titleEditQuestion'><input hidden type='submit' name='sendEditQuestion' id='sendEditQuestion'> </form>");
                    $("#idQuestionEdit").val(questionToEdit['ID']);
                    $("#titleEditQuestion").val(newTitle);
                    $("#formEditQuestion").submit();
                });

            } else if (questionToEdit['typeID'] == '1') {
                deleteDiv("taQuestion");
                deleteDiv("simpleOption");
                var options = <?php echo json_encode($_SESSION['arrayOptions']); ?>;
                createInput("submit", null ,"buttonsOfNewQuestion", "saveEditQuestion", "Enviar", null, null, null);
                createRadioButtons(options, "buttonsOfNewQuestion");
                

                $("#saveEditQuestion").on("click", function() {
                    newTitle = $("#questionTitle").val();
                    $("#teacher").append("<form id='formEditQuestion' action='checkoutForms.php' method='POST'><input hidden type='text' name='idQuestionEdit' id='idQuestionEdit'><input hidden type='text' name='titleEditQuestion' id='titleEditQuestion'><input hidden type='submit' name='sendEditQuestion' id='sendEditQuestion'> </form>");
                    $("#idQuestionEdit").val(questionToEdit['ID']);
                    $("#titleEditQuestion").val(newTitle);
                    $("#formEditQuestion").submit();
                });

                $("#questionTitle").on("input", function() {
                    if($('#saveEditQuestion').length == 0){
                        createInput("submit", null ,"buttonsOfNewQuestion", "saveEditQuestion", "Enviar", null, null, null);
                    }
                    if (/^\s/.test($(this).val())) {
                        $(this).val('');
                    }
                    if($(this).val().length == 0){
                        $('#saveEditQuestion').remove();
                    }
                });

            } else if (questionToEdit['typeID'] == '0') {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
            }
            else if (questionToEdit['typeID'] == '3') {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                createInput("submit", null ,"buttonsOfNewQuestion", "saveEditSimpleQuestion", "Enviar", null, null, null);

                function clickSaveEditPoll(){
                    $("#saveEditSimpleQuestion").on("click", function() {
                        $("#teacher").append("<form id='formEditSimpleQuestion' action='checkoutForms.php' method='POST'>");
                        $("#formEditSimpleQuestion").append("<input hidden type='text' name='titleSimpleOption' id='titleSimpleOption'>");
                        $("#titleSimpleOption").val($("#questionTitle").val());
                        for(let j = 0; j < optionsQuestion.length; j++) {
                            $("#formEditSimpleQuestion").append("<input hidden type='text' name='idsOptions[]' id='idOptionQuestion"+j+1+"'>");
                            $("#idOptionQuestion"+j+1).val(optionsQuestion[j][1]);
                        }
                        for(let i = 0; i < optionsQuestion.length; i++) {
                            $("#formEditSimpleQuestion").append("<input hidden type='text' name='optionsQuestion[]' id='optionQuestion"+i+1+"'>");
                            $("#optionQuestion"+i+1).val($("#optionTitle"+i+1).val());
                        }
                        $("#formEditSimpleQuestion").append("<input hidden type='text' name='idSimpleOption' id='idSimpleOption'>");
                        $("#idSimpleOption").val(questionToEdit['ID']);
                        $("#formEditSimpleQuestion").append("<input hidden type='submit' name='submitButton' id='submitButton'>");
                        $("#formEditSimpleQuestion").submit();
                    });
                }

                var optionsQuestion2 = <?php echo json_encode($_SESSION['optionsQuestion']);?>;
                optionsQuestion = [];
                optionsQuestion2.forEach(element => {
                    if(questionToEdit['ID'] == element['questionID']){
                        optionsQuestion.push(element);
                    }
                });
                createDiv('simpleOption', 'newQuestion');
                for(let i = 0; i < optionsQuestion.length; i++) {
                    createInput("text", "optionsEdit[]", "simpleOption", "optionTitle"+i+1, null, "AFEGEIX UNA OPCIÓ", null, "inputsForAddOption");
                    $("#optionTitle"+i+1).val(optionsQuestion[i]['answer']);
                }
                createDiv('moreOptions', 'newQuestion');
                $("#questionTitle").after($("#simpleOption"));
                $("#buttonsOfNewQuestion").before($("#moreOptions"));

                $(".inputsForAddOption").on("input", function() {
                    if($('#saveEditSimpleQuestion').length == 0){
                        createInput("submit", null ,"buttonsOfNewQuestion", "saveEditSimpleQuestion", "Enviar", null, null, null);
                        clickSaveEditPoll();
                    }
                    $(".inputsForAddOption").each(function(){
                        if (/^\s/.test($(this).val())) {
                            $(this).val('');
                        }
                        if($(this).val().length == 0){
                            $('#saveEditSimpleQuestion').remove();
                        }
                    });
                });

                $("#questionTitle").on("input", function() {
                    if($('#saveEditSimpleQuestion').length == 0){
                        createInput("submit", null ,"buttonsOfNewQuestion", "saveEditSimpleQuestion", "Enviar", null, null, null);
                    }
                    if (/^\s/.test($(this).val())) {
                        $(this).val('');
                    }
                    if($(this).val().length == 0){
                        $('#saveEditSimpleQuestion').remove();
                    }
                });
                
                $(".inputsForAddOption").each(function(){
                    if($(this).val().length == 0 && $('#saveEditSimpleQuestion').length == 0){
                        createInput("submit", null ,"buttonsOfNewQuestion", "saveEditSimpleQuestion", "Enviar", null, null, null);
                        clickSaveEditPoll();
                    }
                    if($(this).val().length == 0){
                        $('#saveEditSimpleQuestion').remove();
                    }
                });
                }
            }
        function clickPen(){
            $(".fa-pen").on("click", function() {
                idQuestion = $(this).attr("id");
                editQuestion(idQuestion);
            });
        }

        function showQuestions(){
            createDiv('questions', 'divDinamic')
            var questions = <?php echo json_encode($_SESSION['allQuestions']); ?>;
            questions.forEach(question => {
                createP(question['ID'], question['question'], '', 'questions'); 
                $("#questions").find("p:last").after("<i id='"+question['ID']+"' class='fa-solid fa-pen'></i>");
                $("#questions").find("i:last").after("<i id='"+question['ID']+"' class='fa-solid fa-trash '></i>");
            });
            clickTrash();
            clickPen();
            }

            function clickTrash(){
            $(".fa-trash").on("click", function() {
                idQuestion = $(this).attr("id");
                confirmDelete();
                $("#modalPublish").show();
                deleteModal();
                deleteQuestion(idQuestion);
            });
        }
        
        function confirmDelete(){
            var popup = "<dialog id='modalPublish'><div id='containerDialog'> <div class='titleDialog' id='divTitleDialog'><h2 id='titleDialog'>Estàs segur que vols esborrar?</h2></div><div id='btnsDialog' class='buttonsModal'><button id='btnPublish-no'>Cancel·lar</button><button id='btnPublish-yes'>Acceptar</button></form></div></div></dialog>";
            $('#teacher').append(popup);
        }

        function deleteModal(){
            $("#btnPublish-no").on("click", function() {
                $("#modalPublish").remove();
            })
        }

        function deleteQuestion(id){
            $("#btnPublish-yes").on("click", function() {
               $("#teacher").append("<form id='formToSend' action='checkoutForms.php' method='POST'><input hidden type='number' name='idQuestionToDelete' id='idQuestionToDelete'><input hidden type='submit' name='sendData' id='sendData'></form>");
               $("#idQuestionToDelete").val(id);
               $("#formToSend").submit();
            });
        }

        function clickTrashPoll(){
            $(".fa-trash").on("click", function() {
                idPoll = $(this).attr("id");
                confirmDelete();
                $("#modalPublish").show();
                deleteModal();
                deletePoll(idPoll);
            });
        }

        function deletePoll(id){
            $("#btnPublish-yes").on("click", function() {
               $("#teacher").append("<form id='formToSendPoll' action='checkoutForms.php' method='POST'><input hidden type='number' name='idPollToDelete' id='idPollToDelete'><input hidden type='submit' name='sendDataPoll' id='sendDataPoll'></form>");
               $("#idPollToDelete").val(id);
               $("#formToSendPoll").submit();
            });
        }

        //VIEW POLLS
        function getTeachersPoll($ID){
            var teachers = <?php echo json_encode($_SESSION['teachersPolls']); ?>;
            teachersOfPoll = [];
            teachers.forEach(teacher => {
                if($ID == teacher['pollID']){
                    teachersOfPoll.push(teacher['teacherID']);
                }
            });
            return teachersOfPoll;
        }

        function teachersPoll(poll){
            teachersofPoll = getTeachersPoll(poll['ID']);
            teachersofPoll.forEach(teacher => {
                element = $("#"+teacher).clone();
                $("#"+teacher).remove();
                element.removeClass('available').addClass('selected').css('background-color', '');
                element.attr('name', 'teachers[]')
                element.appendTo("#selectedTeachers");
            });
            clickTeachers();
        }

        function getQuestionPoll($ID){
            var questions = <?php echo json_encode($_SESSION['questionsPolls']); ?>;
            questionsOfPoll = [];
            questions.forEach(question => {
                if($ID == question['pollID']){
                    questionsOfPoll.push(question['questionID']);
                }
            });
            return questionsOfPoll;
        }


        function questionPoll(poll){
            questionsofPoll = getQuestionPoll(poll['ID']);
            questionsofPoll.forEach(question => {
                element = $("#"+question).clone();
                $("#"+question).remove();
                element.removeClass('available').addClass('selected').css('background-color', '');
                element.attr('name', 'questions[]')
                element.appendTo("#selectedQuestions");
            });
            clickQuestions()
        }

        function getStudentPoll($ID){
            var students = <?php echo json_encode($_SESSION['studentsPolls']); ?>;
            studentsOfPoll = [];
            students.forEach(student => {
                if($ID == student['pollID']){
                    studentsOfPoll.push(student['studentID']);
                }
            });
            return studentsOfPoll;
        }


        function studentPoll(poll){
            studentsofPoll = getStudentPoll(poll['ID']);
            studentsofPoll.forEach(student => {
                element = $("#"+student).clone();
                $("#"+student).remove();
                element.removeClass('available').addClass('selected').css('background-color', '');
                element.attr('name', 'students[]')
                element.appendTo("#selectedStudents");
            });
            clickStudents();
        }
        function onClickEditPoll(pollID){
            $("#newPollForm").on("submit", function(){
                createInputText('IDpoll', 'newPollForm');
                $('#IDpoll').attr('name', 'IDpoll');
                $('#IDpoll').val(pollID);
                $('#IDpoll').attr("hidden",true);;
            });
        }

        function editingPoll(poll){
            $('#pollTitle').val(poll['title']);
            $('#startDate').val(poll['startDate']);
            $('#finishDate').val(poll['finishDate']);
            teachersPoll(poll);
            questionsForPoll();
            questionPoll(poll);
            savePoll();
            studentsForPoll();
            studentPoll(poll);
            $('#cancelButton').remove();
            onClickEditPoll(poll['ID']);
        }

        function clickPenPoll(){
            $(".poll.fa-pen").on('click', function(){
                $('.button').removeClass('active');
                $('#createPoll').addClass('active');
                $("#divDinamic").empty();
                newPoll();
                 //Get info of editing poll
                var polls = <?php echo json_encode($_SESSION['allPolls']); ?>;
                polls.forEach(poll => {
                    if(poll['ID'] == $(this).attr('id')){
                        editPoll = poll;
                    }
                });
                editingPoll(editPoll);
            })
        }

        function showPolls(){
            createDiv('polls', 'divDinamic');
            var polls = <?php echo json_encode($_SESSION['allPolls']); ?>;
            polls.forEach(poll => {
                createP(poll['ID'], poll['title'], '', 'polls');
                $("#polls").find("p:last").after("<i id='"+poll['ID']+"' class='fa-solid fa-pen poll'></i>");
                $("#polls").find("i:last").after("<i id='"+poll['ID']+"' class='fa-solid fa-trash'></i>");
            });
            clickPenPoll();
            clickTrashPoll();         
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
                if(!$('.userTeacher.selected').length){$('#divQuestions').remove();$('#divSave').remove();$('#divStudents').remove();};  
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
                if(!$('.question.selected').length){$('#divSave').remove();$('#divStudents').remove();}
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
            questions.forEach(question => createTextAreaOnlyRead(question['ID'], question['question'], 'question available', 'availableQuestions', ''));
            createManagementButtons('addQuestion', 'deleteQuestion', 'questionButton', 'managementButtonsQuestion');
            clickManagementButtonsQuestions();
            clickQuestions();
        }

        //Information for new poll
        function infoOfPoll(){
            createDiv('pollInfo', 'newPoll')
            createInputText('pollTitle', 'pollInfo')
            $('#pollTitle').attr('name', 'pollTitle')
            $('#pollTitle').on('input', function (e) {
                if (/^\s/.test($('#pollTitle').val())) {
                    $('#pollTitle').val('');
                }
                if($('#pollTitle').val().length > 0 && !$('#saveButton').length){
                    createButtons('Guardar', 'saveButton', 'createPoll', 'divSave');
                    clickSavePoll();
                }else if($('#pollTitle').val().length < 1){
                    $('#saveButton').remove();
                }
            });
            createInputDate('startDate', 'pollInfo');
            $('#startDate').attr('name', 'startDate');
            createInputDate('finishDate', 'pollInfo');
            $('#finishDate').attr('name', 'finishDate');
        }


        //Create new poll
        function newPoll(){
            $form = $("<form/>");
            $form.attr('id', 'newPollForm')
            $form.attr('action', 'checkoutForms.php');
            $form.attr("method", "POST");
            $form.appendTo('#divDinamic')
            createDiv('newPoll', 'newPollForm')
            infoOfPoll();
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

    function createSelectForEditQuestion(arrayOptions, parentID){
        var select = $("<select>").attr('name', 'typeQuestion').attr('id', 'typeSelect');

        var options = arrayOptions;

        
        $.each(options, function (index, value) {
            select.append($("<option>").attr('id', options[index]['ID']).attr('value', options[index]['ID']).text(options[index]['name']).attr("disabled", "disabled").attr("selected","selected"))
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

    function saveButtonOfSimpleQuestion(){
        if (!$("#saveQuestion").length) {
            createInput("submit", null ,"buttonsOfNewQuestion", "saveQuestion", "Enviar", null, null, null);
        }
        $('.inputsForAddOption').each(
            function(){
                if($(this).val() == '' && $("#saveQuestion").length){
                    $("#saveQuestion").remove();
                }
            }
        )
    }

    function onInputNewOptions(){
        $('.inputsForAddOption').on('input', function (e) {
            saveButtonOfSimpleQuestion();
            if (/^\s/.test($(this).val())) {
                $(this).val('');
            }
        });
    }
    function newQuestion(divID) {
        createDiv("newQuestion", "divDinamic");
        createForm("formNewQuestion", "newQuestion", "checkoutForms.php", "POST");
        var types = <?php echo json_encode($_SESSION['arrayTypes']); ?>;
        createSelectForAddQuestion(types, "formNewQuestion");
        $("#formNewQuestion").append("<br>");
        createInput("text", "questionTitle", "formNewQuestion", "questionTitle", null, "Títol de la pregunta", null, null);
        checkSelect();
        createDiv('buttonsOfNewQuestion', "formNewQuestion");
        createInput("reset", null, "buttonsOfNewQuestion", "clearForm", "Cancel·lar", null, null, null);
        $('#questionTitle').on('input', function (e) {
            if (/^\s/.test($('#questionTitle').val())) {
                $('#questionTitle').val('');
            }
            if ($("#questionTitle").val().length && $("#typeSelect option:selected").attr("id") != 0) {
                if (!$("#saveQuestion").length) {
                    createInput("submit", null ,"buttonsOfNewQuestion", "saveQuestion", "Enviar", null, null, null);
                    saveButtonOfSimpleQuestion();
                }
            } else {
                $("#saveQuestion").remove();
            }
        });
    }

    function checkSelect() {
        $('#typeSelect').on('change', function () {
            if ($("#questionTitle").val().length && $("#typeSelect option:selected").attr("id") != 0) {
                if (!$("#saveQuestion").length) {
                    createInput("submit", null ,"buttonsOfNewQuestion", "saveQuestion", "Enviar", null, null, null);
                    saveButtonOfSimpleQuestion();
                }
            } else {
                $("#saveQuestion").remove();
            }

            if ($("#typeSelect option:selected").attr("id") == 2) {
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
                createTextArea("taQuestion", "clearForm", "formNewQuestion");
                deleteDiv("moreOptions");
            } else if ($("#typeSelect option:selected").attr("id") == 1) {
                deleteDiv("taQuestion");
                deleteDiv("simpleOption");
                deleteDiv("moreOptions");
                var options = <?php echo json_encode($_SESSION['arrayOptions']); ?>;
                createRadioButtons(options, "clearForm");
            } else if ($("#typeSelect option:selected").attr("id") == 0) {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                deleteDiv("simpleOption");
                deleteDiv("moreOptions");
            }
            else if ($("#typeSelect option:selected").attr("id") == 3) {
                deleteDiv("taQuestion");
                deleteDiv("radioGroup");
                createDiv('simpleOption', 'formNewQuestion');
                createInput("text", "options[]", "simpleOption", "optionTitle1", null, "AFEGEIX UNA OPCIÓ", null, "inputsForAddOption");
                createInput("text", "options[]", "simpleOption", "optionTitle2", null, "AFEGEIX UNA OPCIÓ", null, "inputsForAddOption");
                createDiv('moreOptions', 'formNewQuestion');
                $("#moreOptions").append("<i id='addOption' class='fa fa-plus' aria-hidden='true'></i>");
                $("#questionTitle").after($("#simpleOption"));
                $("#buttonsOfNewQuestion").before($("#moreOptions"));
                onInputNewOptions()
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
            saveButtonOfSimpleQuestion();
        });
    }

    function addEventForAddInputs() {
        $("#addOption").click(function () {
            var numOption = $(".inputsForAddOption").length + 1;
            createInput("text", "options[]", "simpleOption", "optionTitle" + numOption, null, "AFEGEIX UNA OPCIÓ", null, "inputsForAddOption");
            $("#optionTitle"+numOption).addClass("add");
            $("#simpleOption").find("input:last").after("<i id='" + numOption + "' class='fa fa-minus deleteOption' aria-hidden='true'></i>");
            saveButtonOfSimpleQuestion();
            onInputNewOptions()
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

        //START PAGE
        showPolls();
        //newPoll();
    });
</script>
<?php
if (isset($_SESSION["ID"])) {
    showErrors();
}
?>
</html>