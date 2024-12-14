# Projet WordPress - Création d'un Site E-commerce

## **Introduction**
Ce projet vise à développer un site e-commerce en utilisant WordPress. Il exploite les capacités de cette plateforme pour offrir une expérience utilisateur fluide et intuitive. Grâce à l'intégration de plugins comme WooCommerce et à la personnalisation de thèmes, le site est conçu pour répondre aux exigences modernes du commerce en ligne.

---

## **Table des matières**
1. [Introduction](#introduction)
2. [Installation de WordPress](#installation-de-wordpress)
   - Installation de XAMPP
   - Mise en place de la base de données MySQL
   - Installation de WordPress
3. [Gestion des utilisateurs](#gestion-des-utilisateurs)
   - Ajout d'un utilisateur contributeur
   - Ajout d'un utilisateur éditeur
4. [Installation de Plugins](#installation-de-plugins)
   - WooCommerce
   - TI WooCommerce Wishlist
   - Contact Form 7
   - Elementor
5. [Fichier de base de données](#fichier-de-base-de-données)
   - Importation du fichier de base de données

---

## **Installation de WordPress**

### Étape 1 : Téléchargement et Installation de XAMPP
1. Rendez-vous sur le site officiel [Apache Friends](https://www.apachefriends.org/).
2. Téléchargez la version compatible avec votre système d'exploitation.
3. Installez XAMPP en suivant les instructions.
4. Activez les modules **Apache** et **MySQL**.

### Étape 2 : Mise en place de la base de données MySQL
1. Accédez à l'interface phpMyAdmin via [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Créez une base de données nommée `wordpress`.
3. Configurez les utilisateurs et attribuez les permissions nécessaires.

### Étape 3 : Téléchargement et Installation de WordPress
1. Téléchargez WordPress depuis le site officiel [WordPress.org](https://wordpress.org/).
2. Décompressez le fichier téléchargé dans le répertoire `htdocs` de XAMPP.
3. Configurez le fichier `wp-config.php` avec les informations de votre base de données.
4. Suivez l’assistant d’installation pour finaliser l’installation.

---

## **Gestion des utilisateurs**

### Ajout d’un utilisateur contributeur
1. Connectez-vous en tant qu’administrateur.
2. Accédez à la section **Users** > **Add New User**.
3. Remplissez les informations de l’utilisateur (nom, email, rôle, etc.).
4. Attribuez le rôle **Contributor**.

### Ajout d’un utilisateur éditeur
1. Connectez-vous à l’interface d’administration.
2. Ajoutez un nouvel utilisateur en suivant les mêmes étapes que précédemment.
3. Attribuez le rôle **Editor**, permettant de gérer et publier des contenus.

---

## **Installation de Plugins**

### WooCommerce
1. Accédez à **Plugins** > **Add New** dans l’administration WordPress.
2. Recherchez et installez **WooCommerce**.
3. Configurez les paramètres de votre boutique : modes de paiement, expédition, etc.

### TI WooCommerce Wishlist
1. Installez ce plugin pour permettre aux utilisateurs de créer des listes de souhaits.
2. Configurez les options en fonction des besoins du site.

### Contact Form 7
1. Ajoutez ce plugin pour créer facilement des formulaires de contact.
2. Personnalisez les champs et les messages.

### Elementor
1. Installez **Elementor** pour créer des pages avec une interface glisser-déposer.
2. Utilisez cet outil pour personnaliser des sections comme la page d’accueil ou le footer.

---

## **Fichier de base de données**

### Importation du fichier de base de données
Un fichier SQL est fourni avec ce projet pour simplifier la configuration initiale de votre base de données. Suivez les étapes ci-dessous pour importer ce fichier dans votre environnement :

1. **Accédez à phpMyAdmin :**
   - Ouvrez votre navigateur et rendez-vous sur [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
   
2. **Sélectionnez la base de données :**
   - Choisissez la base de données que vous avez créée pour le projet (par exemple, `wordpress`).

3. **Importez le fichier SQL :**
   - Cliquez sur l’onglet **Import**.
   - Cliquez sur **Choisir un fichier** et sélectionnez le fichier SQL fourni (`wordpress_db.sql`).
   - Cliquez sur **Exécuter** pour lancer l'importation.

4. **Vérifiez les données :**
   - Une fois l'import terminé, vous verrez les tables et les données dans la base de données.

> **Remarque :** Le fichier SQL contient les structures de tables et des exemples de données nécessaires pour le bon fonctionnement du site.

---

 

