<?php
// Inclusion du fichier de connexion BDD et de l'en-tête
require_once 'config/db.php';
require_once 'includes/header.php';

// Gestion de la recherche par titre (F03)
$recherche = filter_input(INPUT_GET, 'recherche', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

try {
    // Requête SQL avec jointures et groupement des auteurs
    $sql = "SELECT 
                l.id_livre, l.titre, l.isbn, l.annee_publication, l.disponible, 
                c.libelle AS categorie, 
                GROUP_CONCAT(CONCAT(a.prenom, ' ', a.nom) SEPARATOR ', ') AS auteurs
            FROM LIVRE l
            JOIN CATEGORIE c ON l.id_categorie = c.id_categorie
            LEFT JOIN LIVRE_AUTEUR la ON l.id_livre = la.id_livre
            LEFT JOIN AUTEUR a ON la.id_auteur = a.id_auteur";

    // Si une recherche est effectuée, on filtre par titre
    if (!empty($recherche)) {
        $sql .= " WHERE l.titre LIKE :recherche";
    }

    $sql .= " GROUP BY l.id_livre ORDER BY l.titre ASC";

    $stmt = $pdo->prepare($sql);

    if (!empty($recherche)) {
        $stmt->execute([':recherche' => '%' . $recherche . '%']);
    } else {
        $stmt->execute();
    }

    $livres = $stmt->fetchAll();

} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération des livres : " . $e->getMessage();
}
?>

<main>
    <h2>Catalogue des Livres</h2>

    <!-- Formulaire de recherche par titre (F03) -->
    <form action="livres.php" method="GET" style="margin-bottom: 24px; max-width: 100%;">
        <div style="display: flex; gap: 10px;">
            <input type="search" name="recherche" placeholder="Rechercher un livre par titre..." 
                   value="<?= htmlspecialchars($recherche) ?>">
            <button type="submit" class="btn-primary">Rechercher</button>
            <?php if (!empty($recherche)): ?>
                <a href="livres.php" class="btn-secondary">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <!-- Affichage des résultats -->
    <?php if (count($livres) > 0): ?>
        <table class="table-data">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur(s)</th>
                    <th>Catégorie</th>
                    <th>Année</th>
                    <th>ISBN</th>
                    <th>Disponibilité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($livre['titre']) ?></strong></td>
                        <td><?= htmlspecialchars($livre['auteurs'] ?? 'Auteur inconnu') ?></td>
                        <td><?= htmlspecialchars($livre['categorie']) ?></td>
                        <td><?= (int)$livre['annee_publication'] ?></td>
                        <td><code><?= htmlspecialchars($livre['isbn']) ?></code></td>
                        <td>
                            <?php if ($livre['disponible']): ?>
                                <span class="badge badge-info">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Emprunté</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-msg">Aucun livre trouvé.</p>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>