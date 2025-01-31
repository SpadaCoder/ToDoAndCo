# Guide de contribution et processus de qualité

## 1. Introduction

Ce document décrit les règles et les bonnes pratiques que tout développeur doit suivre pour contribuer au projet. Il inclut les procédures de développement, les conventions de codage, le processus de revue de code et les tests à réaliser avant toute mise en production.

---

## 2. Configuration du projet

### 2.1. Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- PHP 8.x
- Symfony CLI
- Composer
- Un serveur local (ex : Laragon, Docker)
- Un accès à la base de données du projet

### 2.2. Installation du projet

1. Cloner le dépôt Git :
   ```sh
   git clone https://github.com/SpadaCoder/ToDoAndCo
   ```
2. Se placer dans le dossier du projet :
   ```sh
   cd ToDoAndCo
   ```
3. Installer les dépendances PHP :
   ```sh
   composer install
   ```
4. Configurer l'environnement avec vos informations.
5. Mettre en place la base de données :
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
6. Lancer le serveur de développement :
   ```sh
   symfony server:start
   ```

---

## 3. Workflow Git

### 3.1. Règles Git

- **Branche principale** : `main` (protégée, interdiction de commit direct)
- **Branche de développement** : `dev`
- **Branches de correction** : `fix/nom-du-fix`

### 3.2. Procédure de développement

1. Créer une branche pour toute nouvelle fonctionnalité ou correction de bug :
   ```sh
   git checkout dev
   git pull origin dev
   git checkout -b fix/nom-du-fix
   ```
2. Développer la fonctionnalité en respectant les conventions de code.
3. Vérifier et tester le code en local.
4. Commiter en respectant le format :
   ```sh
   git commit -m "fix: erreur de route"
   ```
5. Pousser la branche et ouvrir une pull request (PR) vers `dev`.
6. Faire une revue de code avant la fusion.

---

## 4. Conventions de codage

### 4.1. PHP & Symfony

- Respecter les standards **PSR-12**.
- Nommage des classes en **CamelCase**.
- Variables et fonctions en **camelCase**.
- Ne pas laisser de **code mort** ou de `var_dump()`.
- Utiliser des **services** pour la logique plutôt que les contrôleurs.
- Passer par le **gestionnaire d'entité (EntityManager)** pour manipuler la base de données.

### 4.2. Twig & Frontend

- Respecter l'**indentation** et la **syntaxe propre**.
- Utiliser des fichiers **CSS et JavaScript distincts**.
- Éviter le **code inline** dans les templates.

### 4.3. Documentation

- Ajouter des **commentaires PHPDoc** sur les classes et fonctions.

---

## 5. Processus de qualité

### 5.1. Tests unitaires & fonctionnels

- Écrire des tests avec **PHPUnit** pour chaque nouvelle fonctionnalité.
- Lancer les tests avant chaque commit majeur :
  ```sh
  php bin/phpunit
  ```

### 5.2. Code Review

- La **PR** doit contenir une **description claire** des modifications.
- Aucun **merge** ne doit être fait sans **validation**.

---

## 6. Déploiement

### 6.1. Environnement de production

- **Ne jamais pousser directement sur **``.
- Utiliser des **migrations Doctrine** pour toute modification de la base de données.
- Purger le **cache Symfony** après déploiement :
  ```sh
  php bin/console cache:clear
  ```

---

## 7. Conclusion

Ce guide doit être suivi par **tous les développeurs** pour assurer la **cohérence et la qualité** du projet. Toute déviation devra être discutée en équipe avant mise en place.

**Bon développement !** 

