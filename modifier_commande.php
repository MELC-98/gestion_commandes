<?php
require_once 'base.php';
verifierConnexion();

$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: liste_commandes.php');
    exit();
}

// Récupérer la commande
$stmt = $pdo->prepare("
    SELECT c.*, p.stock AS produit_stock, p.prix AS produit_prix, p.nom AS produit_nom
    FROM commandes c
    JOIN produits p ON c.produit_id = p.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: liste_commandes.php');
    exit();
}

// Récupérer la liste des clients et des produits
$clients = $pdo->query("SELECT id, code_client, nom, prenom, email FROM clients ORDER BY nom, prenom")->fetchAll();
$produits = $pdo->query("SELECT id, nom, prix, stock FROM produits ORDER BY nom")->fetchAll();

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = intval($_POST['client_id'] ?? 0);
    $produit_id = intval($_POST['produit_id'] ?? 0);
    $quantite = intval($_POST['quantite'] ?? 0);

    // Validation
    if ($client_id === 0) {
        $erreurs[] = "Veuillez sélectionner un client.";
    }
    if ($produit_id === 0) {
        $erreurs[] = "Veuillez sélectionner un produit.";
    }
    if ($quantite <= 0) {
        $erreurs[] = "La quantité doit être supérieure à 0.";
    }

    // Vérifier le stock
    if (empty($erreurs) && $produit_id > 0) {
        $stmt = $pdo->prepare("SELECT stock, prix, nom FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();
        
        if (!$produit) {
            $erreurs[] = "Produit introuvable.";
        } else {
            // Si c'est le même produit, on ajoute l'ancienne quantité au stock disponible
            $stockDisponible = $produit['stock'];
            if ($produit_id == $commande['produit_id']) {
                $stockDisponible += $commande['quantite'];
            }
            
            if ($quantite > $stockDisponible) {
                $erreurs[] = "Stock insuffisant. Stock disponible : " . $stockDisponible;
            }
        }
    }

    // Vérifier le client
    if (empty($erreurs) && $client_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch();
        
        if (!$client) {
            $erreurs[] = "Client introuvable.";
        }
    }

    // Mise à jour
    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();
            
            // Calculer le montant total
            $montant_total = $produit['prix'] * $quantite;
            
            // Remettre l'ancien stock si c'est le même produit
            if ($produit_id == $commande['produit_id']) {
                $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE id = ?");
                $stmt->execute([$commande['quantite'], $produit_id]);
            } else {
                // Remettre le stock de l'ancien produit
                $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE id = ?");
                $stmt->execute([$commande['quantite'], $commande['produit_id']]);
            }
            
            // Mettre à jour la commande
            $stmt = $pdo->prepare("UPDATE commandes SET client_id = ?, produit_id = ?, quantite = ?, montant_total = ? WHERE id = ?");
            $stmt->execute([$client_id, $produit_id, $quantite, $montant_total, $id]);
            
            // Réduire le nouveau stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantite, $produit_id]);
            
            $pdo->commit();
            
            // Mettre à jour les données affichées
            $commande['client_id'] = $client_id;
            $commande['produit_id'] = $produit_id;
            $commande['quantite'] = $quantite;
            $commande['montant_total'] = $montant_total;
            $commande['produit_prix'] = $produit['prix'];
            $commande['produit_nom'] = $produit['nom'];
            
            $succes = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
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
    <title>Modifier une Commande - Gestion des Commandes</title>
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
                <li><a href="liste_produits.php"><i class="fas fa-box"></i> Produits</a></li>
                <li><a href="liste_commandes.php" class="active"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Modifier une Commande</h2>
            </div>

            <?php if ($succes): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Commande modifiée avec succès !
                    <br><a href="liste_commandes.php" class="btn btn-sm btn-secondary" style="margin-top: 10px;">Retour à la liste</a>
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
                        <label for="client_id">Client *</label>
                        <select id="client_id" name="client_id" required>
                            <option value="">-- Sélectionner un client --</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" <?php echo ($commande['client_id'] == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom'] . ' (' . $client['code_client'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="produit_id">Produit *</label>
                        <select id="produit_id" name="produit_id" required>
                            <option value="">-- Sélectionner un produit --</option>
                            <?php foreach ($produits as $produit): 
                                // Calculer le stock affiché (en tenant compte de la quantité actuelle de la commande si c'est le même produit)
                                $stockAffiche = $produit['stock'];
                                if ($produit['id'] == $commande['produit_id']) {
                                    $stockAffiche += $commande['quantite'];
                                }
                            ?>
                                <option value="<?php echo $produit['id']; ?>" data-prix="<?php echo $produit['prix']; ?>" <?php echo ($commande['produit_id'] == $produit['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produit['nom'] . ' - ' . number_format($produit['prix'], 2, ',', ' ') . ' € (Stock: ' . $stockAffiche . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité *</label>
                        <input type="number" id="quantite" name="quantite" min="1" value="<?php echo htmlspecialchars($commande['quantite']); ?>" required>
                    </div>

                    <div class="form-group" id="montant-preview" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                        <strong>Montant total : <span id="montant-total" style="color: #667eea; font-size: 18px;"><?php echo number_format($commande['montant_total'], 2, ',', ' '); ?> €</span></strong>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                        <a href="liste_commandes.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const produitSelect = document.getElementById('produit_id');
        const quantiteInput = document.getElementById('quantite');
        const montantTotal = document.getElementById('montant-total');

        function calculerMontant() {
            const selectedOption = produitSelect.options[produitSelect.selectedIndex];
            const prix = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
            const quantite = parseInt(quantiteInput.value) || 0;
            const total = prix * quantite;
            
            montantTotal.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }

        produitSelect.addEventListener('change', calculerMontant);
        quantiteInput.addEventListener('input', calculerMontant);
    </script>
</body>
</html>
