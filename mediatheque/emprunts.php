<?php
// Inclusion de la connexion BDD et des éléments de mise en page
require_once 'config/db.php';
require_once 'includes/header.php';

// Message de confirmation si un retour vient d'être effectué
$message_succes = "";
if (isset($_GET['retour_succes'])) {
    $message_succes = "Le retour du livre a bien été enregistré !";
}

try {
    // Requête pour récupérer les emprunts en cours (date_retour EST NULL)
    // avec jointures pour obtenir le nom de l'adhérent et le titre du livre
    $sql = "SELECT 
                e.id_emprunt, 
                e.date_emprunt, 
                e.date_retour_prevue,
                a.nom AS adherent_nom, 
                a.prenom AS adherent_prenom,
                l.titre AS livre_titre
            FROM EMPRUNT e
            JOIN ADHERENT a ON e.id_adherent = a.id_adherent
            JOIN LIVRE l ON e.id_livre = l.id_livre
            WHERE e.date_retour IS NULL
            ORDER BY e.date_retour_prevue ASC";
    
    $stmt = $pdo->query($sql);
    $emprunts = $stmt->fetchAll();

} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération des emprunts : " . $e->getMessage();
}

$date_aujourdhui = date('Y-m-d');
?>

<main>
    <h2>Emprunts en cours</h2>

    <?php if (!empty($message_succes)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if (count($emprunts) > 0): ?>
        <table class="table-data">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Adhérent</th>
                    <th>Livre emprunté</th>
                    <th>Date d'emprunt</th>
                    <th>Date retour prévue</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <?php 
                        // Vérification du retard : date prévue dépassée par rapport à aujourd'hui
                        $en_retard = ($emprunt['date_retour_prevue'] < $date_aujourdhui);
                    ?>
                    <tr class="<?= $en_retard ? 'row-retard' : '' ?>">
                        <td><?= (int)$emprunt['id_emprunt'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars(mb_strtoupper($emprunt['adherent_nom'])) ?></strong> 
                            <?= htmlspecialchars($emprunt['adherent_prenom']) ?>
                        </td>
                        <td><?= htmlspecialchars($emprunt['livre_titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($emprunt['date_emprunt'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($emprunt['date_retour_prevue'])) ?></td>
                        <td>
                            <?php if ($en_retard): ?>
                                <span class="badge badge-danger">EN RETARD</span>
                            <?php else: ?>
                                <span class="badge badge-info">En cours</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="retour.php?id=<?= (int)$emprunt['id_emprunt'] ?>" 
                               class="btn-action btn-retour"
                               onclick="return confirm('Confirmer le retour de ce livre ?');">
                                Enregistrer le retour
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-msg">Aucun emprunt en cours pour le moment.</p>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>