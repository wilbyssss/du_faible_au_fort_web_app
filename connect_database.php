<?php
class Database {
    private $host = "localhost"; 
    private $db_name = "du_faible_au_fort"; 
    private $username = "lenyx"; 
    private $password = "password"; 
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Erreur de connexion : " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
