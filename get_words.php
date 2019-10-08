<?php
    class Words
    {
        public function dbConnect()
        {
            $serverName = "localhost";
            $userName = "root";
            $userPassword = "";
            $dbName = "test";
           
            $mysqli = new mysqli($serverName,$userName,$userPassword,$dbName);

            return $mysqli;
        }

        public static function getWords()
        {
            $mysqli = Words::dbConnect();
           
           if ($result = $mysqli->query("SELECT `word` FROM `words`")) {
               while ($row = $result->fetch_assoc()){
                   $words[] = $row['word'];
               }
               $result->close();
           }
            mysqli_close($mysqli);

            return $words;
        }
    }
 
?>