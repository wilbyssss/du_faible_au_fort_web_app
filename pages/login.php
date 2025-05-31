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

        if ($user && password_verify($password, $user['password_admin'])) {
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
    header('Location: home_page.php?id=' .$_SESSION['user_id']);
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #343a40;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out;
        }
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .form-check-input {
            margin-right: 0.5rem;
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 5px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .text-center {
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="login-container">
       <!-- <div class="logo">
            <img src="../images/logo.png" alt="Du Faible au Fort">
        </div>-->
        <h2>Connexion à l'espace admin</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rester_connecte" name="rester_connecte">
                <label class="form-check-label" for="rester_connecte">Rester connecté</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
    </div>
</body>
</html>