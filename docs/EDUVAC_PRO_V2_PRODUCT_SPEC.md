# Cahier des charges fonctionnel — EduVac Pro V2

## Objectif
Réduire le délai de recrutement d'un professeur vacataire en permettant à une école de publier un besoin, identifier des profils pertinents et suivre tout le processus jusqu'à la mission.

## Acteurs et droits
### Enseignant
Créer un compte, vérifier son email, compléter son profil, déclarer matières/niveaux/expériences, définir disponibilités et zones de mobilité, charger CV et justificatifs privés, consulter les offres, candidater, suivre le statut et répondre aux invitations.

### Établissement
Créer un compte établissement, soumettre ses informations de vérification, inviter des collaborateurs, publier/modifier/archiver des offres, consulter les candidatures, filtrer et comparer les profils, planifier des entretiens, sélectionner un candidat, créer une mission et évaluer la collaboration.

### Administrateur
Vérifier les établissements et profils selon une procédure documentée, modérer offres/avis, traiter signalements, suspendre des comptes, gérer plans et consulter le journal d'audit.

## Modules
1. Authentification : inscription distincte enseignant/école, connexion, déconnexion, email vérifié, mot de passe oublié, sessions sécurisées.
2. Onboarding : formulaire progressif, indicateur de complétude, statut de vérification.
3. Répertoire enseignants : recherche par matière, niveau, expérience, disponibilité, zone et statut de vérification.
4. Offres : titre, description, matière, niveau, volume horaire, lieu, type de contrat, rémunération indicative facultative, date de début, date limite et statut.
5. Candidatures : candidature unique par enseignant/offre, message, CV choisi, statuts submitted/shortlisted/interview/s
elected/rejected/withdrawn.
6. Entretiens : date, format, lieu/lien, notes internes et notification.
7. Missions : dates, volume horaire, établissement, enseignant, statut et bilan.
8. Disponibilités : plages récurrentes et indisponibilités ponctuelles.
9. Matching : score explicable calculé côté serveur à partir des critères configurés ; l'école garde la décision.
10. Avis : note et commentaire après mission terminée ; modération et droit de réponse.
11. Abonnements : plans, période, statut, limites, factures/reçus ; intégration de paiement dans une phase séparée après choix du prestataire.
12. Notifications : centre de notifications et emails transactionnels.
13. Administration : vérification, modération, suspension, métriques et audit.

## Statuts essentiels
- school: pending, verified, rejected, suspended.
- teacher: incomplete, pending_review, verified, rejected, suspended.
- offer: draft, published, paused, closed, archived.
- application: submitted, shortlisted, interview, selected, rejected, withdrawn.
- mission: planned, active, completed, cancelled.
- subscription: trial, active, past_due, cancelled, expired.

## Règles métier
- Les écoles sont les clients payants ; l'accès enseignant reste gratuit au lancement.
- Seuls les établissements vérifiés publient des offres.
- Une candidature est unique par couple enseignant/offre.
- Les candidatures restent consultables par leur auteur et l'établissement propriétaire de l'offre.
- Les données de contact détaillées ne sont révélées qu'au stade autorisé du processus.
- Un enseignant ne peut pas s'auto-valider.
- Les avis sont possibles uniquement après une mission terminée.
- Les limites de plan sont contrôlées côté serveur.
- Les actions sensibles sont inscrites dans un audit log.

## Indicateurs produit
Nombre d'écoles vérifiées, offres actives, enseignants vérifiés, délai moyen de présélection, candidatures par offre, taux de conversion offre→mission, rétention des écoles et revenu mensuel récurrent.

## Adaptation locale
Interface en français, FCFA, fuseau Africa/Dakar, téléphone au format international, villes et zones administratives du Sénégal, faible consommation de données, formulaires simples et responsive.