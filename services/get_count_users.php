<?php
require_once '../connect_database.php';

$db = new Database();
$conn = $db->getConnection();

class Users {

    function GetCountUserRegistrer(){
        global $conn; 
        $query = 'SELECT COUNT(*) as total FROM utilisateurs';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    function GetUsersAtifs(){
      global $conn;

     $query = "
         SELECT COUNT(a.user_id) as total
         FROM session_app as a
         INNER JOIN utilisateurs as u ON a.user_id = u.id_utilisateurs
         WHERE a.date_dec IS NULL OR a.date_dec = '00:00:00 00:00'
     ";

     $stmt = $conn->prepare($query);
     $stmt->execute();
     $row = $stmt->fetch(PDO::FETCH_ASSOC);

     return $row['total'];

    }


    
}