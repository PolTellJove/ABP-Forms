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
<div id='divTeacher'>

    <?php if ($user['role'] == 1) { ?>
        <br>
        <div class="messageBox"></div>
        <div id="divButtons">
            <a class="button" id='createQuestion'><i class="fa-regular fa-circle-question"></i> CREAR PREGUNTA</a>
            <a class="button"><i class="fa-solid fa-square-poll-vertical"></i> CREAR ENQUESTA</a>
            <a class="button" id='questionList'><i class="fa-solid fa-list"></i> LLISTAT PREGUNTES</a>
            <a class="button active" id='pollList'><i class="fa-solid fa-list"></i> LLISTAT ENQUESTES</a>
        </div>
    <?php } ?>
    <div id="divDinamic">
        <?php
        function getPolls()
        {
            $polls = getTable('poll');
            echo '<div id="polls">';
            foreach ($polls as $poll) {
                echo "\n" . '<p id=' . $poll['ID'] . '>' . $poll['title'] . '</p>';
            }
            echo "\n" . '</div>';
        }

        function getQuestions()
        {
            $questions = getTable('question');
            echo '<div id="questions" hidden>';
            foreach ($questions as $question) {
                echo "\n" . '<p id=' . $question['ID'] . '>' . $question['question'] . '</p>';
            }
            echo "\n" . '</div>';
        }

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

        // function newQuestion()
        // {
        //     echo '<form action="checkoutForms.php" method="POST" id="newQuestion" hidden>';
        //     getTypes();
        //     echo "<input type='text' name='questionTitle' id='questionTitle'><br>";
        //     echo '<textarea id="taQuestion" readonly></textarea><br>';
        //     getOptions();
        //     echo '<input id="saveQuestion" type="submit" value="Guardar"/>';
        //     echo '<input id="clearForm" type="reset" value="Cancel·lar"/>';
        //     echo "\n" . '</form>';
        // }
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
    $('#radioGroup').hide();
    $("#taQuestion").hide();
    $("#saveQuestion").hide();

    var numOption = 3;

    function changeColor(button_id) {
        $('.button').css("background-color", "#62929E");
        $(button_id).css("background-color", "blue");
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

    function createInput(type, name, parentID, id, value, placeholder, elementInsertBefore) {
        var input = $("<input>");
        input.attr("type", type);
        input.attr("name", name);
        input.attr("id", id);
        if (value) {
            input.val(value);
        }
        if (placeholder) {
            input.attr("placeholder", placeholder);
        }
        if(elementInsertBefore){
            input.insertBefore("#addOption")
        }
        else{
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
        space = $("<br>");
        textArea.insertBefore("#" + insertBeforeThat);
        space.insertBefore("#" + insertBeforeThat);
        $("#" + parentID).append($("<br>"));
    }

    function newQuestion(divID) {
        createDiv("newQuestion", "divDinamic");
        createForm("formNewQuestion", "newQuestion", "checkoutForms.php", "POST");
        var types = <?php echo json_encode($_SESSION['arrayTypes']); ?>;
        createSelectForAddQuestion(types, "formNewQuestion");
        $("#formNewQuestion").append("<br>");
        createInput("text", "questionTitle", "formNewQuestion", "questionTitle", null, "Títol de la pregunta", null);
        checkSelect();
        $("#formNewQuestion").append("<br>");
        $("#formNewQuestion").append("<br>");
        createInput("reset", null, "formNewQuestion", "clearForm", "Cancel·lar", null, null);
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
                createInput("text", "option1", "simpleOption", "questionTitle", null, "Escrigui la opció");
                createInput("text", "option2", "simpleOption", "questionTitle", null, "Escrigui la opció");
                $("#simpleOption").append("<br>");
                $("#simpleOption").append("<i id='addOption' class='fa fa-plus' aria-hidden='true'></i>");
                addEventForAddInputs();
                deleteInputs();
                
            }

            if (document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0) {
                $("#saveQuestion").show();
            } else {
                $("#saveQuestion").hide();
            }
        });
    }

    function addEventForAddInputs(){
        $("#addOption").click(function () {
            createInput("text", "option"+numOption, "simpleOption", "questionTitle", null, "Escrigui la opció", true);
            $("#simpleOption").append("<i id='iconDelete"+numOption+"' class='fa fa-minus' aria-hidden='true'></i>");
            numOption++;
            deleteInputs();
        })
    }

    function deleteInputs(){
        var element = "iconDelete"+numOption;
        console.log(element);
        $(element).click( function() {
            let toDelete = "option"+numOption;
            console.log("hey");
        $("input[name='"+toDelete+"']").remove();
        $(this).remove();
        });
    }

    $(document).ready(function () {
        $("#questionList").click(function () {
            $("#polls").hide();
            $("#newQuestion").hide();
            $("#questions").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        $("#pollList").click(function () {
            $("#questions").hide();
            $("#newQuestion").hide();
            $("#polls").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });

        

        $("#createQuestion").click(function () {
            $("#polls").hide();
            $("#questions").hide();
            if (!$("#newQuestion").length) {
                newQuestion('newQuestion');
            }
            $("#newQuestion").show();
            $('.button').removeClass('active');
            $(this).addClass('active');
        });



        $('#questionTitle').on('input', function (e) {
            if (/^\s/.test($('#questionTitle').val())) {
                $('#questionTitle').val('');
            }
            if (document.getElementById("questionTitle").value.length && $("#typeSelect option:selected").attr("id") != 0) {
                $("#saveQuestion").show();
            } else {
                $("#saveQuestion").hide();
            }
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