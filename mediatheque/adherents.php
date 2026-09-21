<?php
// Inclusion de la connexion BDD et des éléments de mise en page
require_once 'config/db.php';
require_once 'includes/header.php';

try {
    // Requête pour récupérer la liste de tous les adhérents
    $sql = "SELECT id_adherent, nom, prenom, email, date_inscription 
            FROM ADHERENT 
            ORDER BY nom ASC, prenom ASC";
    
    $stmt = $pdo->query($sql);
    $adherents = $stmt->fetchAll();

} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération des adhérents : " . $e->getMessage();
}
?>

<main>
    <h2>Liste des Adhérents</h2>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if (count($adherents) > 0): ?>
        <table class="table-data">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse E-mail</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adherents as $adherent): ?>
                    <tr>
                        <td><?= (int)$adherent['id_adherent'] ?></td>
                        <td><strong><?= htmlspecialchars(mb_strtoupper($adherent['nom'])) ?></strong></td>
                        <td><?= htmlspecialchars($adherent['prenom']) ?></td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($adherent['email']) ?>">
                                <?= htmlspecialchars($adherent['email']) ?>
                            </a>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($adherent['date_inscription'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-msg">Aucun adhérent enregistré pour le moment.</p>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>