<?php
require_once('get_words.php');
/*
 * 3d10-word-search-generator.php
 * by Duane O'Brien - http://chaoticneutral.net
 * written for IBM DeveloperWorks
 */

$letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

$words = explode("\n", file_get_contents('words.list.txt'));

$used = array();

shuffle($words);

$length = 10;
$height = 10;

$grid = array();
for ($i = 0; $i < $length; $i++) {
    // You start by filling the array with periods
    $grid[$i] = array_fill(0,$length,'.');
    // Only do a word every other line.
    if ($i%2 == 0) {
        $word = array_shift($words);
        while (strlen($word) > $length) {
            // Exclude words that are too long for the puzzle
            $word = array_shift($words);
        }
        // Randomize the starting point of the word in the line
        $start = rand(0, $length-strlen($word));
        $wordletters = str_split($word);
        // Splice the array over the existing array line.
        array_splice($grid[$i], $start, count($wordletters), $wordletters);
        $used[$word] = true;
    }
}
//echo "<pre/>"; print_r($words);  echo "<pre/>";

for ($i = 0; $i < $height; $i++) {
    $line = '';
    // This seems a little weird, but by flipping the height and length, we can access the vertical lines
    // like they are a single array.
    for ($n = 0; $n < $length; $n++) {
        // The odd lines just get random letters
        if ($i%2 == 1) {
            // If the letter is still a period, replace it with a random number.
            if ($grid[$n][$i] == '.') {
                $grid[$n][$i] = $letters[rand(0,25)];
            }
        }
        $line .= $grid[$n][$i];
    }
    // Only do a word every other line
    if ($i%2 == 0) {
        // Walk through the words until we find one that works.
        foreach ($words as $word) {
            if (strlen($word) > $height) {
                // If the word is too long, toss it
                continue;
			}else if (strlen($word) == $height && !preg_match("/^" . $line . "$/",$word)) {
                // If the word is just long enough but doesn't match the line we have, toss it.
                continue;
            } else {
                // We have a shorter word, now let's see if it matches
                if (preg_match("/" . $line . "/",$word)) {
                    // It's an exact match.  Fill it in
                    $used[$word] = true;
                    $wordletters = str_split($word);
                    for ($c = 0; $c < $height; $c++) {
                        $grid[$c][$i] = array_shift($wordletters);
                    }
                    $result .= $word . "\n";
                    break;
                } else {
                    // The word is shorter than the the line.  
                    // We need to step through the line to see if it will match
                    $wordlen = strlen($word);
                    $margin = $length - $wordlen;
                    $found = false;
                    for ($x = 0; $x < $margin; $x++) {
                        $match = substr($line, $x, $wordlen);
                        if (preg_match("/" . $match . "/",$word)) {
                                $found = true;
                                break;
                        }
                    }
                    // Did we find a match?  If so, fill it in.
                    if ($found) {
                        $used[$word] = true;
                        $wordletters = str_split($word);
                        for ($c = 0; $c < $height; $c++) {
                            if ( $grid[$c][$i] == '.' && ($c < $x || empty($wordletters))) {
                                // This is a period, and we're either before or after the word we are placing
                                // therefore, put in a random letter.
                                $grid[$c][$i] = $letters[rand(0,25)];
                            } else  {
                                // We're in the word we need to place.
                                if ($grid[$c][$i] == '.') {
                                    // This is a blank character, replace it
                                    $grid[$c][$i] = array_shift($wordletters);
                                } else if ($c >= $x) {
                                    // This is not a blank character, but we stil need to shit one off the word.
                                    array_shift($wordletters);
                                }
                            }
                        }
                    break;
                    }
                }
            }
        }
    }
}

echo "<pre>";
for ($i = 0; $i < $length; $i++) {
    echo (implode('', $grid[$i]) . "\n");
}
echo "</pre>";

echo "<br />\n<br />\n<b>Find These Words : </b><br />\n";
echo (implode("<br />\n", array_keys($used)));

?>