<?php
require_once 'base.php';
verifierConnexion();

$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: liste_clients.php');
    exit();
}

// Récupérer le client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: liste_clients.php');
    exit();
}

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_client = trim($_POST['code_client'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    // Validation
    if (empty($code_client)) {
        $erreurs[] = "Le code client est obligatoire.";
    }
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $erreurs[] = "Le prénom est obligatoire.";
    }
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'email n'est pas valide.";
    }

    // Vérifier si le code client existe déjà (pour un autre client)
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE code_client = ? AND id != ?");
        $stmt->execute([$code_client, $id]);
        if ($stmt->fetchColumn() > 0) {
            $erreurs[] = "Ce code client existe déjà.";
        }
    }

    // Vérifier si l'email existe déjà (pour un autre client)
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) {
            $erreurs[] = "Cet email est déjà utilisé.";
        }
    }

    // Mise à jour
    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("UPDATE clients SET code_client = ?, nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?");
            $stmt->execute([$code_client, $nom, $prenom, $email, $telephone ?: null, $id]);
            $succes = true;
            
            // Mettre à jour les données affichées
            $client['code_client'] = $code_client;
            $client['nom'] = $nom;
            $client['prenom'] = $prenom;
            $client['email'] = $email;
            $client['telephone'] = $telephone;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Client - Gestion des Commandes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-boxes"></i> Gestion</h1>
                <p>Commandes & Clients</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="liste_clients.php" class="active"><i class="fas fa-users"></i> Clients</a></li>
                <li><a href="liste_produits.php"><i class="fas fa-box"></i> Produits</a></li>
                <li><a href="liste_commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Modifier un Client</h2>
            </div>

            <?php if ($succes): ?>
                <div class="alert alert-success">
                    Client modifié avec succès !
                    <a href="liste_clients.php" class="btn btn-sm btn-secondary" style="margin-left: 15px;">Retour à la liste</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($erreurs)): ?>
                <div class="alert alert-erreur">
                    <ul style="margin-left: 20px;">
                        <?php foreach ($erreurs as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="code_client">Code Client *</label>
                        <input type="text" id="code_client" name="code_client" value="<?php echo htmlspecialchars($client['code_client']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($client['nom']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($client['prenom']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($client['telephone'] ?? ''); ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                        <a href="liste_clients.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
