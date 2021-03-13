<?php
include 'functions.php';
$pdo = pdo_connect_mysql();
$msg = '';

$answerV = 0;


if (!empty($_POST)) {
    // Post data not empty insert a new record
    // Check if POST variable "title" exists, if not default the value to blank, basically the same for all variables
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
    // Insert new record into the "polls" table
    $stmt = $pdo->prepare('INSERT INTO polls VALUES (NULL, ?, ?)');
    $stmt->execute([$title, $desc]);
    // Below will get the last insert ID, this will be the poll id
    $poll_id = $pdo->lastInsertId();


    // Get the question
    $questions = isset($_POST['question']) ? $_POST['question'] : '';
    //$questions = isset($_POST['question']) ? explode(PHP_EOL, $_POST['question']) : '';
    foreach($questions as $question) {
        if (empty($question)) continue;
        // Sends it to the DB
        $stmt = $pdo->prepare('INSERT INTO poll_questions VALUE (NULL,?,?)');
        $stmt->execute([$poll_id, $question]);
        // Gets the primary key to tie it to the answers

    }
    $poll_questions_id = $pdo->lastInsertId();
    // Get the answers and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
    $answers = isset($_POST['answers']) ? explode(PHP_EOL, $_POST['answers']) : '';
    //$answers = isset($_POST['answers']) ? $_POST['answers'] : '';
    foreach ($answers as $answer) {
        // If the answer is empty there is no need to insert
        if (empty($answer)) continue;
        // Add answer to the "poll_answers" table
        $stmt = $pdo->prepare('INSERT INTO poll_answers VALUES (NULL, ?, ? ,0,?)');
        $stmt->execute([$poll_id, $answer,$poll_questions_id]);
    }
    // Output message
    $msg = 'Created Successfully!';
    print_r($_POST);
}
?>

<?=template_header('Create Poll')?>
<script type="text/javascript">
    function addTextArea(){
        var div = document.getElementById('div_quotes');
        div.innerHTML += "<label for='question'>Vraag</label>";
        div.innerHTML += "<textarea  name='question[]' />";
        div.innerHTML += "<label for='answers'>Antwoorden</label>";
        div.innerHTML += "<textarea name='answers' />";

        div.innerHTML += "\n<br />";
    }
</script>

<div class="content update">
    <h2>Create Poll</h2>
    <form action="create.php" method="post">
        <label for="title">Titel</label>
        <input type="text" name="title" id="title">
        <label for="desc">Beschrijving</label>
        <input type="text" name="desc" id="desc">

       <div id="div_quotes">
           <label for="question">Vraag</label>
           <textarea  name="question[]" id="question"></textarea>
           <label for="answers">Antwoorden (1 antwoord per zin)</label>
           <textarea name="answers" id="answers" required></textarea>
           <input type="button" value="Add text area" onClick="addTextArea();">
       </div>

        <input type="submit" value="Create">
    </form>
    <?php if ($msg): ?>
        <p><?=$msg?></p>
    <?php endif; ?>
</div>


<?=template_footer()?>
