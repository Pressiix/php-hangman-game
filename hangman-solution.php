<?php
require_once('get_words.php');

$MAX_GUESSES  = 6;                           # max guesses that can be made before game ends
$guesses   = $MAX_GUESSES;                   # number of guesses the player has left
$available = "abcdefghijklmnopqrstuvwxyz";   # letters available to be guessed

# we name our cookies with LAB5_SOLUTION_ prefix so that they don't mix up with your own...

if (isset($_COOKIE["HANGMAN_word"]) && !isset($_POST["newgame"])) {
	$word = $_COOKIE["HANGMAN_word"];    # read previously chosen word from cookie
	$available = $_COOKIE["HANGMAN_available"];
	$guesses = $_COOKIE["HANGMAN_guesses"];
} else {
	//$words = file("words.txt", FILE_IGNORE_NEW_LINES);   # get word array from source file //or SQL Query
	$words = Words::getWords();  # get word array from SQL Query
	$word  = $words[rand(0, count($words))];	//random some word from array
	
	setcookie("HANGMAN_word", $word);
	setcookie("HANGMAN_available", $available);
	setcookie("HANGMAN_guesses", $guesses);
}

if (isset($_GET["guess"]) && $guesses > 0) {   # user wants to guess a letter
	$guess = $_GET["guess"];
	if (preg_match("/$guess/", $available)) {
		# not already guessed before; make the guess
		$available = preg_replace("/$guess/", " ", $available);
		setcookie("HANGMAN_available", $available);
		
		if (!preg_match("/$guess/", $word)) {   # an incorrect guess
			$guesses--;
			setcookie("HANGMAN_guesses", $guesses);
		}
	}
}

# produce current clue string based on available letters
$clue = preg_replace("/[$available]/", "_", $word);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Hangman</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  </head>
  
  <body>
    
    <div>
      <img src="https://miro.medium.com/max/318/1*TzbmJ5RWXeyjnT1ZuG664A.png" alt="hangman" /> 
	  <br/>
      (<?= $guesses ?> guesses remaining)
    </div>
    <br/>
    <div id="clue"> <?= $clue ?> </div>
    
		<!--form action="hangman-solution.php">
			<input name="guess" type="text" size="1" maxlength="1" autofocus="autofocus" >
			
			<input type="submit" value="Guess" >
		</!--form -->
		<br/>
		<form action="hangman-solution.php" method="post">
			<input name="newgame" type="hidden" value="true">
			<input type="submit" value="New Game">
		</form>
	<br/>
		<!-- advanced feature: guessing letter buttons -->
    <div id="letters">
		<table>
			<tr>
      <?php
	  $row = 0;
      $LETTERS = "abcdefghijklmnopqrstuvwxyz";
      for ($i = 0; $i < strlen($LETTERS); $i++) {
				?>
				<form action="hangman-solution.php">
					
							<td>
								<input name="guess" type="hidden" value="<?= $LETTERS[$i] ?>" />
								<input type="submit" value="<?= $LETTERS[$i] ?>" <?= (preg_match("/{$LETTERS[$i]}/", $available) && $guesses > 0 && $clue != $word) ? "" : "disabled=\"disabled\"" ?> style="width:40px;">
							</td>
				</form>
				<?php
				$row++;
				if($row == 7)
				{
					echo "</tr>";
					$row = 0;
				}
			}
			?>
				
			</table>
    </div>
			
    <?php if ($clue == $word && $guesses > 0) { ?>
		<script>alert(' Congratulations!  You win! ')</script>
    <?php } ?>

    <?php if ($guesses == 0) { ?>
		<script>alert(' Game over! You lost! ')</script>
    <?php } ?>
		<br/>
    <div id="hint">
    	HINT: The word is: <code>"<?= $word ?>"</code> <br />
    	The letters available are: <code>"<?= $available ?>"</code>
    </div>
  </body>
</html>