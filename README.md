# EduRecruit - Plateforme de Recrutement de Professeurs Vacataires

## Description

EduRecruit est une plateforme complète d'inscription et de recrutement de professeurs vacataires permettant aux établissements et recruteurs de trouver et contacter des professeurs qualifiés, et aux professeurs de créer leur profil professionnel complet.

## 🚀 Fonctionnalités

### 🔐 Authentification sécurisée
- **Inscription** : Création de compte pour professeurs et recruteurs
- **Connexion** : Authentification avec email, mot de passe et rôle
- **Gestion de session** : Session persistante avec localStorage
- **Rôles** : Professeur, Recruteur, Administrateur

### 👥 Gestion des profils
- **Profil complet** : Informations personnelles et professionnelles
- **Pour les professeurs** :
  - Matières enseignées
  - Niveaux (Primaire, Collège, Lycée, Université)
  - Diplômes et certifications
  - Années d'expérience
  - Description professionnelle
- **Édition de profil** : Modification des informations à tout moment
- **Affichage public** : Profils visibles par les recruteurs

### 🔍 Recherche et filtrage
- **Recherche textuelle** : Par nom, email, matières, description
- **Filtres avancés** :
  - Par matière enseignée
  - Par niveau d'enseignement
- **Affichage en grille** : Interface moderne et intuitive

### 💬 Système de messagerie
- **Conversations** : Messagerie en temps réel entre recruteurs et professeurs
- **Historique** : Conservation de toutes les conversations
- **Notifications** : Alertes pour nouveaux messages
- **Recherche** : Recherche dans les conversations
- **Indicateurs** : Badges pour messages non lus

### 🔔 Système de notifications
- **Notifications en temps réel** : Alertes pour toutes les activités importantes
- **Types de notifications** :
  - Nouveaux messages
  - Mises à jour de profil
  - Nouvelles offres
  - Confirmations d'actions
- **Marquage** : Marquer comme lu / Tout marquer comme lu
- **Badges** : Indicateurs visuels du nombre de notifications non lues

### 📊 Tableau de bord
- **Statistiques** :
  - Nombre total de professeurs inscrits
  - Nombre d'offres actives
  - Nombre de messages
  - Nombre de notifications
- **Activité récente** : Dernières actions sur la plateforme
- **Professeurs récents** : Derniers professeurs inscrits

### 📱 Interface responsive
- **Design moderne** : Interface élégante et professionnelle
- **Responsive** : Adapté à tous les écrans (desktop, tablette, mobile)
- **Navigation intuitive** : Menu de navigation clair et accessible

## 📁 Structure du projet

```
├── recrutement-professeurs.html  # Interface principale
├── recrutement-professeurs.css   # Styles et design
├── recrutement-professeurs.js    # Logique de l'application
└── README_RECRUTEMENT.md         # Documentation
```

## 🔐 Connexion Administrateur

**Identifiants par défaut :**
- **Email** : `admin@edurecruit.com`
- **Mot de passe** : `admin123`
- **Rôle** : `Administrateur`

⚠️ **Important** : En production, remplacez ce système d'authentification par un système sécurisé avec hashage de mots de passe et gestion des permissions avancées.

## 🎯 Utilisation

### Pour les professeurs

1. **Inscription** :
   - Cliquez sur "Inscription"
   - Remplissez le formulaire avec vos informations
   - Sélectionnez "Professeur" comme type de compte
   - Validez votre inscription

2. **Compléter son profil** :
   - Allez dans "Profil"
   - Cliquez sur "Modifier le profil"
   - Ajoutez vos matières, niveaux, diplômes, expérience et description
   - Enregistrez vos modifications

3. **Gérer les messages** :
   - Consultez vos messages dans la section "Messages"
   - Répondez aux recruteurs qui vous contactent
   - Recevez des notifications pour nouveaux messages

### Pour les recruteurs

1. **Inscription** :
   - Cliquez sur "Inscription"
   - Remplissez le formulaire
   - Sélectionnez "Recruteur" comme type de compte
   - Validez votre inscription

2. **Rechercher des professeurs** :
   - Allez dans "Professeurs"
   - Utilisez la barre de recherche
   - Filtrez par matière et/ou niveau
   - Consultez les profils détaillés

3. **Contacter un professeur** :
   - Cliquez sur un profil de professeur
   - Cliquez sur "Contacter"
   - Envoyez votre message
   - Suivez vos conversations dans "Messages"

## 🔧 Technologies utilisées

- **HTML5** : Structure sémantique
- **CSS3** : Styles modernes avec Grid, Flexbox, animations
- **JavaScript (ES6+)** : Logique de l'application orientée objet
- **Font Awesome** : Icônes
- **Google Fonts (Inter)** : Typographie moderne
- **localStorage** : Stockage local des données

## 💾 Stockage des données

Toutes les données sont stockées dans le **localStorage** du navigateur :

- `edurecruit_users` : Tous les utilisateurs (professeurs, recruteurs, admins)
- `edurecruit_professeurs` : Liste des professeurs avec leurs profils complets
- `edurecruit_messages` : Toutes les conversations et messages
- `edurecruit_notifications` : Toutes les notifications
- `edurecruit_offres` : Offres d'emploi (pour futures fonctionnalités)
- `edurecruit_session` : Session utilisateur actuelle

## 🎨 Design

- **Couleurs principales** :
  - Bleu primaire : `#4f46e5` (Indigo)
  - Vert : `#10b981` (Succès)
  - Rouge : `#ef4444` (Danger/Alertes)
  - Orange : `#f59e0b` (Avertissements)

- **Interface** :
  - Design moderne et épuré
  - Animations fluides
  - Cards avec ombres
  - Responsive design

## 🔒 Sécurité

⚠️ **Version de démonstration** : Cette version utilise localStorage pour le stockage des données. Pour une utilisation en production, il est nécessaire d'implémenter :

- Un backend serveur sécurisé
- Une base de données chiffrée
- Un système d'authentification robuste (hashage de mots de passe)
- Validation et sanitization des données
- Protection CSRF
- HTTPS obligatoire
- Conformité RGPD

## 📝 Données de démonstration

La plateforme inclut 5 professeurs de démonstration avec des profils complets pour tester les fonctionnalités :

1. **Marie Dubois** - Mathématiques, Physique (Collège, Lycée)
2. **Jean Martin** - Français, Histoire (Primaire, Collège)
3. **Sophie Laurent** - Anglais, Espagnol (Collège, Lycée, Université)
4. **Pierre Moreau** - Informatique, Mathématiques (Lycée, Université)
5. **Claire Bernard** - Sciences, Chimie (Collège, Lycée)

## 🚀 Démarrage rapide

1. Ouvrez `recrutement-professeurs.html` dans un navigateur moderne
2. Créez un compte (Professeur ou Recruteur)
3. Explorez les fonctionnalités :
   - Complétez votre profil (si professeur)
   - Recherchez des professeurs (si recruteur)
   - Envoyez des messages
   - Consultez les notifications

## 📋 Fonctionnalités futures

- [ ] Système d'offres d'emploi
- [ ] Candidatures en ligne
- [ ] Évaluations et avis
- [ ] Calendrier de disponibilité
- [ ] Vidéoconférence intégrée
- [ ] Upload de CV et documents
- [ ] Système de recommandations
- [ ] Export de données
- [ ] API REST pour intégration
- [ ] Application mobile

## 🐛 Dépannage

### Les données ne se sauvegardent pas
- Vérifiez que votre navigateur autorise le localStorage
- Essayez en navigation privée pour réinitialiser

### Les notifications ne s'affichent pas
- Vérifiez que vous êtes bien connecté
- Actualisez la page

### Impossible de contacter un professeur
- Vérifiez que vous êtes connecté en tant que recruteur
- Un professeur ne peut pas se contacter lui-même

## 📄 Licence

Ce projet est fourni à des fins éducatives et de démonstration.

## 👥 Contribution

Pour améliorer cette plateforme :
1. Identifiez les fonctionnalités à ajouter
2. Testez les modifications
3. Documentez les changements

---

**EduRecruit** - Connecter les talents éducatifs avec les opportunités
