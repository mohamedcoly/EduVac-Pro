# EduVac Pro V2 — refonte SaaS

Cette branche prépare la migration du prototype vers une application SaaS Laravel/MySQL destinée au recrutement de professeurs vacataires au Sénégal.

## Documentation V2
- [Architecture cible](docs/EDUVAC_PRO_V2_ARCHITECTURE.md)
- [Cahier des charges fonctionnel](docs/EDUVAC_PRO_V2_PRODUCT_SPEC.md)
- [Plan de refonte et critères d'acceptation](docs/EDUVAC_PRO_V2_REFACTOR_PLAN.md)

## Stack cible
PHP 8.3+, Laravel, Blade, Bootstrap 5, JavaScript léger, MySQL 8, Laravel Breeze, Spatie Laravel Permission, DomPDF, OpenStreetMap.

## État de cette branche
La documentation de cadrage et une première migration de domaine ont été ajoutées. **Ce n'est pas encore une application Laravel exécutable** : le squelette Laravel, les modèles, contrôleurs, policies, vues, tests et configuration de déploiement restent à construire.

## Démarrage prévu après création du squelette Laravel
```bash
composer create-project laravel/laravel eduvac-pro
cd eduvac-pro
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require spatie/laravel-permission
php artisan migrate
npm install
npm run build
```

Configurer ensuite les variables de connexion MySQL dans `.env`. Ne jamais publier ce fichier.

## Migration des données
Ne pas connecter cette branche à la base Supabase de production. Sauvegarder et auditer les données existantes ; ne transférer que les données métier nécessaires. Les mots de passe et tokens Supabase ne sont pas migrés.
