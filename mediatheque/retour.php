<?php
// Inclusion du fichier de configuration PDO
require_once 'config/db.php';

// Récupération et validation de l'ID d'emprunt
$id_emprunt = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_emprunt) {
    // Redirection si aucun identifiant valide n'est fourni
    header('Location: emprunts.php');
    exit();
}

try {
    // Début d'une transaction PDO pour la cohérence des opérations
    $pdo->beginTransaction();

    // 1. Récupérer l'ID du livre associé à cet emprunt (uniquement s'il n'est pas déjà rendu)
    $stmt_check = $pdo->prepare("SELECT id_livre FROM EMPRUNT WHERE id_emprunt = :id_emprunt AND date_retour IS NULL");
    $stmt_check->execute([':id_emprunt' => $id_emprunt]);
    $emprunt = $stmt_check->fetch();

    if ($emprunt) {
        $id_livre = $emprunt['id_livre'];

        // 2. Mettre à jour la date de retour dans la table EMPRUNT avec la date du jour
        $sql_update_emprunt = "UPDATE EMPRUNT 
                               SET date_retour = CURRENT_DATE() 
                               WHERE id_emprunt = :id_emprunt";
        $stmt_emprunt = $pdo->prepare($sql_update_emprunt);
        $stmt_emprunt->execute([':id_emprunt' => $id_emprunt]);

        // 3. Repasser le livre à disponible = TRUE (1) dans la table LIVRE
        $sql_update_livre = "UPDATE LIVRE 
                             SET disponible = 1 
                             WHERE id_livre = :id_livre";
        $stmt_livre = $pdo->prepare($sql_update_livre);
        $stmt_livre->execute([':id_livre' => $id_livre]);

        // Validation de toutes les opérations dans la base de données
        $pdo->commit();

        // Redirection vers la liste des emprunts en cours avec un message de confirmation
        header('Location: emprunts.php?retour_succes=1');
        exit();

    } else {
        // L'emprunt n'existe pas ou le livre a déjà été retourné
        $pdo->rollBack();
        header('Location: emprunts.php');
        exit();
    }

} catch (PDOException $e) {
    // Annulation des modifications en cas d'erreur
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erreur lors de l'enregistrement du retour : " . htmlspecialchars($e->getMessage()));
}