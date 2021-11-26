# PHP - P6 Openclassrooms - Développez de A à Z le site communautaire SnowTricks

## Installation :

-   **_Étape 1 :_** Cloner le projet
-   **_Étape 2 :_** Exécuter la commande `composer install` à la racine du projet
-   **_Étape 3 :_** Créer un fichier .env.local à la racine de votre projet avec vos propres identifiants en prenant comme modèle le .env présent
-   **_Étape 4 :_** Exécuter les commandes suivantes pour installer la base de donnée et les fixtures :

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

`php bin/console doctrine:fixtures:load -n`

[Lien vers le code climate du projet](https://codeclimate.com/github/Frnalex/snowtricks)
