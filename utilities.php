<?
    function connToDB(){
            try {
                $hostname = "localhost";
                $dbname = "MP09";
                $username = "admin";
                $pw = "admin123";
                $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
                } catch (PDOException $e) {
                    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                    exit;
                }
                return $pdo;
    }
?>