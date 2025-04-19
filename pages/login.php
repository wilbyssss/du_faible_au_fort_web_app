<?php
session_start();
require_once('../connect_database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('SELECT id_admin, password_admin FROM administration WHERE email_admin = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password_admin']) {
            $_SESSION['connecte'] = true;
            $_SESSION['user_id'] = $user['id_admin'];
            
            // Gestion du "Rester connecté"
            if (isset($_POST['rester_connecte'])) {
                $token = bin2hex(random_bytes(32));
                $expire = time() + (86400 * 30); // 30 jours
                
                // Mettre à jour la base de données
                $stmt = $conn->prepare('UPDATE administration SET remember_token = ? WHERE id_admin = ?');
                $stmt->execute([$token, $user['id_admin']]);
                
                // Définir le cookie
                setcookie('remember_me', $token, [
                    'expires' => $expire,
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
            
            header('Location: home_page.php?id=' . $user['id_admin']);
            exit;
        } else {
            $error = "Identifiants incorrects";
        }
    } catch (PDOException $e) {
        $error = "Erreur de connexion: " . $e->getMessage();
    }
}

// Empêcher la mise en cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Si déjà connecté, rediriger vers la page d'accueil
if (isset($_SESSION['connecte']) && $_SESSION['connecte'] === true) {
    header('Location: home_page.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (isset($error)): ?>
            <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <label style="display: block; margin: 10px 0;">
                <input type="checkbox" name="rester_connecte"> Rester connecté
            </label>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>