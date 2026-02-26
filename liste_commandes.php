<?php
require_once 'base.php';
verifierConnexion();

// Suppression d'une commande
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    try {
        // Récupérer les informations de la commande pour remettre le stock
        $stmt = $pdo->prepare("SELECT produit_id, quantite FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        $commande = $stmt->fetch();
        
        if ($commande) {
            // Remettre le stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock + ? WHERE id = ?");
            $stmt->execute([$commande['quantite'], $commande['produit_id']]);
            
            // Supprimer la commande
            $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        header('Location: liste_commandes.php?message=Commande supprimée avec succès');
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Recherche
$recherche = $_GET['recherche'] ?? '';
$sql = "
    SELECT c.id, c.date_commande, c.quantite, c.montant_total,
           cl.id AS client_id, cl.nom AS client_nom, cl.prenom AS client_prenom,
           p.id AS produit_id, p.nom AS produit_nom, p.prix AS produit_prix
    FROM commandes c
    JOIN clients cl ON c.client_id = cl.id
    JOIN produits p ON c.produit_id = p.id
";
$params = [];

if (!empty($recherche)) {
    $sql .= " WHERE cl.nom LIKE ? OR cl.prenom LIKE ?";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$sql .= " ORDER BY c.date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Commandes - Gestion des Commandes</title>
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
                <h2>Liste des Commandes</h2>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (isset($erreur)): ?>
                <div class="alert alert-erreur"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <a href="ajouter_commande.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter une commande</a>
                    <form method="GET" class="search-box">
                        <input type="text" name="recherche" placeholder="Rechercher par client..." value="<?php echo htmlspecialchars($recherche); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($recherche): ?>
                            <a href="liste_commandes.php" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Montant total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($commandes) > 0): ?>
                            <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
                                <td><?php echo htmlspecialchars($commande['client_nom'] . ' ' . $commande['client_prenom']); ?></td>
                                <td><?php echo htmlspecialchars($commande['produit_nom']); ?></td>
                                <td><?php echo $commande['quantite']; ?></td>
                                <td><?php echo number_format($commande['produit_prix'], 2, ',', ' '); ?> DH</td>
                                <td><strong><?php echo number_format($commande['montant_total'], 2, ',', ' '); ?> DH</strong></td>
                                <td class="actions">
                                    <a href="modifier_commande.php?id=<?php echo $commande['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="liste_commandes.php?supprimer=<?php echo $commande['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');"><i class="fas fa-trash"></i> Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-message">
                                    <i class="fas fa-inbox"></i>
                                    <p>Aucune commande trouvée</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="pagination-info">
                    <?php echo count($commandes); ?> commande(s) trouvée(s)
                </div>
            </div>
        </main>
    </div>
</body>
</html>
