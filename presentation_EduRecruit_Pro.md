# Présentation du Projet : EduVac Pro

Ce document contient le contenu rédigé pour chaque diapositive de votre PowerPoint. Vous pouvez copier-coller ces textes directement dans vos slides.

---

## Diapo 1. Page de garde
- **Titre du projet** : EduVac Pro - Plateforme de mise en relation pour vacations pédagogiques
- **Présenté par** : [Votre Nom / Noms des membres du groupe]
- **Filière** : Licence 2 Informatique
- **Encadreur / Tuteur** : [Nom de votre tuteur]
- **Année universitaire** : 2025-2026

---

## Diapo 2. Contexte et présentation du projet
- **Présentation générale** : EduVac Pro est une application web (SaaS) décentralisée permettant de connecter directement des établissements scolaires avec des enseignants pour des missions de remplacement.
- **Pourquoi ce choix ?** : Le domaine éducatif souffre souvent de lenteurs administratives pour pallier l'absence soudaine d'un professeur. La digitalisation de ce secteur est une nécessité.
- **Besoin ciblé** : Fluidifier la recherche d'opportunités pour les professeurs et simplifier le processus de recrutement d'urgence pour les écoles, tout en centralisant les communications et l'administration.

---

## Diapo 3. Problématique
- **Difficulté à résoudre** : Comment réduire le temps et la complexité administrative liés au recrutement de professeurs remplaçants tout en garantissant un suivi sécurisé ?
- **Problèmes sous-jacents** : 
  - Manque de visibilité sur les disponibilités des enseignants en temps réel.
  - Lourdeur de la création des contrats de vacation.
- **Importance du sujet** : En informatique, la transformation numérique de l'administration (EdTech) est un enjeu majeur car elle automatise des tâches répétitives pour laisser plus de temps à l'humain et à l'éducation.

---

## Diapo 4. Outils et technologies utilisés
- **Langages de programmation** : HTML5, CSS3 (Vanilla), JavaScript (ES6+ mode asynchrone).
- **Frameworks frontend** : Tailwind CSS (pour la structure responsive et la mise en page rapide).
- **Base de données & Backend** : Supabase (Backend-as-a-Service basé sur PostgreSQL), gérant l'authentification sécurisée, la base de données Temps Réel et le stockage (Storage pour les CV).
- **Bibliothèques tierces** : `html2pdf.js` (génération automatique des contrats PDF), `DiceBear API` (génération d'avatars dynamiques).

---

## Diapo 5. Analyse et conception
- **Besoins fonctionnels** :
  - **Pour l'École** : Publier des offres, gérer les candidatures, générer des contrats PDF, évaluer l'enseignant.
  - **Pour l'Enseignant** : Renseigner son profil et ses disponibilités, uploader un CV, postuler en 1 clic.
  - **Commun** : Système de messagerie instantanée (Chat) intégré.
- **Besoins non fonctionnels** : Plateforme "Responsive" (adaptée aux mobiles), ultra-rapide (pas de rechargement long), architecture sécurisée (Authentification avec Token).
- **Architecture** : Séparation stricte des préoccupations (Fichiers HTML pour la structure, CSS sémantique pour l'interface utilisateur, scripts JS modulaires pour la logique métier).

---

## Diapo 6. Réalisation du projet
- **Solution développée** : Une interface moderne basée sur le "Glassmorphism" et des tableaux de bord interactifs sans rechargement de page (Single Page Application like).
- **Fonctionnalités au cœur de la démo** :
  1. Suivi du statut des candidatures en direct (En attente, Accepté, Refusé).
  2. Messagerie intégrée et génération automatique du PDF "Accord de Vacation Pedagogique".
  3. Système d'étoiles (Rating) pour évaluer la mission.
- *(Note pour la présentation : À ce moment, passez sur votre navigateur pour faire une démonstration de 2 minutes).*

---

## Diapo 7. Difficultés rencontrées
- **Problèmes techniques** : 
  - Gérer l'asynchronisme en JavaScript (Promesses, requêtes `async/await`) avec la base de données distante Supabase.
  - Maintenir un code propre sans utiliser de gros Framework JS (comme React ou Vue) face à des interfaces dynamiques et réactives complexes.
- **Contraintes** : L'implémentation de la génération de fichiers PDF multi-navigateurs depuis le simple HTML.
- **Solutions apportées** : Refactoring majeur pour isoler la logique (`auth.js`, `dashboard_recruiter.js`, `dashboard_teacher.js`) et centraliser la charte graphique dans un seul fichier de styles CSS sémantiques.

---

## Diapo 8. Bilan du projet
- **Ce que nous avons appris** : L’intégration et la manipulation d’un Backend-as-a-Service (Supabase) via API, et la modélisation d'une base de données relationnelle (PostgreSQL).
- **Compétences développées** : Gestion fine de l'UI/UX (Expérience/Interface Utilisateur), appels asynchrones avec JavaScript, et versionnement/organisation du code.
- **Apport du projet** : Ce projet constitue un pont solide entre la théorie algorithmique de L2 et les besoins très pratiques de l'industrie (Full-Stack web development).

---

## Diapo 9. Limites et perspectives
- **Limites actuelles** : 
  - Absence de notifications push natives.
  - Manque d'intégration avec une passerelle de paiement tierce pour générer les fiches de paie.
- **Améliorations possibles** : 
  - Module d'administration complet pour valider les diplômes des professeurs manuellement.
- **Suites à donner** : Migrer la logique pure JS vers un Framework moderne comme React ou Next.js pour garantir la scalabilité, et pourquoi pas en faire une application mobile avec React Native.

---

## Diapo 10. Conclusion
- **Résumé global** : EduVac Pro répond de manière élégante et ergonomique à un problème réel des directions académiques.
- **Importance du projet** : Il prouve qu'avec les outils cloud modernes, un besoin concret peut être adressé rapidement, avec une logique claire de bout en bout (du Frontend au Backend).
- **Ouverture** :  inclure de l'intelligence artificielle capable de recommander le meilleur CV pour une mission précise.

**Merci pour votre attention !**  
Avez-vous des questions ?
