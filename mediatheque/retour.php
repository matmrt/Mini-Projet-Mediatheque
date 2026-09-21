<?php
require_once 'config/db.php';

$id_emprunt = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_emprunt) {
    header('Location: emprunts.php');
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt_check = $pdo->prepare("SELECT id_livre FROM EMPRUNT WHERE id_emprunt = :id_emprunt AND date_retour IS NULL");
    $stmt_check->execute([':id_emprunt' => $id_emprunt]);
    $emprunt = $stmt_check->fetch();

    if ($emprunt) {
        $id_livre = $emprunt['id_livre'];

        $sql_update_emprunt = "UPDATE EMPRUNT 
                               SET date_retour = CURRENT_DATE() 
                               WHERE id_emprunt = :id_emprunt";
        $stmt_emprunt = $pdo->prepare($sql_update_emprunt);
        $stmt_emprunt->execute([':id_emprunt' => $id_emprunt]);

        $sql_update_livre = "UPDATE LIVRE 
                             SET disponible = 1 
                             WHERE id_livre = :id_livre";
        $stmt_livre = $pdo->prepare($sql_update_livre);
        $stmt_livre->execute([':id_livre' => $id_livre]);

        $pdo->commit();

        header('Location: emprunts.php?retour_succes=1');
        exit();

    } else {
        $pdo->rollBack();
        header('Location: emprunts.php');
        exit();
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erreur lors de l'enregistrement du retour : " . htmlspecialchars($e->getMessage()));
}