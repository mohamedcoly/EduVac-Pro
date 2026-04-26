<p align="center">
  <img src="https://img.shields.io/badge/EduVac%20Pro-Plateforme%20Éducative-4f46e5?style=for-the-badge&logoColor=white" alt="EduVac Pro" />
</p>

<h1 align="center">🎓 EduVac Pro</h1>

<p align="center">
  <strong>Plateforme de mise en relation entre enseignants vacataires et établissements scolaires</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" />
  <img src="https://img.shields.io/badge/Supabase-3ECF8E?style=flat-square&logo=supabase&logoColor=white" />
  <img src="https://img.shields.io/badge/PostgreSQL-316192?style=flat-square&logo=postgresql&logoColor=white" />
</p>

---

## 📋 Présentation

**EduVac Pro** est une plateforme web de type marketplace qui connecte les **établissements scolaires** en besoin urgent de remplaçants avec une **communauté d'enseignants vacataires** qualifiés et disponibles.

> *Parce que chaque journée d'école compte.*

### Le problème

Le remplacement d'enseignants absents reste un processus **lent, informel et non sécurisé** (appels téléphoniques, bouche-à-oreille). Les établissements perdent parfois plusieurs jours à trouver un remplaçant qualifié.

### La solution

EduVac Pro centralise l'offre et la demande sur une plateforme unique avec un **algorithme de matching intelligent**, un **système de candidature en un clic**, et une **messagerie intégrée** pour accélérer la mise en relation.

---

## ✨ Fonctionnalités

### 👨‍🏫 Espace Professeur
- Création de profil complet (diplôme, spécialités, expérience, ville)
- Upload de CV au format PDF
- Calendrier de disponibilités interactif (demi-journées)
- Parcours des offres avec **score de matching** personnalisé
- Candidature en un clic
- Suivi de l'état des candidatures (En attente / Accepté / Refusé)
- Chat intégré avec le recruteur

### 🏫 Espace Recruteur
- Publication d'offres de vacation (titre, niveau, lieu, disponibilité requise)
- Modification des annonces existantes
- Deux onglets : **Candidatures** et **Offres en cours** (disposition flex inline)
- Visualisation des **disponibilités du professeur** sur chaque candidature
- Recherche de profils enseignants avec filtres (ville, diplôme)
- Acceptation/refus avec **notification automatique** via chat
- Score de matching sur chaque profil

### 🔐 Espace Administrateur
- Validation des inscriptions enseignants
- Consultation des CVs
- Gestion des profils en attente

### 🧠 Algorithme de Matching
Score de compatibilité sur **100 points** calculé selon :

| Critère | Points max | Détail |
|---------|-----------|--------|
| Localisation | 40 pts | Correspondance géographique |
| Diplôme | 40 pts | Doctorat > Bac+5 > Bac+4 > Bac+3 |
| Expérience | 20 pts | Années d'enseignement (plafonné à 5 ans) |

---

## 🛠️ Technologies

| Technologie | Rôle |
|-------------|------|
| **HTML5** | Structure des pages |
| **CSS3** | Styles personnalisés et animations |
| **JavaScript ES6+** | Logique métier et interactions |
| **Tailwind CSS** | Framework CSS utilitaire |
| **Supabase** | Backend as a Service (Auth, DB, Storage) |
| **PostgreSQL** | Base de données relationnelle |
| **DiceBear** | Génération d'avatars uniques |
| **Google Fonts** | Typographie Inter |

---

## 📁 Structure du projet

```
EduVac-Pro/
├── index.html                  # Landing page + authentification
├── dashboard_teacher.html      # Espace professeur
├── dashboard_recruiter.html    # Espace recruteur
├── admin.html                  # Interface administrateur
├── pending.html                # Page d'attente de validation
├── onboarding_teacher.html     # Onboarding professeur
├── supabase_schema.sql         # Schéma complet de la base de données
├── css/
│   └── style.css               # Styles globaux et design system
├── js/
│   ├── auth.js                 # Authentification (connexion / inscription)
│   ├── dashboard_teacher.js    # Logique du dashboard professeur
│   ├── dashboard_recruiter.js  # Logique du dashboard recruteur
│   ├── matching.js             # Algorithme de matching
│   └── toast.js                # Système de notifications toast
└── README.md
```

---

## 🚀 Installation et lancement

### Prérequis
- Un navigateur web moderne (Chrome, Firefox, Edge)
- Un compte [Supabase](https://supabase.com) (gratuit)

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/mohamedcoly/EduVac-Pro.git
   cd EduVac-Pro
   ```

2. **Configurer Supabase**
   - Créer un projet sur [supabase.com](https://supabase.com)
   - Exécuter le fichier `supabase_schema.sql` dans l'éditeur SQL de Supabase
   - Copier l'URL et la clé `anon` du projet

3. **Mettre à jour les clés**
   - Remplacer `supabaseUrl` et `supabaseAnonKey` dans les fichiers :
     - `js/auth.js`
     - `js/dashboard_teacher.js`
     - `js/dashboard_recruiter.js`

4. **Lancer le projet**
   - Ouvrir `index.html` dans un navigateur
   - Ou utiliser l'extension **Live Server** de VS Code

---

## 🎯 Scénario de démonstration

1. **Inscription** d'un professeur → remplissage du profil et des disponibilités
2. **Inscription** d'un recruteur → publication d'une offre avec disponibilité requise
3. Le professeur **postule** → la candidature apparaît chez le recruteur avec les disponibilités
4. Le recruteur **accepte** → message de notification envoyé automatiquement
5. **Ouverture du chat** → échange entre les deux parties

---

## 📊 Schéma de la base de données

```
profiles ──────────┐
  id (UUID, PK)    │
  role (ENUM)      │     teacher_details
  full_name        │◄─── id (FK → profiles)
  city             │     diploma, experience_years
  is_validated     │     specialties, cv_url
                   │
                   │     availabilities
                   │◄─── teacher_id (FK → profiles)
                   │     jour_semaine, matin, apres_midi
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
  teacher_id ─────→ profiles
  status (pending/accepted/rejected)
                   │
chat_messages ─────┤
  application_id ──┘
  sender_id ──────→ profiles
  content
```

---

## 👥 Équipe

| Nom | Rôle |
|-----|------|
| Mohamed Coly | Développeur principal |

**Filière** : Licence 2 Informatique  
**Année** : 2025 – 2026

---

## 📄 Licence

Ce projet a été réalisé dans le cadre d'un projet universitaire.

---

<p align="center">
  <strong>EduVac Pro</strong> — Moderniser le recrutement des enseignants vacataires 🎓
</p>
