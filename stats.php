<?php session_start();
$_GET['titlePage'] = 'Estadístiques';
$_GET['bodyID'] = 'stats';
$_GET['bodyClass'] = 'stats';
include 'utilities.php'; 
?><!DOCTYPE html>
<?php include 'header.php'; ?>

<div id="divDinamic">
    <h1>Estadístiques sobre les meves enquestes</h1>
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
        var polls = <?php echo json_encode($_SESSION['pollsTeacher']); ?>;
        polls.forEach(poll => {
            createP(poll['id'], poll['title'], '', 'polls');
            if(poll['reply'] == 0){
                $("#polls").find("p:last").after("  Respostes: "+poll['reply']+ "<i class='fa-sharp fa-solid fa-eye-slash'></i>");
            }
            else{
                $("#polls").find("p:last").after("  Respostes: "+poll['reply']+ "<i class='fa-solid fa-eye'></i>");
            }
            
        });     
    }
    showPolls();
</script>



</div>
<?php include 'footer.php'; ?>
<?php
showErrors();
?>

</body>

</html>