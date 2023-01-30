<?php session_start();
$_GET['titlePage'] = 'Estadístiques';
$_GET['bodyID'] = 'stats';
$_GET['bodyClass'] = 'stats';
include 'utilities.php'; 
?><!DOCTYPE html>
<?php include 'header.php'; ?>

<div id="divDinamic">
<?php
function getPolls($id)
{
    $startSession = connToDB()->prepare("SELECT p.id, p.title, sum(sp.reply) as reply from poll p inner join teacher_poll tp on p.ID=tp.pollID INNER JOIN student_poll sp on sp.pollID=tp.pollID where tp.teacherID = :id group by p.id;
    ");
    $startSession->bindParam(":id", $id);
    $done = $startSession->execute();
    if ($done) {
        writeInLog("S", "Enquestes rebudes correctament", $_SESSION["ID"]);
    } else {
        writeInLog("W", "Enquestes no rebudes correctament", $_SESSION["ID"]);
    }
    $_SESSION['pollsTeacher'] = [];
    foreach ($startSession as $poll) {
        array_push($_SESSION['pollsTeacher'], $poll);
    }
}
getPolls($_SESSION['ID']);
?>


<script>

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

    function showPolls(){
        createDiv('polls', 'divDinamic');
        $("#polls").append("<h1>Estadístiques sobre les meves enquestes</h1>")
        var polls = <?php echo json_encode($_SESSION['pollsTeacher']); ?>;
        polls.forEach(poll => {
            createP(poll['id'], poll['title'], '', 'polls');
            if(poll['reply'] == 0){
                $("#polls").find("p:last").after("  Respostes: "+poll['reply']+ "<i class='fa-sharp fa-solid fa-eye-slash'></i>");
            }
            else{
                $("#polls").find("p:last").after("  Respostes: "+poll['reply']+ "<i id="+poll['id']+" name='pollid' class='fa-solid fa-eye'></i>");
            }
        });
        clickSeeStatistics();
    }

    function deleteDiv(id){
            $("#"+id+"").remove();
        }

    function clickSeeStatistics() {
        $(".fa-eye").click(function () {
            var numPoll = $(this).attr('id');
            $("#polls").append("<form id='showStatsForm' action='checkoutForms.php' method='POST'><input hidden type='text' name='idPoll' id='idPoll'><input hidden type='submit' name='sendPollId' id='sendPollId'> </form>");
            $("#idPoll").val(numPoll);
            $("#showStatsForm").submit();
           
        });
    }

    function showStatsPoll(){
        createDiv('polls', 'divDinamic');
        var optionsPoll = <?php echo json_encode($_SESSION['optionsQuestion']);?>;
        $("#polls").append("<h1>"+optionsPoll[0]['idPoll']+"</h1>");
        $("#polls").append("<h3>Preguntes númeriques:</h3>");
        optionsPoll.forEach(element => {
            if(element['type'] == 1){
                createP(element['questionUser'], element['answerText'], element['optionUser'], 'polls');
                $("#polls").find("p:last").after("   -> "+element['number']);
            }
            
        });
        console.log(optionsPoll);
        $("#polls").append("<h3>Opció simple:</h3>");
        optionsPoll.forEach(element => {
            if(element['type'] == 3){
                createP(element['questionUser'], element['answerText'], element['optionUser'], 'polls');
                $("#polls").find("p:last").after("   -> "+element['number']);
            }
            
        });
        $("#polls").append("<h3>Respostes obertes:</h3>");
        $("#polls").append('<button type="button" style="text-align:center" class="collapsible">Veure respostes obertes</button><div id="content" class="content">');
        var optionsPollCollapsable = <?php echo json_encode($_SESSION['optionsQuestionCollapsable']);?>;
        optionsPollCollapsable.forEach(element => {
            if(element['type'] == 2){
                createP(element['questionUser'], element['answerAreaText'], element['optionUser'], 'content');
            }
        });
        var coll = $(".collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
            content.style.display = "none";
            } else {
            content.style.display = "block";
            }
        });
        }
    }
  

</script>

<?php
if(!isset($_GET['poll'])){
    echo "<script>showPolls();</script>";
}
else{
    echo "<script>showStatsPoll();</script>";
}
?>


</div>
<?php include 'footer.php'; ?>
<?php
showErrors();
?>

</body>

</html>