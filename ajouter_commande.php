<?php
require_once 'base.php';
verifierConnexion();

// Récupérer la liste des clients et des produits
$clients = $pdo->query("SELECT id, code_client, nom, prenom, email FROM clients ORDER BY nom, prenom")->fetchAll();
$produits = $pdo->query("SELECT id, nom, prix, stock FROM produits WHERE stock > 0 ORDER BY nom")->fetchAll();

$erreurs = [];
$succes = false;
$fichierGenere = '';

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
        } elseif ($quantite > $produit['stock']) {
            $erreurs[] = "Stock insuffisant. Stock disponible : " . $produit['stock'];
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

    // Insertion et mise à jour du stock
    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();
            
            // Calculer le montant total
            $montant_total = $produit['prix'] * $quantite;
            
            // Insérer la commande
            $stmt = $pdo->prepare("INSERT INTO commandes (client_id, produit_id, quantite, montant_total) VALUES (?, ?, ?, ?)");
            $stmt->execute([$client_id, $produit_id, $quantite, $montant_total]);
            $commande_id = $pdo->lastInsertId();
            
            // Réduire le stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantite, $produit_id]);
            
            $pdo->commit();
            
            // Générer le fichier texte
            $dateHeure = date('Y-m-d_H-i-s');
            $nomFichier = "commande_{$dateHeure}.txt";
            $cheminFichier = "Facture/{$nomFichier}";
            
            $contenu = "=====================================\n";
            $contenu .= "         FACTURE DE COMMANDE         \n";
            $contenu .= "=====================================\n\n";
            $contenu .= "Date : " . date('d/m/Y H:i:s') . "\n";
            $contenu .= "N° Commande : " . $commande_id . "\n\n";
            $contenu .= "-------------------------------------\n";
            $contenu .= "INFORMATIONS CLIENT\n";
            $contenu .= "-------------------------------------\n";
            $contenu .= "Code client : " . $client['code_client'] . "\n";
            $contenu .= "Nom : " . $client['nom'] . "\n";
            $contenu .= "Prénom : " . $client['prenom'] . "\n";
            $contenu .= "Email : " . $client['email'] . "\n\n";
            $contenu .= "-------------------------------------\n";
            $contenu .= "DÉTAILS DE LA COMMANDE\n";
            $contenu .= "-------------------------------------\n";
            $contenu .= "Produit : " . $produit['nom'] . "\n";
            $contenu .= "Prix unitaire : " . number_format($produit['prix'], 2, ',', ' ') . " DH\n";
            $contenu .= "Quantité : " . $quantite . "\n";
            $contenu .= "-------------------------------------\n";
            $contenu .= "MONTANT TOTAL : " . number_format($montant_total, 2, ',', ' ') . " DH\n";
            $contenu .= "=====================================\n";
            
            file_put_contents($cheminFichier, $contenu);
            $fichierGenere = $nomFichier;
            
            $succes = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
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
    <title>Ajouter une Commande - Gestion des Commandes</title>
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
                <h2>Ajouter une Commande</h2>
            </div>

            <?php if ($succes): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Commande ajoutée avec succès !
                    <?php if ($fichierGenere): ?>
                        <br>Fichier généré : <strong><?php echo htmlspecialchars($fichierGenere); ?></strong>
                    <?php endif; ?>
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
                                <option value="<?php echo $client['id']; ?>" <?php echo (isset($_POST['client_id']) && $_POST['client_id'] == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom'] . ' (' . $client['code_client'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="produit_id">Produit *</label>
                        <select id="produit_id" name="produit_id" required>
                            <option value="">-- Sélectionner un produit --</option>
                            <?php foreach ($produits as $produit): ?>
                                <option value="<?php echo $produit['id']; ?>" data-prix="<?php echo $produit['prix']; ?>" data-stock="<?php echo $produit['stock']; ?>" <?php echo (isset($_POST['produit_id']) && $_POST['produit_id'] == $produit['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produit['nom'] . ' - ' . number_format($produit['prix'], 2, ',', ' ') . ' DH (Stock: ' . $produit['stock'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité *</label>
                        <input type="number" id="quantite" name="quantite" min="1" value="<?php echo htmlspecialchars($_POST['quantite'] ?? '1'); ?>" required>
                    </div>

                    <div class="form-group" id="montant-preview" style="background: #f8f9fa; padding: 15px; border-radius: 5px; display: none;">
                        <strong>Montant total estimé : <span id="montant-total" style="color: #667eea; font-size: 18px;">0,00 DH</span></strong>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer la commande</button>
                        <a href="liste_commandes.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const produitSelect = document.getElementById('produit_id');
        const quantiteInput = document.getElementById('quantite');
        const montantPreview = document.getElementById('montant-preview');
        const montantTotal = document.getElementById('montant-total');

        function calculerMontant() {
            const selectedOption = produitSelect.options[produitSelect.selectedIndex];
            const prix = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
            const quantite = parseInt(quantiteInput.value) || 0;
            const total = prix * quantite;
            
            if (prix > 0 && quantite > 0) {
                montantPreview.style.display = 'block';
                montantTotal.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
            } else {
                montantPreview.style.display = 'none';
            }
        }

        produitSelect.addEventListener('change', calculerMontant);
        quantiteInput.addEventListener('input', calculerMontant);
        
        // Calcul initial
        calculerMontant();
    </script>
</body>
</html>
