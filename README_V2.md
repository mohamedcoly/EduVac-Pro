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
- Les documents candidats sont destinés à un stockage privé ; les contrôleurs de téléchargement et Policies restent à implémenter avant l'ouverture de l'upload.
- Ne jamais commiter .env, secrets, clés de paiement ou identifiants de production.
- Ne pas connecter cette branche à la base Supabase de production.

## Étape suivante
Exécuter l'installation et les migrations sur un environnement local, ajouter les tests d'authentification et d'autorisation, puis implémenter les Policies et le workflow de vérification des établissements et enseignants.


## Autorisations et modération
Les Policies des écoles, profils enseignants, offres et candidatures sont enregistrées dans AppServiceProvider. Les scopes de listing visibles sont disponibles sur School, JobOffer et TeacherProfile. Les routes administratives de validation sont documentées dans docs/EDUVAC_PRO_V2_SECURITY.md.

**Important :** le code de cette étape n'a pas encore été exécuté par PHPUnit ni validé sur MySQL. Ne pas déployer avant les tests et la revue des contrôleurs qui consommeront ces Policies et scopes.
