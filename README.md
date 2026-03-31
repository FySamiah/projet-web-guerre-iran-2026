# projet-web-guerre-iran-2026

- Docker Desktop installé et démarré

## Démarrage
1. Extraire le ZIP dans un dossier
2. Ouvrir un terminal dans ce dossier
3. Exécuter :

   docker-compose up -d

4. Attendre 20 secondes (initialisation MySQL)
5. Ouvrir le navigateur :

   Front-office : http://localhost:8080
   Back-office  : http://localhost:8080/login.php

## Identifiants Back-office
   Email    : admin@site.com
   Password : password

## Réinitialiser la BDD (si besoin)
   docker-compose down -v
   docker-compose up -d