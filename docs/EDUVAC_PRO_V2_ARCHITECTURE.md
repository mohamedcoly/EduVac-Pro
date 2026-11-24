# EduVac Pro V2 — Architecture cible

## 1. Positionnement
EduVac Pro est un SaaS B2B2C de recrutement et de gestion des enseignants vacataires. Le pilote commercial démarre à Dakar, Sénégal. Les établissements scolaires sont les clients payants ; les enseignants créent gratuitement leur profil et candidatent aux missions.

## 2. Périmètre V1 commercialisable
- Établissements : inscription, vérification, profil, gestion des établissements/campus, publication et gestion des offres, recherche de profils, candidatures, shortlist, entretiens, missions, évaluations et facturation/abonnement.
- Enseignants : inscription, profil professionnel, diplômes/expériences, matières, niveaux, zones de mobilité, disponibilités, dépôt privé de CV et justificatifs, candidatures, suivi et évaluations.
- Administration plateforme : vérification des établissements et enseignants, modération, gestion des signalements, suspension, suivi des abonnements et journal d'audit.
- Matching explicable : matière, niveau, disponibilité, zone/distance et expérience. Le score aide au tri ; il ne prend aucune décision automatique.
- Notifications applicatives et email ; WhatsApp pourra être ajouté ultérieurement via un fournisseur conforme.
- Interface responsive et adaptée aux connexions mobiles.

## 3. Hors périmètre de la première version
Application mobile native, intelligence artificielle générative, paie des enseignants, comptabilité complète, ERP scolaire, signature électronique certifiée, visioconférence intégrée et expansion multi-pays avant validation du pilote Dakar.

## 4. Stack retenue
- Backend : PHP 8.3+ avec Laravel (monolithe modulaire).
- Frontend : Blade, Bootstrap 5, JavaScript léger ; pas de SPA complexe.
- Base de données : MySQL 8.
- Authentification : Laravel Breeze (sessions sécurisées, vérification email, réinitialisation mot de passe).
- Autorisations : rôles et permissions avec Spatie Laravel Permission, complétés par Policies Laravel sur chaque ressource.
- Documents : stockage privé Laravel ; téléchargement via contrôleur autorisé et URL temporaire, jamais de bucket public.
- PDF : DomPDF pour les exports simples.
- Géographie : géocodage et cartes OpenStreetMap, avec saisie manuelle toujours possible.
- Tests : PHPUnit ou Pest ; GitHub pour le versionnement et les issues.
- Déploiement : VPS Linux, HTTPS, sauvegardes automatiques et environnement de staging.

## 5. Architecture logique
Monolithe modulaire : Authentification, Comptes & établissements, Profils enseignants, Offres, Candidatures, Matching, Missions, Évaluations, Abonnements, Notifications, Administration et Audit.

Les contrôleurs HTTP valident les entrées via Form Requests, délèguent la logique métier à des services, puis utilisent Policies pour vérifier les accès. Les modèles Eloquent représentent les données. Les tâches longues (emails, génération PDF) passent par la queue lorsque l'hébergement le permet.

## 6. Rôles
- teacher : enseignant.
- school_manager : responsable d'un établissement.
- school_recruiter : recruteur rattaché à un établissement.
- platform_admin : administrateur de la plateforme.

Un compte établissement peut avoir plusieurs membres. L'appartenance à un établissement est stockée dans une table pivot avec rôle local ; ne jamais faire confiance à un rôle envoyé par le navigateur.

## 7. Parcours métier
1. L'école crée son compte et son établissement ; statut pending.
2. L'administration vérifie l'établissement et l'active.
3. Le responsable choisit un plan, complète le profil et publie une offre.
4. Les enseignants vérifiés consultent l'offre et postulent avec leur profil/CV.
5. L'école compare les candidatures, contacte les candidats et planifie un entretien.
6. L'école sélectionne un enseignant et crée une mission.
7. À la fin, les deux parties peuvent laisser une évaluation modérée.
8. L'école gère son abonnement et ses factures/reçus.

## 8. Modèle commercial de référence
Plans discutés : Découverte 0 FCFA, Essentiel 7 500 FCFA/mois, Pro 15 000 FCFA/mois, Réseau 30 000 FCFA/mois. Les limites exactes (offres actives, membres, établissements) doivent être appliquées côté serveur dans un PlanLimitService. Ne pas bloquer les données historiques lorsqu'un abonnement expire.

## 9. Principes de sécurité obligatoires
- Hashage des mots de passe par Laravel ; aucun mot de passe en clair.
- CSRF sur les formulaires web, validation serveur et limitation de débit sur login, inscription et réinitialisation.
- Vérification email ; contrôle de statut du compte et de validation métier.
- Policies sur chaque offre, candidature, mission, document et établissement.
- Un recruteur ne voit que les données des établissements auxquels il appartient.
- CV et pièces justificatives en stockage privé, avec contrôle d'accès à chaque téléchargement.
- Les secrets et mots de passe de base ne sont jamais commités ; .env reste ignoré.
- Journal d'audit des actions administratives et sensibles.
- Sauvegardes chiffrées, test de restauration et HTTPS obligatoire.
- Pas de rôle admin attribuable depuis le formulaire d'inscription.

## 10. Ordre d'implémentation
A. Socle Laravel, configuration, migrations, authentification et rôles.
B. Établissements, membres et validation.
C. Profils enseignants et documents privés.
D. Offres, candidatures et pipeline de recrutement.
E. Matching explicable, disponibilité et notifications.
F. Missions, évaluations, abonnements et administration.
G. Tests, durcissement, déploiement pilote et documentation.

Cette architecture remplace la logique prototype HTML/Supabase par une application Laravel/MySQL maintenable. La branche de refonte doit être développée et testée avant toute fusion dans master.