# EduVac Pro V2 — branche de refonte

Cette branche transforme le prototype en SaaS de recrutement de professeurs vacataires, avec un lancement pilote à Dakar.

## Documentation produit
- [Architecture cible](docs/EDUVAC_PRO_V2_ARCHITECTURE.md)
- [Cahier des charges fonctionnel](docs/EDUVAC_PRO_V2_PRODUCT_SPEC.md)
- [Plan de refonte](docs/EDUVAC_PRO_V2_REFACTOR_PLAN.md)

## Stack
PHP 8.3+, Laravel 12, Blade, MySQL 8, Spatie Laravel Permission. L'authentification initiale utilise le guard session Laravel et des contrôleurs Blade dédiés.

## Modules déjà préparés dans cette branche
- Fondations Laravel : bootstrap, routes, configuration de l'application, MySQL, sessions, mail, fichiers privés.
- Schéma de données : utilisateurs, établissements, membres, profils enseignants, matières, niveaux, CV/documents, disponibilités, offres, candidatures, entretiens, missions, évaluations, abonnements et audit.
- Rôles initiaux : teacher, school_manager, school_recruiter, platform_admin.
- Inscription distincte enseignant / établissement.
- Connexion, déconnexion, limitation des tentatives, vérification d'adresse e-mail et réinitialisation de mot de passe.
- Création transactionnelle des profils et établissements ; les établissements sont créés avec le statut pending.
- Redirection vers un espace enseignant, établissement ou administration selon le rôle.

## État de validation
Les fichiers ont été ajoutés à GitHub, mais aucun environnement PHP/Composer/MySQL n'a été exécuté dans cette session. **Le code n'est donc pas encore certifié par composer install, php artisan migrate ou les tests.** Il faut effectuer cette validation en local avant de considérer le module prêt pour la production.

## Mise en place locale
Le dépôt est une branche de refonte qui doit être fusionnée avec un squelette Laravel 12 complet. Créer d'abord un projet Laravel propre, puis intégrer les fichiers de cette branche en fusionnant les fichiers standard au lieu de les écraser aveuglément :

    composer create-project laravel/laravel eduvac-pro "^12.0"
    cd eduvac-pro
    composer require spatie/laravel-permission
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

Copier ensuite les fichiers de cette branche. Vérifier et fusionner en particulier composer.json, bootstrap/app.php, routes/web.php, config/* et les migrations. Ne pas conserver deux migrations créant les mêmes tables users ou password_reset_tokens.

Puis :

    cp .env.example .env
    php artisan key:generate
    # Créer la base eduvac_pro et configurer DB_* dans .env
    composer install
    php artisan migrate --seed
    php artisan route:list
    php artisan serve

Pour le développement, utiliser MAIL_MAILER=log : les liens de vérification et de réinitialisation sont écrits dans les logs Laravel. En production, configurer un vrai fournisseur SMTP, HTTPS, un disque privé persistant et les sauvegardes.

## Sécurité
- Le rôle est assigné côté serveur à partir du choix de parcours ; aucun rôle administrateur n'est proposé à l'inscription.
- Mots de passe hashés ; aucun mot de passe ni token Supabase n'est migré.
- Les inscriptions d'établissement créent un établissement pending, qui ne doit pas publier avant validation.
- Les CV et justificatifs sont stockés sur le disque privé ; le téléchargement est protégé par TeacherDocumentPolicy et une route authentifiée.
- Ne jamais commiter .env, secrets, clés de paiement ou identifiants de production.
- Ne pas connecter cette branche à la base Supabase de production.

## Étape suivante
Exécuter l'installation et les migrations sur un environnement local, lancer PHPUnit, corriger les éventuels échecs et effectuer une recette fonctionnelle avec des comptes enseignant, établissement et administration.


## Autorisations et modération
Les Policies des écoles, profils enseignants, offres et candidatures sont enregistrées dans AppServiceProvider. Les scopes de listing visibles sont disponibles sur School, JobOffer et TeacherProfile. Les routes administratives de validation sont documentées dans docs/EDUVAC_PRO_V2_SECURITY.md.

**Important :** les Policies et contrôleurs sont enregistrés dans cette branche, mais ils n'ont pas encore été exécutés par PHPUnit ni validés sur MySQL. Ne pas déployer avant les tests et la revue fonctionnelle.


## Documents candidats et tests de sécurité
- Les CV et justificatifs utilisent le disque Laravel privé nommé private.
- Le téléchargement passe par la route teacher-documents.download et la TeacherDocumentPolicy ; aucun lien public direct n'est prévu.
- Des tests PHPUnit couvrent l'isolation des établissements, les rôles locaux owner/recruiter et le blocage de publication avant validation.
- Configuration PHPUnit : SQLite en mémoire pour isoler les tests.

Les tests sont ajoutés mais **n'ont pas encore été exécutés** dans un environnement PHP/Composer. La prochaine validation doit lancer composer install puis php artisan test et corriger tous les échecs avant de poursuivre.


## Moteur de matching enseignant
- Classement des profils vérifiés et disponibles sur 100 points : matière (30), niveau (25), localisation (20), disponibilité déclarée (15), expérience (10).
- Écran de matching accessible depuis chaque offre dans l'espace établissement.
- Les critères et limites du score sont documentés dans [EDUVAC_PRO_V2_MATCHING.md](docs/EDUVAC_PRO_V2_MATCHING.md).
- Tests ciblés ajoutés dans tests/Feature/TeacherMatchingTest.php ; ils doivent encore être exécutés dans un environnement Laravel.


## Entretiens
Le workflow de planification et de réponse aux entretiens est décrit dans [EDUVAC_PRO_V2_INTERVIEWS.md](docs/EDUVAC_PRO_V2_INTERVIEWS.md). Une migration ajoute le suivi de la réponse de l’enseignant. Tests ciblés dans `tests/Feature/InterviewWorkflowTest.php` (à exécuter localement).


## Propositions de mission
Le fonctionnement des propositions et de l’accord de l’enseignant est détaillé dans [EDUVAC_PRO_V2_MISSIONS.md](docs/EDUVAC_PRO_V2_MISSIONS.md). Tests ciblés dans `tests/Feature/MissionWorkflowTest.php` (à exécuter localement).

## Module Notifications & communication

Le centre de notifications internes est maintenant intégré à la branche :
- notifications persistées via la table Laravel `notifications` ;
- alertes lors d'une nouvelle candidature, d'un changement de statut, d'un entretien planifié/annulé et d'une réponse à une mission ;
- consultation paginée, marquage individuel et marquage global comme lu ;
- compteur de notifications non lues sur les espaces enseignant, établissement et administration ;
- tests Feature ajoutés pour l'accès à la boîte, le marquage comme lu et l'isolation entre utilisateurs.

Les notifications sont actuellement **in-app uniquement**. L'e-mail transactionnel et WhatsApp restent des étapes ultérieures. Les tests PHP/Laravel et la migration doivent être exécutés dans l'environnement local avant validation.


## Messagerie interne (module ajouté)
- Une conversation est associée à une candidature et relie le profil enseignant aux membres de l'établissement concerné.
- Création/réouverture depuis la fiche candidature ; liste des conversations et historique des messages.
- Messages validés côté serveur (texte, limite 5 000 caractères), envoi protégé par authentification, vérification de la participation et limitation de débit.
- Indicateur de messages non lus, mise à jour de lecture à l'ouverture et notifications in-app au destinataire.
- Tables : `conversations`, `conversation_participants`, `messages`. Les conversations ne sont visibles que par leurs participants.
- Test Feature `tests/Feature/MessagingAccessTest.php` : authentification, accès à la boîte et refus d'accès sans participation.

**À valider localement :** la branche est modifiée dans GitHub, mais les tests et migrations n'ont pas été exécutés dans cet environnement. Après intégration au squelette Laravel 12, lancer `php artisan migrate` puis `php artisan test` et contrôler les flux avec un compte enseignant et un compte établissement.
