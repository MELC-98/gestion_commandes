<?php
require_once 'base.php';
verifierConnexion();

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    // Validation
    if (empty($nom)) {
        $erreurs[] = "Le nom du produit est obligatoire.";
    }
    if ($prix <= 0) {
        $erreurs[] = "Le prix doit être supérieur à 0.";
    }
    if ($stock < 0) {
        $erreurs[] = "Le stock ne peut pas être négatif.";
    }

    // Insertion
    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $description ?: null, $prix, $stock]);
            $succes = true;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit - Gestion des Commandes</title>
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
                <li><a href="liste_clients.php"><i class="fas fa-users"></i> Clients</a></li>
                <li><a href="liste_produits.php" class="active"><i class="fas fa-box"></i> Produits</a></li>
                <li><a href="liste_commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Ajouter un Produit</h2>
            </div>

            <?php if ($succes): ?>
                <div class="alert alert-success">
                    Produit ajouté avec succès !
                    <a href="liste_produits.php" class="btn btn-sm btn-secondary" style="margin-left: 15px;">Retour à la liste</a>
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
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prix">Prix (DH) *</label>
                        <input type="number" id="prix" name="prix" step="0.01" min="0.01" value="<?php echo htmlspecialchars($_POST['prix'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                        <a href="liste_produits.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
