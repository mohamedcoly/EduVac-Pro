# Exposé du Projet EduVac Pro
## Support de Présentation — Licence 2 Informatique

---

## 📌 Diapo 1 — Page de Garde

- **Titre du projet** : EduVac Pro — Plateforme de Mise en Relation Enseignants Vacataires / Établissements Scolaires
- **Membres du groupe** : *(à compléter)*
- **Filière** : Licence 2 Informatique
- **Encadreur / Tuteur** : *(à compléter)*
- **Année universitaire** : 2025 – 2026

---

## 📌 Diapo 2 — Contexte et Présentation du Projet

### Présentation générale

EduVac Pro est une **plateforme web de type marketplace** qui connecte les **établissements scolaires** (écoles, collèges, lycées) en besoin urgent de remplaçants avec une **communauté d'enseignants vacataires** qualifiés et disponibles.

La plateforme fonctionne comme un écosystème à **trois acteurs** :
1. **Les Professeurs** — créent leur profil, renseignent leurs disponibilités et postulent aux missions
2. **Les Recruteurs** (établissements) — publient des offres de vacation, consultent les profils et gèrent les candidatures
3. **L'Administrateur** — valide les inscriptions des enseignants pour garantir la fiabilité

### Pourquoi ce projet a été choisi

Le secteur de l'éducation souffre d'un **manque criant de solutions numériques** pour la gestion des remplacements d'enseignants. Les processus actuels (appels téléphoniques, réseaux informels, bouche-à-oreille) sont :
- **Lents** : plusieurs jours pour trouver un remplaçant
- **Inefficaces** : pas de centralisation des profils disponibles
- **Non sécurisés** : aucune vérification des diplômes

Ce projet répond au **besoin concret** de moderniser et accélérer la mise en relation entre l'offre et la demande dans le recrutement d'enseignants vacataires.

---

## 📌 Diapo 3 — Problématique

### Question centrale

> **Comment concevoir et développer une plateforme web permettant de réduire le délai de remplacement d'un enseignant absent tout en garantissant la qualité et la fiabilité des profils recrutés ?**

### Sous-problématiques

1. Comment **automatiser le matching** entre un besoin de recrutement et un profil enseignant ?
2. Comment **centraliser et sécuriser** les données sensibles (CV, diplômes, disponibilités) ?
3. Comment offrir une **expérience utilisateur fluide** pour des utilisateurs non techniques (directeurs d'école, professeurs) ?
4. Comment permettre une **communication directe** entre recruteur et candidat au sein même de la plateforme ?

### Importance dans le domaine informatique

Ce projet met en pratique des compétences fondamentales en informatique :
- Conception d'une **architecture client-serveur** avec API REST
- Gestion de **bases de données relationnelles** avec politiques de sécurité (RLS)
- Développement d'une **interface utilisateur** moderne et responsive
- Implémentation d'un **algorithme de matching** intelligent

---

## 📌 Diapo 4 — Outils et Technologies Utilisés

### Langages de programmation

| Langage | Utilisation | Pourquoi ce choix |
|---------|-------------|-------------------|
| **HTML5** | Structure des pages web (landing page, dashboards, admin) | Standard universel du web, sémantique, accessible par tous les navigateurs sans installation |
| **CSS3** | Mise en forme, animations, responsive design | Permet de créer des interfaces visuellement attractives avec des transitions fluides et du glassmorphisme |
| **JavaScript (ES6+)** | Logique métier, interactions dynamiques, appels API | Langage natif du navigateur, permet des interactions temps réel sans rechargement de page (SPA) |
| **SQL** | Définition du schéma de base de données, triggers, fonctions RPC | Langage standard pour la gestion de bases de données relationnelles, indispensable pour Supabase |

### Framework CSS

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **Tailwind CSS (CDN)** | Classes utilitaires pour le design des interfaces | Productivité maximale : style directement dans le HTML, pas de fichiers CSS volumineux à maintenir. Cohérence visuelle garantie par le système de design tokens |

### Plateforme Backend (BaaS)

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **Supabase** | Authentification, base de données, stockage de fichiers, API temps réel | Alternative open-source à Firebase. Offre une base **PostgreSQL** complète avec authentification intégrée, stockage de fichiers (CVs), et des politiques de sécurité RLS (Row Level Security) — **tout cela sans coder un serveur backend** |

### Base de données

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **PostgreSQL** (via Supabase) | Stockage structuré de toutes les données (profils, missions, candidatures, messages, disponibilités) | SGBD relationnel robuste, open-source, supporte les types ENUM, JSON, UUID, les triggers et les fonctions stockées. Idéal pour des données structurées avec des relations complexes |

### Stockage de fichiers

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **Supabase Storage** | Upload et hébergement sécurisé des CVs (PDF) | Stockage cloud avec gestion fine des permissions (un professeur ne peut voir que son propre CV, les recruteurs peuvent voir tous les CVs) via des politiques de sécurité |

### Librairies externes

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **DiceBear Avatars** | Génération automatique d'avatars uniques par utilisateur | Humanise l'interface sans que l'utilisateur ait besoin d'uploader sa photo. Chaque avatar est unique et déterministe (même seed = même avatar) |
| **Google Fonts (Inter)** | Typographie professionnelle | Police moderne, lisible, largement utilisée dans les interfaces SaaS professionnelles |

### Outils de développement

| Outil | Utilisation | Pourquoi ce choix |
|-------|-------------|-------------------|
| **Visual Studio Code** | Éditeur de code principal | IDE gratuit, extensible, avec support natif de HTML/CSS/JS et terminal intégré |
| **Navigateur Chrome** (DevTools) | Tests, débogage, inspection du DOM | Console JavaScript, inspecteur réseau, et simulateur responsive intégrés |
| **Git / GitHub** | Versionnement du code source | Traçabilité des modifications, collaboration, et sauvegarde du projet |

---

## 📌 Diapo 5 — Analyse et Conception

### Besoins fonctionnels

#### Côté Professeur
- S'inscrire et se connecter de manière sécurisée
- Renseigner son profil (nom, ville, diplôme, expérience, spécialités)
- Uploader son CV au format PDF
- Gérer son calendrier de disponibilités (demi-journées par jour de la semaine)
- Parcourir les offres de missions avec un score de matching
- Postuler à une mission en un clic
- Suivre l'état de ses candidatures (en attente, accepté, refusé)
- Discuter avec le recruteur via un chat intégré

#### Côté Recruteur
- S'inscrire et se connecter
- Publier des offres de vacation (titre, niveau, lieu, disponibilité requise, description)
- Modifier et mettre à jour ses annonces
- Consulter les candidatures reçues avec la disponibilité du professeur
- Rechercher des profils enseignants avec filtres (ville, diplôme)
- Voir le score de matching de chaque professeur
- Accepter ou refuser une candidature avec notification automatique
- Discuter avec les candidats acceptés

#### Côté Administrateur
- Voir la liste des inscriptions en attente de validation
- Valider ou rejeter un profil enseignant
- Consulter les CV des candidats

### Besoins non fonctionnels

| Besoin | Description |
|--------|-------------|
| **Sécurité** | Authentification par email/mot de passe, Row Level Security (RLS) sur chaque table, stockage chiffré des CVs |
| **Performance** | Chargement rapide via CDN (Tailwind, Supabase JS), pas de framework lourd côté client |
| **Ergonomie** | Interface intuitive avec sidebar de navigation, design moderne, micro-animations |
| **Responsive** | Adaptation aux écrans desktop et tablette |
| **Maintenabilité** | Code modulaire (fichiers JS séparés par rôle), CSS centralisé |

### Architecture du système

```
┌──────────────────────────────────────────────────┐
│                  NAVIGATEUR (Client)             │
│                                                  │
│  ┌──────────┐  ┌───────────────┐  ┌───────────┐ │
│  │index.html│  │dashboard_     │  │dashboard_ │ │
│  │(Landing) │  │teacher.html   │  │recruiter  │ │
│  └────┬─────┘  └──────┬────────┘  └─────┬─────┘ │
│       │               │                 │        │
│  ┌────┴───┐    ┌──────┴──────┐   ┌──────┴─────┐ │
│  │auth.js │    │dashboard_   │   │dashboard_  │ │
│  │        │    │teacher.js   │   │recruiter.js│ │
│  └────┬───┘    └──────┬──────┘   └──────┬─────┘ │
│       │               │                 │        │
│       └───────────────┼─────────────────┘        │
│                       │                          │
│               ┌───────┴───────┐                  │
│               │  matching.js  │                  │
│               │  toast.js     │                  │
│               └───────┬───────┘                  │
└───────────────────────┼──────────────────────────┘
                        │ API REST (HTTPS)
                        ▼
┌──────────────────────────────────────────────────┐
│              SUPABASE (Backend as a Service)      │
│                                                  │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │    Auth    │  │ PostgreSQL │  │  Storage   │ │
│  │ (Comptes) │  │   (Data)   │  │   (CVs)    │ │
│  └────────────┘  └────────────┘  └────────────┘ │
└──────────────────────────────────────────────────┘
```

### Schéma de la base de données (Tables principales)

```
profiles ──────────┐
  id (UUID, PK)    │
  role (ENUM)      │
  full_name        │     teacher_details
  city             │◄──── id (FK → profiles)
  is_validated     │      diploma, experience_years
  email_alerts     │      specialties, cv_url
                   │
                   │     availabilities
                   │◄──── teacher_id (FK → profiles)
                   │      jour_semaine, matin, apres_midi
                   │
missions ──────────┤
  id (UUID, PK)    │
  recruiter_id ────┘
  title, level, location
  availability, description
                   │
applications ──────┤
  id (UUID, PK)    │
  mission_id ──────┘
  teacher_id ──────→ profiles
  status (ENUM: pending/accepted/rejected)
                   │
chat_messages ─────┤
  application_id ──┘
  sender_id ───────→ profiles
  content
```

---

## 📌 Diapo 6 — Réalisation du Projet

### Présentation de la solution

EduVac Pro est composé de **5 pages web** interconnectées :

| Page | Rôle |
|------|------|
| `index.html` | Landing page publique + modale d'authentification (connexion / inscription) |
| `dashboard_teacher.html` | Espace professeur : découverte des offres, candidatures, profil & disponibilités |
| `dashboard_recruiter.html` | Espace recruteur : recherche de profils, gestion candidatures (2 onglets), offres en cours |
| `admin.html` | Interface administrateur pour valider les inscriptions |
| `pending.html` | Page d'attente de validation du profil |

### Fonctionnalités principales

#### 1. Authentification sécurisée
- Inscription avec rôle (Professeur / Recruteur)
- Connexion par email et mot de passe
- Création automatique du profil en base de données
- Redirection intelligente selon le rôle

#### 2. Algorithme de Matching intelligent
Score de compatibilité calculé sur **3 critères** (100 points max) :
- **Localisation** (40 pts) : correspondance géographique entre le professeur et la mission
- **Diplôme** (40 pts) : niveau d'études (Doctorat > Bac+5 > Bac+4 > Bac+3)
- **Expérience** (20 pts) : années d'enseignement (plafonné à 5 ans)

Résultat affiché sous forme de **badge coloré** (vert ≥ 80%, jaune ≥ 50%, gris < 50%).

#### 3. Gestion des disponibilités
- Calendrier interactif (7 jours × 2 créneaux : matin / après-midi)
- Les disponibilités du professeur sont **visibles par le recruteur** sur chaque candidature
- Le recruteur peut **préciser la disponibilité requise** pour chaque mission

#### 4. Système de candidature complet
- Postulation en un clic depuis le tableau de bord professeur
- Suivi en temps réel de l'état (En attente → Accepté / Refusé)
- **Notification automatique** : accepter ou refuser une candidature envoie directement un message chat au candidat

#### 5. Messagerie intégrée (Chat)
- Chat en temps réel entre recruteur et candidat accepté
- Avatars personnalisés et interface intuitive
- Historique des messages conservé

#### 6. Gestion des offres par le recruteur
- Publication d'offres avec titre, niveau, lieu, disponibilité et description
- **Modification des annonces** via une modale d'édition
- Deux onglets en **flex inline** : « Candidatures » et « Offres en cours »

#### 7. Panneau d'administration
- Liste des professeurs en attente de validation
- Bouton de validation rapide
- Accès aux CVs des candidats

### Démonstration

*(Démonstration en direct de la plateforme)*
1. Inscription d'un professeur → remplissage du profil et disponibilités
2. Inscription d'un recruteur → publication d'une offre avec disponibilité
3. Le professeur postule → la candidature apparaît chez le recruteur avec les disponibilités
4. Le recruteur accepte → message automatique envoyé au professeur
5. Ouverture du chat → échange entre les deux parties

---

## 📌 Diapo 7 — Difficultés Rencontrées

### Problèmes techniques

| Problème | Impact | Solution apportée |
|----------|--------|-------------------|
| **Row Level Security (RLS) Supabase** | Les politiques de sécurité bloquaient certaines requêtes légitimes (insertion de profil à l'inscription) | Désactivation temporaire du RLS en mode démo + réécriture des triggers |
| **Trigger `handle_new_user`** | Le trigger automatique d'insertion de profil entrait en conflit avec la création manuelle | Suppression du trigger et gestion de l'insertion côté JavaScript (auth.js) |
| **Encodage des caractères (UTF-8)** | Les accents (é, è, ê) s'affichaient mal dans certains cas | Ajout systématique de `<meta charset="UTF-8">` et vérification de l'encodage des fichiers |
| **Gestion des modules ES6** | `import/export` ne fonctionnait pas avec des scripts classiques | Utilisation de `<script type="module">` pour activer le support des imports |

### Contraintes rencontrées

- **Pas de serveur backend dédié** : tout repose sur Supabase (BaaS), ce qui limite certaines fonctionnalités avancées
- **Temps limité** : priorisation des fonctionnalités essentielles (matching, candidatures, chat)
- **Tests en mode démo** : nécessité de contourner la validation email pour faciliter les tests devant le jury

---

## 📌 Diapo 8 — Bilan du Projet

### Ce que nous avons appris

- **Architecture web moderne** : conception d'une application complète sans framework lourd
- **Backend as a Service** : utilisation de Supabase comme alternative au développement backend traditionnel
- **SQL avancé** : triggers, fonctions RPC, types ENUM, contraintes d'unicité, politiques RLS
- **Design d'interface** : création d'un UI premium avec Tailwind CSS (glassmorphisme, animations, système de design)
- **Algorithme de matching** : conception d'un système de scoring multi-critères

### Compétences développées

| Compétence | Détail |
|------------|--------|
| **Développement Front-end** | HTML5, CSS3, JavaScript ES6+, DOM manipulation |
| **Base de données** | Modélisation relationnelle, SQL, PostgreSQL |
| **Sécurité web** | Authentification, RLS, politiques d'accès aux fichiers |
| **UX/UI Design** | Interfaces intuitives, responsive, accessibles |
| **Gestion de projet** | Organisation en phases, priorisation des fonctionnalités |
| **Résolution de problèmes** | Débogage, lecture de documentation technique, adaptation |

### Apports du projet

- **Académique** : mise en pratique concrète des cours de programmation web, bases de données et algorithmique
- **Pratique** : expérience d'un projet de bout en bout, de la conception à la démonstration

---

## 📌 Diapo 9 — Limites et Perspectives

### Limites actuelles

1. **Pas de notifications push/email réelles** : les notifications se font uniquement via le chat intégré
2. **Pas de vérification d'identité forte** : les diplômes sont déclarés mais non vérifiés automatiquement
3. **Pas d'application mobile native** : l'interface est responsive mais pas optimisée pour mobile
4. **Mode démo** : le RLS est désactivé pour la présentation, ce qui ne serait pas acceptable en production
5. **Pas de système de paiement** : aucune gestion financière des vacations

### Améliorations possibles

| Amélioration | Description |
|--------------|-------------|
| **Notifications email** | Envoyer des emails automatiques lors des changements de statut via Supabase Edge Functions |
| **Vérification de diplômes** | Intégrer un système d'upload avec validation OCR (reconnaissance optique de caractères) |
| **Application mobile** | Développer une version mobile avec React Native ou Flutter |
| **Géolocalisation** | Utiliser l'API Google Maps pour calculer les distances réelles au lieu d'une comparaison textuelle |
| **Système de notation** | Permettre aux recruteurs et professeurs de se noter mutuellement après une mission (table `ratings` déjà prévue dans le schéma) |
| **Intelligence artificielle** | Améliorer l'algorithme de matching avec du machine learning (historique des missions réussies) |
| **Contrats automatisés** | Générer des contrats PDF pré-remplis à la signature électronique |

---

## 📌 Diapo 10 — Conclusion

### Résumé global

EduVac Pro est une **plateforme web fonctionnelle** qui répond au besoin de mise en relation rapide entre établissements scolaires et enseignants vacataires. Le projet couvre l'ensemble du **cycle de recrutement** : de la publication de l'offre à l'échange direct entre les parties, en passant par un algorithme de matching intelligent et une gestion fine des disponibilités.

### Importance du projet

Dans un contexte où la **continuité pédagogique** est un enjeu majeur, disposer d'un outil numérique capable de trouver un remplaçant qualifié en quelques heures (au lieu de plusieurs jours) représente une **avancée significative** pour le monde de l'éducation.

### Ouverture

Ce projet constitue une **base solide** pouvant évoluer vers un produit SaaS (Software as a Service) commercialisable. L'architecture choisie (Supabase + frontend léger) permet une **mise à l'échelle progressive** sans refonte majeure. À terme, EduVac Pro pourrait s'étendre à d'autres secteurs nécessitant du remplacement temporaire de personnel qualifié (santé, industrie, etc.).

---

## 📌 Diapo 11 — Questions / Réponses

> ### Merci pour votre attention ! 🎓
>
> Nous sommes à votre disposition pour répondre à vos questions.
>
> **EduVac Pro** — *Parce que chaque journée d'école compte.*

