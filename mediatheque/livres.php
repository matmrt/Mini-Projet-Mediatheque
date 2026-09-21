<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Récupération des filtres depuis l'URL
$recherche = filter_input(INPUT_GET, 'recherche', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$isbn_search = filter_input(INPUT_GET, 'isbn', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$id_categorie = filter_input(INPUT_GET, 'categorie', FILTER_VALIDATE_INT) ?: null;
$annee = filter_input(INPUT_GET, 'annee', FILTER_VALIDATE_INT) ?: null;

try {
    // 1. Récupération des catégories pour le filtre HTML
    $stmtCat = $pdo->query("SELECT id_categorie, libelle FROM CATEGORIE ORDER BY libelle ASC");
    $categories = $stmtCat->fetchAll();

    // 2. Construction dynamique de la requête principale
    $sql = "SELECT 
                l.id_livre, l.titre, l.isbn, l.annee_publication, l.disponible, 
                c.libelle AS categorie, 
                GROUP_CONCAT(CONCAT(a.prenom, ' ', a.nom) SEPARATOR ', ') AS auteurs
            FROM LIVRE l
            JOIN CATEGORIE c ON l.id_categorie = c.id_categorie
            LEFT JOIN LIVRE_AUTEUR la ON l.id_livre = la.id_livre
            LEFT JOIN AUTEUR a ON la.id_auteur = a.id_auteur";

    $conditions = [];
    $params = [];

    if (!empty($recherche)) {
        $conditions[] = "l.titre LIKE :recherche";
        $params[':recherche'] = '%' . $recherche . '%';
    }

    if (!empty($isbn_search)) {
        $conditions[] = "l.isbn LIKE :isbn";
        $params[':isbn'] = '%' . $isbn_search . '%';
    }

    if ($id_categorie) {
        $conditions[] = "l.id_categorie = :id_categorie";
        $params[':id_categorie'] = $id_categorie;
    }

    if ($annee) {
        $conditions[] = "l.annee_publication = :annee";
        $params[':annee'] = $annee;
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY l.id_livre ORDER BY l.titre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $livres = $stmt->fetchAll();

} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération des livres : " . $e->getMessage();
}
?>

<main>
    <h2>Catalogue des Livres</h2>

    <!-- Formulaire de recherche et filtres -->
    <form action="livres.php" method="GET" style="margin-bottom: 24px; max-width: 100%;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="search" name="recherche" placeholder="Titre..." 
                   value="<?= htmlspecialchars($recherche) ?>" style="flex: 1; min-width: 160px;">

            <input type="search" name="isbn" placeholder="ISBN..." 
                   value="<?= htmlspecialchars($isbn_search) ?>" style="width: 150px;">

            <select name="categorie" style="padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_categorie'] ?>" <?= $id_categorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="annee" placeholder="Année (ex: 2023)" 
                   value="<?= $annee ? (int)$annee : '' ?>" style="width: 130px; padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);">

            <button type="submit" class="btn-primary">Filtrer</button>

            <?php if (!empty($recherche) || !empty($isbn_search) || $id_categorie || $annee): ?>
                <a href="livres.php" class="btn-secondary">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

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
        <p class="empty-msg">Aucun livre ne correspond à votre recherche.</p>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>