<?php
error_reporting(0);
// Définir la racine du projet de manière dynamique
define('ROOT_PATH', realpath(dirname(__DIR__)));
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/du_faible_au_fort/");

// Inclusion de la connexion à la base de données
require_once(ROOT_PATH . '/connect_database.php');

class Header {
    private $db;
    private $conn;
    private $id;
    private $username;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->id = $_GET['id'] ?? null;

        $this->validateId();
        $this->fetchAdminData();
    }

    private function validateId() {
        if (!$this->id) {
            die("ID administrateur manquant");
        }
    }

    private function fetchAdminData() {
        try {
            $sql = "SELECT username_admin FROM administration WHERE id_admin=?";
            $smt = $this->conn->prepare($sql);
            $smt->execute([$this->id]);
            $result = $smt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                die("Administrateur non trouvé");
            }

            $this->username = htmlspecialchars($result['username_admin']);
        } catch (PDOException $e) {
            die("Erreur de base de données: " . $e->getMessage());
        }
    }

    public function getUsername() {
        return $this->username;
    }

    public function getId() {
        return $this->id;
    }

    public function getBaseUrl() {
        return BASE_URL;
    }

    public function getCssUrl($file) {
        return BASE_URL . 'includes/' . $file;
    }

    public function getJsUrl($file) {
        return BASE_URL . 'includes/' . $file;
    }
}

// Initialisation du header
$header = new Header();