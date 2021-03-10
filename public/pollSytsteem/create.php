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
    $question = isset($_POST['question']) ? $_POST['question'] : '';
    // Sends it to the DB
        $stmt = $pdo->prepare('INSERT INTO poll_questions VALUE (NULL,?,?)');
        $stmt->execute([$poll_id, $question]);
        // Gets the primary key to tie it to the answers
        $poll_questions_id = $pdo->lastInsertId();
    // Get the answers and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
    $answers = isset($_POST['answers']) ? explode(PHP_EOL, $_POST['answers']) : '';
    foreach ($answers as $answer) {
        // If the answer is empty there is no need to insert
        if (empty($answer)) continue;
        // Add answer to the "poll_answers" table
        $stmt = $pdo->prepare('INSERT INTO poll_answers VALUES (NULL, ?, ? ,0,?)');
        $stmt->execute([$poll_id, $answer,$poll_questions_id]);
    }
    // Output message
    $msg = 'Created Successfully!';

}
?>

<?=template_header('Create Poll')?>
<script>

</script>

<div class="content update">
    <h2>Create Poll</h2>
    <form action="create.php" method="post">
        <label for="title">Titel</label>
        <input type="text" name="title" id="title">
        <label for="desc">Beschrijving</label>
        <input type="text" name="desc" id="desc">


        <label for="question">Vraag</label>
        <input type="text" name="question" id="question">
        <label for="answers">Antwoorden (1 antwoord per zin)</label>
        <textarea name="answers" id="answers" required></textarea>



            <input type="button" onclick="addInput()"/>

            <span id="responce"></span>
            <script>
                var countBox =1;
                var boxName = 0;
                function addInput()
                {
                    var boxName="question"+countBox;
                    document.getElementById('responce').innerHTML+='<br/><input type="text" id="'+boxName+'" value="'+boxName+'" "  /><br/>';
                    countBox += 1;
                }
            </script>


        <input type="button" onclick="addInput()"/>

        <span id="response"></span>
            <script>
            var countBox2 =1;
            var boxName2 = 0;
            function addInput()
            {
                var boxName2="answer"+countBox2;
                document.getElementById('response').innerHTML+='<br/><input type="text" id="'+boxName2+'" value="'+boxName2+'" "  /><br/>';
                document.getElementById('response').innerHTML+='<br/><input type="text" id="'+boxName2+'" value="'+boxName2+'" "  /><br/>';
                countBox2 += 1;
            }
        </script>

        <input type="submit" value="Create">
    </form>
    <?php if ($msg): ?>
        <p><?=$msg?></p>
    <?php endif; ?>
</div>


<?=template_footer()?>
