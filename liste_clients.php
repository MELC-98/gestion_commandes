<?php
require_once 'base.php';
verifierConnexion();

// Suppression d'un client
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    try {
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: liste_clients.php?message=Client supprimé avec succès');
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Recherche
$recherche = $_GET['recherche'] ?? '';
$sql = "SELECT * FROM clients";
$params = [];

if (!empty($recherche)) {
    $sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR code_client LIKE ?";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$sql .= " ORDER BY nom, prenom";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients - Gestion des Commandes</title>
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
                <h2>Liste des Clients</h2>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (isset($erreur)): ?>
                <div class="alert alert-erreur"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <a href="ajouter_client.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter un client</a>
                    <form method="GET" class="search-box">
                        <input type="text" name="recherche" placeholder="Rechercher par nom..." value="<?php echo htmlspecialchars($recherche); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($recherche): ?>
                            <a href="liste_clients.php" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Code Client</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($clients) > 0): ?>
                            <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['code_client']); ?></td>
                                <td><?php echo htmlspecialchars($client['nom']); ?></td>
                                <td><?php echo htmlspecialchars($client['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo htmlspecialchars($client['telephone'] ?? '-'); ?></td>
                                <td class="actions">
                                    <a href="modifier_client.php?id=<?php echo $client['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="liste_clients.php?supprimer=<?php echo $client['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');"><i class="fas fa-trash"></i> Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-message">
                                    <i class="fas fa-inbox"></i>
                                    <p>Aucun client trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="pagination-info">
                    <?php echo count($clients); ?> client(s) trouvé(s)
                </div>
            </div>
        </main>
    </div>
</body>
</html>
