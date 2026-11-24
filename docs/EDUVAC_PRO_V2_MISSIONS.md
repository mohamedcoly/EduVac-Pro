# Propositions de mission — EduVac Pro V2

## Déclenchement
Une proposition de mission ne peut être créée que lorsque la candidature est au statut `selected`. Une contrainte unique sur `missions.application_id` empêche la création de plusieurs missions pour une même candidature.

## Conditions formalisées
L’établissement renseigne :
- dates de début et de fin (fin facultative) ;
- volume horaire hebdomadaire ;
- rémunération en FCFA et périodicité (heure, semaine, mois ou forfait) ;
- description des responsabilités ;
- horaires et lieu de travail.

La proposition reste au statut `planned` et en attente de réponse. L’enseignant propriétaire de la candidature peut accepter ou décliner. L’acceptation fait passer la mission à `active`; le refus la passe à `cancelled`. La réponse est horodatée.

## Contrôles
- Seuls les membres de l’établissement associé à l’offre peuvent proposer une mission.
- Seul l’enseignant concerné peut répondre.
- Une mission est liée à une candidature unique.
- Les champs de rémunération sont exprimés en FCFA ; aucun paiement n’est encaissé ou exécuté par cette fonctionnalité.

## Hors périmètre actuel
- Génération et signature électronique de contrat.
- Paiement des salaires / commissions.
- Gestion des présences, feuilles d’heures et litiges.
- Clôture opérationnelle et évaluations réciproques (étape ultérieure).

## Migration et tests
```bash
php artisan migrate
php artisan test --filter=MissionWorkflowTest
```
Les tests sont écrits mais doivent être exécutés dans l’environnement Laravel du projet.
