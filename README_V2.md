# EduVac Pro V2 — branche de refonte

Cette branche contient le cadrage produit V2 et les premiers fichiers de fondation Laravel pour le SaaS de recrutement de professeurs vacataires au Sénégal.

## Documentation
- [Architecture cible](docs/EDUVAC_PRO_V2_ARCHITECTURE.md)
- [Cahier des charges fonctionnel](docs/EDUVAC_PRO_V2_PRODUCT_SPEC.md)
- [Plan de refonte et critères d'acceptation](docs/EDUVAC_PRO_V2_REFACTOR_PLAN.md)

## Stack
PHP 8.3+, Laravel 12, Blade, Bootstrap 5, JavaScript léger, MySQL 8, Laravel Breeze, Spatie Laravel Permission.

## État réel
Les fichiers de fondation, le modèle User, les migrations initiales (utilisateurs, rôles, établissements, profils enseignants, offres, candidatures, missions, abonnements et audit), le seeder de rôles et une première page d'accueil sont présents.

**Cette branche n'est pas encore exécutable telle quelle.** Le squelette Laravel doit être complété avec les fichiers standard du framework, les vues et contrôleurs Breeze, les assets Vite, les modèles métier, les Policies, les tests et les configurations de déploiement. La migration doit être testée sur une base MySQL locale avant utilisation.

## Mise en place locale recommandée
La manière la plus sûre est de générer un projet Laravel 12 propre, puis d'y intégrer les fichiers de cette branche :
```bash
composer create-project laravel/laravel eduvac-pro "^12.0"
cd eduvac-pro
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
```

Copier ensuite les fichiers V2 du dépôt dans le projet Laravel généré, en fusionnant les fichiers existants (notamment `composer.json`, `bootstrap/app.php`, `routes/web.php` et `config/permission.php`) plutôt qu'en les écrasant aveuglément. Vérifier que les migrations standard Laravel et Breeze ne sont pas dupliquées. Puis :
```bash
cp .env.example .env
php artisan key:generate
# Configurer DB_* dans .env pour MySQL
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Sécurité et données
- Ne jamais connecter cette branche directement à la base Supabase de production.
- Sauvegarder et auditer les données existantes avant toute migration.
- Ne pas migrer les mots de passe ni les tokens Supabase.
- Ne jamais commiter `.env`, clé privée, secret de paiement ou clé de service.
- Les CV et justificatifs doivent rester sur un disque privé, accessibles uniquement après autorisation serveur.

## Prochaine étape technique
Compléter l'authentification Breeze et les flux d'inscription distincts (enseignant/établissement), puis créer les Policies et tests d'isolation des données avant d'implémenter les tableaux de bord.
