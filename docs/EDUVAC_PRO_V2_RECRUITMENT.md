# EduVac Pro V2 — Parcours de recrutement

## Parcours établissement

1. Un compte ayant le rôle `school_manager` ou `school_recruiter` doit être membre d'un établissement vérifié.
2. L'établissement crée une offre en brouillon avec matière, niveau, ville, description, volume horaire, rémunération et dates.
3. Un membre autorisé publie l'offre. Les enseignants peuvent alors la consulter.
4. L'établissement recherche des profils enseignants vérifiés et disponibles par nom, ville, matière, niveau et expérience.
5. Les candidatures reçues sont consultables depuis l'espace établissement. Le CV reste sur le disque privé et son accès est contrôlé par policy.
6. Le recruteur peut mettre à jour le statut : présélection, entretien, retenu ou rejeté.

## Parcours enseignant

1. L'enseignant complète son profil, ajoute matières, niveaux, disponibilités et CV.
2. Il peut parcourir les offres publiées par des établissements vérifiés.
3. Pour candidater, son profil doit être vérifié et disponible.
4. La candidature est liée à son profil et à son CV privé. Une contrainte unique en base empêche deux candidatures au même poste par le même profil.
5. L'enseignant consulte ses candidatures et peut retirer une candidature encore soumise ou présélectionnée.

## Routes principales

- `/etablissement/offres` : gestion des offres.
- `/etablissement/enseignants` : annuaire des enseignants vérifiés.
- `/etablissement/candidatures` : suivi des candidatures reçues.
- `/offres` : offres disponibles pour les enseignants.
- `/offres/{offer}` : détail et candidature.
- `/mes-candidatures` : suivi des candidatures de l'enseignant.

## Contrôles d'accès

- Les requêtes d'offres côté établissement sont limitées aux établissements dont l'utilisateur est membre.
- Les enseignants peuvent consulter les offres publiques des établissements vérifiés, même avant la validation de leur profil ; la candidature reste bloquée jusqu'à validation.
- L'annuaire ne retourne que les profils enseignants vérifiés.
- Le téléchargement du CV est autorisé au propriétaire, à l'administrateur ou à un membre d'établissement concerné par une candidature liée.
- Le backend contrôle le propriétaire du CV lors du dépôt de candidature ; un identifiant de document appartenant à un autre enseignant est refusé.

## Validation restant à effectuer

Les tests automatisés ont été ajoutés, mais doivent être exécutés dans un environnement Laravel configuré avec PHP, Composer et MySQL ou SQLite. Il reste également à effectuer une recette fonctionnelle avec un compte enseignant, un compte établissement vérifié et un compte administrateur.
