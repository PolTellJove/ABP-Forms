<?php
session_start();
include 'utilities.php';
$user = logUser();
$_GET['titlePage'] = 'Reply Poll';
$_GET['bodyID'] = 'reply';
$_GET['bodyClass'] = 'reply';
?><!DOCTYPE html>
<html lang="en">
<!-- FUNCTIONS -->
<?php 
    function getQuestionsOfPoll($ID){
        $startSession = connToDB()->prepare("SELECT q.ID as questionID, q.question as question, q.typeID as type, GROUP_CONCAT(qo.optionID) as options, 
        GROUP_CONCAT(qo.optionID,'-', o.answer) as answers
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
    getQuestionsOfPoll(5);
?>
<script>
        //Create elements
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

    function createDiv(id, parentID){
        var div = $('<div/>');
        div.attr('id', id);
        $("#"+parentID+"").append(div);
    }
</script>
<?php include 'header.php'; ?>
<div id='divReply'>
<h1>CONTESTAR ENQUESTA</h1>
<div id='divQuestionsToReply'>
<script>
    var questions = <?php echo json_encode($_SESSION['questionsOfPoll']);?>;
    questions.forEach(question => {
        createDiv('div'+question['questionID'], 'divQuestionsToReply');
        $('#div'+question['questionID']).addClass('question');
        $('#div'+question['questionID']).append('<h3 id="'+question['questionID']+'" >'+question['question']+'</h3>');
        
        if(question['type'] != 2){
            answers = question['answers'].split(",");
            answers.forEach(answer => {
                answerData = answer.split("-");
                $('#div'+question['questionID']).append('<label for="'+answerData[0]+'"><input type="radio" id="'+answerData[0]+'" name="question'+question['questionID']+'[]"'+question['questionID']+'" value="'+answerData[1]+'" checked>'+answerData[1]+'</label>');
            });
        }
    });
</script>
</div>
<?php include 'footer.php'; ?>
</body>