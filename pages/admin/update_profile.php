<?php
session_start();
require_once('../../connect_database.php');
if($_SERVER['REQUEST_METHOD'] === "POST"){
 $id = $_POST['id_admin'];
 $username = $_POST['username'];
 $database = new Database(); 
 $conn = $database->getConnection();

 try {
  $sql = "UPDATE administration SET username_admin = ? WHERE id_admin = ?";
  $smt = $conn->prepare($sql);
  $smt->execute([$username, $id]);
  echo "mise à jour réussie";
 } catch (PDOException $e) {
  echo 'Erreur : ' .$e->getMessage();
 }
}
header("Location: my_account.php?id=$id");
exit();