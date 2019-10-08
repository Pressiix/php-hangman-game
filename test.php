<?php 
echo "(".$_GET['guesses']." guesses remaining)"; 
?>
<br/><br/>
<div id="clue"> <?= $_GET['clue'] ?> </div>
<br/>
		<form action="hangman-solution.php" method="post">
			<input name="newgame" type="hidden" value="true">
			<input type="submit" value="New Game">
		</form>

        