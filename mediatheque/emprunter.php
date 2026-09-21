<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$message_succes = "";
$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_adherent = filter_input(INPUT_POST, 'id_adherent', FILTER_VALIDATE_INT);
    $id_livre = filter_input(INPUT_POST, 'id_livre', FILTER_VALIDATE_INT);

    if ($id_adherent && $id_livre) {
        try {
            $pdo->beginTransaction();

            $date_emprunt = date('Y-m-d');
            $date_retour_prevue = date('Y-m-d', strtotime('+14 days'));

            $sql_insert = "INSERT INTO EMPRUNT (id_adherent, id_livre, date_emprunt, date_retour_prevue) 
                           VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([
                ':id_adherent' => $id_adherent,
                ':id_livre' => $id_livre,
                ':date_emprunt' => $date_emprunt,
                ':date_retour_prevue' => $date_retour_prevue
            ]);

            $sql_update = "UPDATE LIVRE SET disponible = 0 WHERE id_livre = :id_livre AND disponible = 1";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([':id_livre' => $id_livre]);

            if ($stmt_update->rowCount() > 0) {
                $pdo->commit();
                $message_succes = "L'emprunt a été enregistré avec succès ! Date de retour prévue : " . date('d/m/Y', strtotime($date_retour_prevue));
            } else {
                $pdo->rollBack();
                $message_erreur = "Erreur : Le livre sélectionné n'est plus disponible.";
            }

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message_erreur = "Une erreur est survenue lors de l'enregistrement : " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message_erreur = "Veuillez sélectionner un adhérent et un livre valides.";
    }
}

$query_adherents = $pdo->query("SELECT id_adherent, nom, prenom FROM ADHERENT ORDER BY nom, prenom");
$adherents = $query_adherents->fetchAll();

$query_livres = $pdo->query("SELECT id_livre, titre, isbn FROM LIVRE WHERE disponible = 1 ORDER BY titre");
$livres_disponibles = $query_livres->fetchAll();
?>

<main class="container">
    <h2>Enregistrer un nouvel emprunt</h2>

    <?php if (!empty($message_succes)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message_succes) ?></div>
    <?php endif; ?>

    <?php if (!empty($message_erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message_erreur) ?></div>
    <?php endif; ?>

    <form action="emprunter.php" method="POST" class="form-emprunt">

        <div class="form-group">
            <label for="id_adherent">Adhérent :</label>
            <select name="id_adherent" id="id_adherent" required>
                <option value="">-- Sélectionner un adhérent --</option>
                <?php foreach ($adherents as $adherent): ?>
                    <option value="<?= (int)$adherent['id_adherent'] ?>">
                        <?= htmlspecialchars($adherent['nom'] . ' ' . $adherent['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_livre">Livre disponible :</label>
            <select name="id_livre" id="id_livre" required>
                <option value="">-- Sélectionner un livre --</option>
                <?php if (count($livres_disponibles) > 0): ?>
                    <?php foreach ($livres_disponibles as $livre): ?>
                        <option value="<?= (int)$livre['id_livre'] ?>">
                            <?= htmlspecialchars($livre['titre']) ?> (ISBN: <?= htmlspecialchars($livre['isbn']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>Aucun livre disponible actuellement</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-info">
            <p><strong>Date d'emprunt :</strong> <?= date('d/m/Y') ?></p>
            <p><strong>Date de retour prévue (J+14) :</strong> <?= date('d/m/Y', strtotime('+14 days')) ?></p>
        </div>

        <button type="submit" class="btn-submit">Valider l'emprunt</button>
    </form>
</main>

<?php require_once 'includes/footer.php'; ?>