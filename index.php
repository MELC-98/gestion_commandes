<?php
require_once 'base.php';
verifierConnexion();

// Récupérer les statistiques
$nbClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$revenusTotal = $pdo->query("SELECT COALESCE(SUM(montant_total), 0) FROM commandes")->fetchColumn();

// Produits en stock faible (moins de 5)
$produitsFaibleStock = $pdo->query("SELECT * FROM produits WHERE stock < 5 ORDER BY stock ASC LIMIT 5")->fetchAll();

// Dernières commandes
$dernieresCommandes = $pdo->query("
    SELECT c.id, c.date_commande, c.quantite, c.montant_total,
           cl.nom AS client_nom, cl.prenom AS client_prenom,
           p.nom AS produit_nom
    FROM commandes c
    JOIN clients cl ON c.client_id = cl.id
    JOIN produits p ON c.produit_id = p.id
    ORDER BY c.date_commande DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestion des Commandes</title>
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
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="liste_clients.php"><i class="fas fa-users"></i> Clients</a></li>
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
                <h2>Tableau de bord</h2>
                <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['login']); ?> !</p>
            </div>

            <!-- Cards de statistiques -->
            <div class="dashboard-cards">
                <div class="card card-clients">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <h3>Clients</h3>
                    <div class="number"><?php echo $nbClients; ?></div>
                </div>
                <div class="card card-produits">
                    <div class="card-icon"><i class="fas fa-box"></i></div>
                    <h3>Produits</h3>
                    <div class="number"><?php echo $nbProduits; ?></div>
                </div>
                <div class="card card-commandes">
                    <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
                    <h3>Commandes</h3>
                    <div class="number"><?php echo $nbCommandes; ?></div>
                </div>
                <div class="card card-revenus">
                    <div class="card-icon"><i class="fas fa-euro-sign"></i></div>
                    <h3>Revenus Totaux</h3>
                    <div class="number"><?php echo number_format($revenusTotal, 2, ',', ' '); ?> DH</div>
                </div>
            </div>

            <!-- Stats supplémentaires -->
            <div class="stats-grid">
                <!-- Produits en stock faible -->
                <div class="stat-card">
                    <h4><i class="fas fa-exclamation-triangle"></i> Produits en stock faible</h4>
                    <?php if (count($produitsFaibleStock) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produitsFaibleStock as $produit): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td class="stock-low"><?php echo $produit['stock']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="empty-message">Aucun produit en stock faible</p>
                    <?php endif; ?>
                </div>

                <!-- Dernières commandes -->
                <div class="stat-card">
                    <h4><i class="fas fa-clock"></i> Dernières commandes</h4>
                    <?php if (count($dernieresCommandes) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Produit</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dernieresCommandes as $commande): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($commande['client_nom'] . ' ' . $commande['client_prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['produit_nom']); ?></td>
                                    <td><?php echo number_format($commande['montant_total'], 2, ',', ' '); ?> DH</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="empty-message">Aucune commande enregistrée</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
