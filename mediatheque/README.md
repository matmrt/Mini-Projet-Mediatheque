# Application Web — Gestion de Médiathèque

Mini-projet réalisé dans le cadre du BTS CIEL IR (Durée : 6 heures).

## Étudiant
* **Nom / Prénom** : Mathéo MARTHÉLY
* **Classe** : BTS CIEL IR

---

## Stack Technique
* **Langage Serveur** : PHP 8.x (sans framework)
* **SGBD** : MySQL / MariaDB via PDO (avec gestion des transactions)
* **Environnement** : XAMPP (Apache / MySQL)
* **Front-end** : HTML5, CSS3 personnalisé (Design System inspiré d'Apple / iOS)

---

## Fonctionnalités Réalisées (Cahier des charges)

| Code | Intitulé | État |
| :--- | :--- | :---: |
| **F01** | Page d'accueil & Tableau de bord global | Validé |
| **F02** | Catalogue complet des livres (titres, auteurs, disponibilités) | Validé |
| **F03** | Formulaire de recherche de livres par titre | Validé |
| **F04** | Consultation de la liste des adhérents | Validé |
| **F05** | Enregistrement d'un nouvel emprunt (sélection dynamique) | Validé |
| **F06** | Suivi des emprunts en cours | Validé |
| **F07** | Détection et mise en évidence des retards de restitution | Validé |
| **F08** | Enregistrement du retour d'un livre (mises à jour automatisées) | Validé |

---

## Instructions d'installation et de lancement

1. **Lancement de l'environnement** :
   - Démarrer les modules **Apache** et **MySQL** dans XAMPP Control Panel.

2. **Base de données** :
   - Ouvrir `phpMyAdmin` (`http://localhost/phpmyadmin`).
   - Sélectionner ou importer la base de données `mediatheque`.
   - Exécuter le script SQL fourni dans le dossier pour instancier les 6 tables (`ADHERENT`, `AUTEUR`, `CATEGORIE`, `LIVRE`, `LIVRE_AUTEUR`, `EMPRUNT`).

3. **Déploiement du code** :
   - Placer le dossier du projet dans le répertoire `htdocs` de XAMPP : `C:/xampp/htdocs/mediatheque/`.
   - Vérifier la configuration de connexion PDO dans `config/db.php`.

4. **Accès à l'application** :
   - Ouvrir le navigateur et se rendre à l'adresse : `http://localhost/mediatheque/`

---

## Arborescence du Projet

```text
mediatheque/
├── assets/
│   └── style.css            # Styles généraux (Design Apple Store)
├── config/
│   └── db.php               # Connexion PDO à la BDD MySQL
├── includes/
│   ├── header.php           # Navigation principale
│   └── footer.php           # Pied de page
├── adherents.php            # Liste des adhérents (F04)
├── emprunter.php            # Formulaire de nouvel emprunt (F05)
├── emprunts.php             # Gestion des emprunts & retards (F06, F07)
├── index.php                # Accueil & Statistiques (F01)
├── livres.php               # Catalogue & Recherche par titre (F02, F03)
├── retour.php               # Traitement du retour d'un livre (F08)
└── README.md                # Fichier de présentation du rendu