<?php
require_once 'base.php';

// Si déjà connecté, rediriger vers le dashboard
if (estConnecte()) {
    header('Location: index.php');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        // Rechercher l'utilisateur dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        // Vérifier le mot de passe (admin2026)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            header('Location: index.php');
            exit();
        } else {
            $erreur = 'Login ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Commandes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Gestion des Commandes</h1>
            <h2>Connexion</h2>
            
            <?php if ($erreur): ?>
                <div class="alert alert-erreur"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </form>
            
            
        </div>
    </div>
</body>
</html>
