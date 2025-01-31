# OpenClassrooms - Projet 8 : Améliorez une application existante de ToDo & Co

## Présentation

Dépôt Git de [ToDoAndCo](https://github.com/SpadaCoder/ToDoAndCo).

Ce projet est le huitième projet de la formation Développeur d'application - PHP/Symfony d'OpenClassrooms.


## Configuration conseillée

Le projet a été développé sur un serveur local avec les versions suivantes :

> - Apache 2.4.54
> - PHP 8.1.10
> - Symfony 6.4.17
> - [MySQL](https://www.mysql.com/fr/) 8.0.30
> - [Composer](https://getcomposer.org/) 2.8.3
> - [Node.js](https://nodejs.org/en/) 18.8.0

## Installation

- Cloner le dépôt Git

```bash
git clone https://github.com/SpadaCoder/ToDoAndCo
```

- Dans le dossier cloné (`ToDoAndCo`), copier le fichier **.env** et le renommer en **.env.local**

```bash
cd ToDoAndCo
cp .env .env.local
```

- Configurer les variables d'environnement dans le fichier **.env.local**

- Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Charger les fixtures

- Lancer la commande suivante :

```bash
php bin/console doctrine:fixtures:load
```