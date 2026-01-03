# Mediatekformation
## Présentation
La partie back-office du site, développé avec Symfony 6.4, permet d'administrer les formations, les playlists et les catégories présentent sur la partie front-office du site.<br>
Le readme de la partie front-office est disponible à l'adresse suivante (dans le README) : https://github.com/CNED-SLAM/mediatekformation<br>
## Les différentes pages
Voici les 10 pages du site.
### Page 1 : l'authentification
Cette page est accessible en ajoutant "/login" dans l'URL du site et présente un formulaire pour accéder à la partie front-office du site.<br>
Le formulaire attend de recevoir un "Login" et un "Mot de passe" valide dans leurs champs respectifs et un bouton "Se connecter" permet de se connecter.<br>

![img1](https://github.com/user-attachments/assets/32e46ac6-a366-484c-94d1-4c79ce0c4c50)
### Page 2 : l'accueil
Cette page présente la page d'accueil (qui pour l'instant fait surtout acte de présence pour des ajouts futurs).<br>
La partie du haut contient une bannière (logo, nom et phrase présentant le but du site), un menu permettant d'accéder aux 4 pages principales (Accueil, Formations, Playlists, Catégories) et un bouton de déconnexion.<br>

![img2](https://github.com/user-attachments/assets/0f76f73a-56ce-43c5-98a8-5396f378d358)
### Page 3 : les formations
Cette page présente les formations proposées en ligne (accessibles sur YouTube).<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
La partie centrale contient un tableau composé de 5 colonnes :<br>

•	La 1ère colonne ("formation") contient le titre de chaque formation.<br>
•	La 2ème colonne ("playlist") contient le nom de la playlist dans laquelle chaque formation se trouve.<br>
•	La 3ème colonne ("catégories") contient la ou les catégories concernées par chaque formation (langage…).<br>
•	La 4ème colonne ("date") contient la date de parution de chaque formation.<br>
•	La 5ème colonne contient la miniature visible sur YouTube, ainsi qu'un bouton "Modifier" et "Supprimer" pour chaque formation.<br>

Au niveau de la colonne "formation", un bouton "ajouter une formation" permet d'ajouter une nouvelle formation.<br>
Au niveau des colonnes "formation", "playlist" et "date", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">").<br>
Au niveau des colonnes "formation" et "playlist", il est possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br> 
Au niveau de la catégorie, la sélection d'une catégorie dans le combo permet d'afficher uniquement les formations qui ont cette catégorie. Le fait de sélectionner la ligne vide du combo permet d'afficher à nouveau toutes les formations.<br>
Par défaut la liste est triée sur la date par ordre décroissant (la formation la plus récente en premier).<br>
Le fait de cliquer sur une miniature permet d'accéder à la page contenant le détail de la formation.<br>

![img3](https://github.com/user-attachments/assets/72a2cc6a-e57f-4eb7-903c-0d91b533efb9)
### Page 4 : détail d'une formation
Cette page n'est pas accessible par le menu mais uniquement en cliquant sur une miniature dans la page "Formations".<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
La partie centrale est séparée en 2 parties :<br>

•	La partie gauche contient la vidéo qui peut être directement visible dans le site ou sur YouTube.<br>
•	La partie droite contient la date de parution, le titre de la formation, le nom de la playlist, la liste des catégories et sa description détaillée.<br>

![img4](https://github.com/user-attachments/assets/e095045c-ccf5-4965-a021-ed8d77b4ed94)
### Page 5 : ajout d'une formation
Cette page est accessible en cliquant sur le bouton "ajouter une formation" présent en dessous du filtre des formations.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
Un formulaire est affiché, demandant plusieurs informations :<br>

• Le Titre de la formation.<br>
• La Description.<br>
• La Playlist rattachée.<br>
• La ou Les Catégorie(s).<br>
• La Date de publication (qui ne peut pas être antérieure à la date du jour).<br>
• Le Lien de la vidéo sur YouTube.<br>

À noter que seuls le titre et la playlist sont obligatoires pour enregistrer une nouvelle formation.<br>
En fin de formulaire se trouve un bouton pour "Enregistrer" ou "Annuler" l'ajout d'une nouvelle formation.<br>

![img5](https://github.com/user-attachments/assets/6add3c6f-0ac4-4e75-9eae-8901a06d4484)
### Page 6 : modification d'une formation
Cette page est accessible en cliquant sur le bouton "Modifier" présent en dessous de la miniature des formations.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
Un formulaire est affiché, reprenant les informations de la formation sélectionnée :<br>

• Le Titre de la formation.<br>
• La Description.<br>
• La Playlist rattachée.<br>
• La ou Les Catégorie(s).<br>
• La Date de publication (qui après modification ne peut pas être antérieure à la date du jour).<br>
• Le Lien de la vidéo sur YouTube (avec cette fois-ci un aperçu).<br>

En fin de formulaire se trouve un bouton pour "Enregistrer" ou "Annuler" la modification d'une formation.<br>

![img6](https://github.com/user-attachments/assets/8149f887-6643-42c8-9a25-f5f66d2f2c2e)
### Pop-Up 1 : suppression d'une formation
Cette pop-up apparaît en cliquant sur le bouton "Supprimer" présent en dessous de la miniature des formations.<br>
Une pop-up est affichée et demande une confirmation de la suppression, avec un bouton "Annuler" pour annuler la suppression et un bouton "OK" pour confirmer la suppression.<br>

![popup1](https://github.com/user-attachments/assets/104337cb-307d-4885-ac48-39548f57c89a)
### Page 7 : les playlists
Cette page présente les playlists.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
La partie centrale contient un tableau composé de 4 colonnes :<br>

•	La 1ère colonne ("playlist") contient le nom de chaque playlist.<br>
• La 2ème colonne ("formations") contient le nombre de formations présentes dans chaque playlist.<br>
•	La 3ème colonne ("catégories") contient la ou les catégories concernées par chaque playlist (langage…).<br>
•	La 4ème colonne contient deux boutons permettant de "Modifier" ou "Supprimer" une playlist.<br>

Au niveau de la colonne "playlist", un bouton "ajouter une playlist" permet d'ajouter une nouvelle playlist.<br>
Au niveau de la colonne "playlist", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">"). Il est aussi possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br> 
Au niveau de la catégorie, la sélection d'une catégorie dans le combo permet d'afficher uniquement les playlists qui ont cette catégorie. Le fait de sélectionner la ligne vide du combo permet d'afficher à nouveau toutes les playlists.<br>
Par défaut la liste est triée sur le nom de la playlist.<br>

![img7](https://github.com/user-attachments/assets/931cf4a3-e375-42ce-8369-c8d5038e8ff9)
### Page 8 : ajout d'une playlist
Cette page est accessible en cliquant sur le bouton "ajouter une playlist" présent en dessous du filtre des playlists.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
Un formulaire est affiché, demandant plusieurs informations :<br>

• Le Nom de la playlist.<br>
• La Description.<br>

À noter que seul le nom est obligatoire pour enregistrer une nouvelle playlist.<br>
En fin de formulaire se trouve un bouton pour "Enregistrer" ou "Annuler" l'ajout d'une nouvelle playlist.<br>

![img8](https://github.com/user-attachments/assets/922fb507-790d-4606-bd2c-7e4af8c3877f)
### Page 9 : modification d'une playlist
Cette page est accessible en cliquant sur le bouton "Modifier" présent à gauche dans la 4ème colonne.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
Un formulaire est affiché, reprenant les informations de la playlist sélectionnée :<br>

• Le Nom de la playlist.<br>
• La Description.<br>
• La ou Les formation(s) rattachée(s) (qui ne sont pas modifiable).<br>

En fin de formulaire se trouve un bouton pour "Enregistrer" ou "Annuler" la modification d'une nouvelle playlist.<br>

![img9](https://github.com/user-attachments/assets/794fd342-d437-4308-9718-5c78ae49de6d)
### Pop-up 2 : suppression d'une playlist
Cette pop-up apparaît en cliquant sur le bouton "Supprimer" présent à droite dans la 4ème colonne.<br>
Une pop-up est affichée et demande une confirmation de la suppression, avec un bouton "Annuler" pour annuler la suppression et un bouton "OK" pour confirmer la suppression.<br>

![popup2](https://github.com/user-attachments/assets/943701ed-210f-4b8a-8b98-8c20470d8c1f)
### Page 10 : les catégories
Cette page présente les catégories.<br>
La partie haute est identique à la page d'accueil (bannière, menu et bouton de déconnexion).<br>
La partie centrale contient un tableau composé de 2 colonne :<br>

• La 1ère colonne contient le nom des différentes catégories.<br>
• La 2ème colonne contient un bouton permettant de "Supprimer" une catégorie

La partie du bas contient un formulaire intégré dans la page qui permet d'enregistrer une nouvelle catégorie (si elle n'existe pas déjà), en cliquant sur le bouton "Enregistrer".<br>

![img10](https://github.com/user-attachments/assets/18736963-2cf8-49df-ac5a-c64b21e487a1)
### Pop-up 3 : suppression d'une catégorie
Cette pop-up apparaît en cliquant sur le bouton "Supprimer" présent dans la 2ème colonne.<br>
Une pop-up est affichée et demande une confirmation de la suppression, avec un bouton "Annuler" pour annuler la suppression et un bouton "OK" pour confirmer la suppression.<br>
À noter qu'il est impossible de supprimer une catégorie si elle contient des formations.<br>

![popup3](https://github.com/user-attachments/assets/4cef8110-ddb6-46f9-9e24-29ff36293f6c)
### Pop-up 4 : déconnexion
Cette pop-up apparaît en cliquant sur le bouton "Déconnexion" présent tout à droite du menu de la partie haute.<br>
Une pop-up est affichée et demande une confirmation de la déconnexion, avec un bouton "Annuler" pour annuler la déconnexion et un bouton "OK" pour confirmer la déconnexion.<br>
La déconnexion entraîne un retour sur la page d'accueil de la partie front-office du site.<br>

![popup4](https://github.com/user-attachments/assets/927209dc-2f79-4ded-bd49-79f1a8ee8546)
## Test de l'application en local
- Vérifier que Composer, Git et Wamserver (ou équivalent) sont installés sur l'ordinateur.
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent) puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode admin, se positionner dans le dossier du projet et taper "composer install" pour reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL en root sans mot de passe et créer la BDD 'mediatekformation'.<br>
- Récupérer le fichier mediatekformation.sql en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- De préférence, ouvrir l'application dans un IDE professionnel. L'adresse pour la lancer est : http://localhost/mediatekformation/public/index.php<br>
