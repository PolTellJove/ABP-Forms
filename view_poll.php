<?php
session_start();
include 'utilities.php';
$user = logUser();
$_SESSION['questionsOfPoll'] = [];
$_SESSION['poll'] = [];
$_GET['titlePage'] = 'Reply Poll';
$_GET['bodyID'] = 'reply';
$_GET['bodyClass'] = 'reply';
$_SESSION['currentDate'] = strtotime(date("Y-m-d h:m:s"));
?><!DOCTYPE html>
<html lang="en">
<!-- FUNCTIONS -->
<?php 
    function checkToken(){
        $pre = md5("REPLY");
        $userIDEncrypt = md5($_GET['s'].$_GET['p']);
        $post = md5("POLL");
        $token = $pre.$userIDEncrypt.$post;
        if($token == $_GET['k']){
            getPoll($_GET['p']);
            getAnswersOfQuestion();
        }else{
            return false;
        }
    }

    function getQuestionsOfPoll($ID){
        $startSession = connToDB()->prepare("SELECT q.ID as questionID, q.question as question, q.typeID as typeID,
        GROUP_CONCAT(qo.optionID,'::', o.answer SEPARATOR '{#}') as answers
        FROM abp_poll.poll_question pq 
        LEFT JOIN abp_poll.question q on pq.questionID = q.ID 
        LEFT OUTER JOIN question_option qo on q.ID = qo.questionID 
        LEFT OUTER JOIN abp_poll.option o on qo.optionID = o.ID 
        WHERE pq.pollID = :pollID GROUP BY questionID;");
        $startSession->bindParam(':pollID', $ID);
        $startSession->execute();
        $_SESSION['questionsOfPoll'] = [];
        foreach ($startSession as $question) {
            array_push($_SESSION['questionsOfPoll'], $question);
        }
    }

    function getAnswersOfQuestion(){
        $startSession = connToDB()->prepare("SELECT DISTINCT (ua.questionID) as question, ua.answer, ua.optionID as selectedOption FROM abp_poll.user_answer ua where studentID = :studentID AND pollID = :pollID;");
        $startSession->bindParam(':pollID', $_GET['p']);
        $startSession->bindParam(':studentID', $_GET['s']);
        $startSession->execute();
        $_SESSION['answers'] = [];
        foreach ($startSession as $answer) {
            array_push($_SESSION['answers'], $answer);
        }
    }

    function getPoll($ID){
        $startSession = connToDB()->prepare("SELECT p.title as title, p.startDate as startDate, p.finishDate as finish, sp.reply as reply, sp.studentID FROM abp_poll.poll p INNER JOIN student_poll sp on p.ID = sp.pollID WHERE p.ID = :pollID AND sp.studentID = :studentID;");
        $startSession->bindParam(':pollID', $ID);
        $startSession->bindParam(':studentID', $_GET['s']);
        $startSession->execute();
        $_SESSION['poll'] = [];
        foreach ($startSession as $poll) {
            array_push($_SESSION['poll'], $poll);
        }
        getQuestionsOfPoll($ID);
    }
    checkToken();
?>
<script>
    //Create elements
    function createDiv(id, parentID){
        var div = $('<div/>');
        div.attr('id', id);
        $("#"+parentID+"").append(div);
    };

    function createTextArea(id, text, className, parentID, group = ''){
            var newInput = $('<textarea>');
            newInput.attr("type", "text");
            newInput.attr("id", id);
            newInput.val(text);
            newInput.addClass(className);
            newInput.attr('name', group);
            $("#"+parentID+"").append(newInput);
            while (newInput.clientHeight < newInput.scrollHeight) {
                $(newInput).height($(newInput).height()+5);
            }
    };

    function createButtons(text, id, className, parentID){
            var a = $("<a/>").text(text);
            a.attr('id', id);
            a.addClass(className); 
            $("#"+parentID+"").append(a); 
    };

    function createP(id, text, parentID){
            var p = $("<p/>").text(text);
            p.attr('id', id);
            $("#"+parentID+"").append(p);
    }
    
    //INFORMATION OF POLL - QUESTIONS
    var questions = <?php echo json_encode($_SESSION['questionsOfPoll']);?>;
    var poll = <?php echo json_encode($_SESSION['poll']);?>;
    var replyAnswers = <?php echo json_encode($_SESSION['answers']);?>;
    var startDate = <?php echo json_encode(strtotime($_SESSION['poll'][0]['startDate']));?>;
    var finishDate = <?php echo json_encode(strtotime($_SESSION['poll'][0]['finish']));?>;
    var currentDate = <?php echo json_encode($_SESSION['currentDate']);?>;
</script>
<?php include 'header.php'; ?>
<div id='divReply'>
<h1 id ='titlePoll'></h1>
    <form id='formReplyPoll' action='checkoutForms.php' method='POST'>
        <div id='divQuestionsToReply'>
        <script>
            $('#titlePoll').text(poll[0]['title']);

            function optionIsSelected(grbName){
                $("input[name='"+grbName+"']").on('click', function(){
                    if($($(this).parent()).parent().hasClass('last')) {
                        $($(this).parent()).parent().removeClass('last');
                        nextQuestion();
                    }
                });
            }

            function questionHasAnswer(question){
                $("#ta"+question).on('input', function (e) {
                    if (/^\s/.test($(this).val())) {
                        $(this).val('');
                    }
                    if($(this).val().length > 0 & $(this).parent().hasClass('last')){
                        $(this).parent().removeClass('last');
                        nextQuestion();
                    }
                });
            }
            function onClickSave(){
                $('#saveButton').on('click', function(){
                    $('#formReplyPoll').append('<input type="hidden" name="replyPoll" value="" />');
                    $('#formReplyPoll').append('<input type="hidden" name="teacher" value="<?php echo $_GET['t']; ?>" />');
                    $('#formReplyPoll').append('<input type="hidden" name="student" value="<?php echo $_GET['s']; ?>" />');
                    $('#formReplyPoll').append('<input type="hidden" name="poll" value="<?php echo $_GET['p']; ?>" />');
                    $('#formReplyPoll').submit();
                });
            }

            function saveAnswersOfPoll(){
                createDiv('divSave', 'divQuestionsToReply');
                createButtons('Guardar', 'saveButton', 'button', 'divSave');
                onClickSave();
            }

            function nextQuestion(){
                if(questions.length){
                    currentQuestion = questions.shift();
                    createQuestion(currentQuestion);
                }else{
                    saveAnswersOfPoll();
                }
            }

            function viewNextQuestion(){
                while(questions.length){
                    currentQuestion = questions.shift();
                    createViewQuestion(currentQuestion);
                }
            }

            function createViewQuestion(question){
                createDiv('div'+question['questionID'], 'divQuestionsToReply');
                $('#div'+question['questionID']).addClass('question');
                $('#div'+question['questionID']).append('<h3 id="title'+question['questionID']+'" >'+question['question']+'</h3>');
                
                if(question['typeID'] != 2){
                    answers = question['answers'].split("{#}");
                    answers.forEach(answer => {
                        answerData = answer.split("::");
                        $('#div'+question['questionID']).append('<label for="rb'+answerData[0]+'"><input type="radio" id="rb'+answerData[0]+'" name="question-n'+question['typeID']+'-'+question['questionID']+'" value="'+answerData[0]+'">'+answerData[1]+'</label>');
                        $('#rb'+answerData[0]).attr("disabled",true);
                    });
                    replyAnswers.forEach($answer => {
                        if($answer['question'] == question['questionID']){
                            $('#rb'+$answer['selectedOption']).prop("checked", true);
                        }
                    });
                }else if(question['typeID'] == 2){
                    createTextArea('ta'+question['questionID'], '', 'question', 'div'+question['questionID'], 'question-n'+question['typeID']+'-'+question['questionID']);
                    replyAnswers.forEach($answer => {
                        if($answer['question'] == question['questionID']){
                            $('#ta'+question['questionID']).val($answer['answer']);
                            $('#ta'+question['questionID']).attr('readonly', true);
                        }
                    });
                    
                }
            }

            function createQuestion(question){
                createDiv('div'+question['questionID'], 'divQuestionsToReply');
                $('#div'+question['questionID']).addClass('question last');
                $('#div'+question['questionID']).append('<h3 id="title'+question['questionID']+'" >'+question['question']+'</h3>');
                
                if(question['typeID'] != 2){
                    answers = question['answers'].split("{#}");
                    answers.forEach(answer => {
                        answerData = answer.split("::");
                        $('#div'+question['questionID']).append('<label for="rb'+answerData[0]+'"><input type="radio" id="rb'+answerData[0]+'" name="question-n'+question['typeID']+'-'+question['questionID']+'" value="'+answerData[0]+'">'+answerData[1]+'</label>');
                        optionIsSelected('question-n'+question['typeID']+'-'+question['questionID']);
                    });
                }else if(question['typeID'] == 2){
                    createTextArea('ta'+question['questionID'], '', 'question', 'div'+question['questionID'], 'question-n'+question['typeID']+'-'+question['questionID']);
                    $('#ta'+question['questionID']).attr("placeholder", "Escriu la teva resposta aqui");
                    questionHasAnswer(question['questionID']);
                }
            }
            // if(startDate > currentDate){
            //     createP('notStarted', "Enquesta encara no disponible.", 'divQuestionsToReply');
            // }else if(poll[0]['reply'] == 0 && finishDate < currentDate){
            //     createP('finished', "Enquesta  no disponible", 'divQuestionsToReply');
            // }else if(poll[0]['reply'] == 0){
            //     nextQuestion();
            // }else if(poll[0]['reply'] == 1){
            //     viewNextQuestion();
            // }
            
            if(poll[0]['reply'] == 0){
                nextQuestion();
            }else if(poll[0]['reply'] == 1){
               viewNextQuestion();
            }
        </script>
        </div>
    </forms>
</div>
<?php include 'footer.php'; ?>
</body>