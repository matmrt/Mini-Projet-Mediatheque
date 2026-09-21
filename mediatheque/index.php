<?php
// Inclusion de la connexion BDD et des éléments de mise en page
require_once 'config/db.php';
require_once 'includes/header.php';

// Récupération des statistiques pour le tableau de bord
try {
    // 1. Nombre total de livres
    $nb_livres = $pdo->query("SELECT COUNT(*) FROM LIVRE")->fetchColumn();

    // 2. Nombre total d'adhérents
    $nb_adherents = $pdo->query("SELECT COUNT(*) FROM ADHERENT")->fetchColumn();

    // 3. Nombre d'emprunts en cours (non restitués)
    $nb_emprunts_cours = $pdo->query("SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL")->fetchColumn();

    // 4. Nombre d'emprunts en retard
    $sql_retards = "SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL AND date_retour_prevue < CURRENT_DATE()";
    $nb_retards = $pdo->query($sql_retards)->fetchColumn();

} catch (PDOException $e) {
    $erreur = "Erreur lors du chargement du tableau de bord : " . $e->getMessage();
}
?>

<main>
    <div class="welcome-banner">
        <h2>Bienvenue sur l'application de Gestion de Médiathèque</h2>
        <p>Utilisez le menu de navigation pour consulter le catalogue, gérer les adhérents et effectuer les opérations d'emprunts et de retours.</p>
    </div>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <!-- Tableau de bord / Statistiques -->
    <section class="dashboard">
        <h3>Tableau de bord</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= (int)$nb_livres ?></div>
                <div class="stat-label">Livres enregistrés</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?= (int)$nb_adherents ?></div>
                <div class="stat-label">Adhérents inscrits</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?= (int)$nb_emprunts_cours ?></div>
                <div class="stat-label">Emprunts en cours</div>
            </div>

            <div class="stat-card <?= $nb_retards > 0 ? 'card-warning' : '' ?>">
                <div class="stat-number"><?= (int)$nb_retards ?></div>
                <div class="stat-label">Retards constatés</div>
            </div>
        </div>
    </section>

    <!-- Accès rapides -->
    <section class="quick-actions">
        <h3>Actions fréquentes</h3>
        <div class="actions-buttons">
            <a href="emprunter.php" class="btn-primary">+ Enregistrer un nouvel emprunt</a>
            <a href="emprunts.php" class="btn-secondary">Voir les emprunts en cours</a>
            <a href="livres.php" class="btn-secondary">Rechercher un livre</a>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>