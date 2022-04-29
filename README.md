# Blog Symfony-Twig

Une application réalisé avec le framework Symfony, qui permet de consulter des articles d'un blog.
L'administrateur peut ajouter, modifier, et supprimer des articles et des catégories.
L'utilisateur peu consulter les articles soit en résumé soit dans leurs totalités.


### Prérequi

*Prérequis sur votre machine pour le bon fonctionnement de ce projet :
- PHP Version 7.4.11
- MySQL
- Symfony version 5.4 minimum avec le CLI(Binaire) Symfony
- Composer

### Installation

Après avoir cloné le projet avec ``git clone https://github.com/JulieDrouin/Blog-Symfony.git``

Exécutez la commande ``cd app-blog-symf`` pour vous rendre dans le dossier depuis le terminal.

Ensuite, dans l'ordre taper les commandes dans votre terminal :

- 1 ``composer install`` afin d'installer toutes les dépendances composer du projet.

- 2 ``composer prepare``      afin d'exécuter les scripts suivants
     [
        "php bin/console doctrine:database:drop --if-exists --force",       Supprimer la base de donnée MySQL si elle existe
        "php bin/console doctrine:database:create",                         Installer la base de donnée MySQL
        "php bin/console make:migration",                                   Exécuter la création des entitées, fichier de version des entitées
        "php bin/console doctrine:migrations:migrate",                      Exécuter la migration en base de donnée
        "php bin/console doctrine:fixtures:load -n"                         Exécuter les dataFixtures
     ]

- 3 Installer le serveur sous https ENABLING, TLS autorité de certification🔒 avec la commande suivante ``symfony server:ca:install``

- 4 Vous pouvez maintenant demarré votre serveur via la commande suivante : ``symfony serve``

- 5 Vous pouvez maintenant accéder à votre blog en vous connectant au serveur : ``https://localhost:8000``


### Admin -Login

Par défaut, le login et le mot de passe sont :

Nom d’utilisateur : ``admin@gmail.com``
Mot de Passe : ``AdminAdmin``

Cette espace est reservé à l'admin, afin de lui permettre de créer , modifier, ou supprimer des articles et catégorie, via l'url : https://localhost:8000/admin/dashboard/post ou https://localhost:8000/admin/dashboard/category.

Accessible également via la navbar => Dashboard