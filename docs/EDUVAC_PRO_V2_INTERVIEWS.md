# Workflow des entretiens — EduVac Pro V2

## Parcours
1. L’établissement présélectionne une candidature.
2. Un membre autorisé de l’établissement planifie un entretien avec date/heure, format (présentiel, en ligne ou téléphone), lieu/lien et notes internes.
3. La candidature passe au statut « interview ».
4. L’enseignant concerné peut confirmer, décliner ou demander un autre créneau. Une demande de report doit comporter une note.
5. L’établissement peut annuler un entretien encore planifié.

## Sécurité
- La planification est réservée aux membres de l’établissement concerné et aux administrateurs plateforme.
- Seul le compte enseignant propriétaire de la candidature peut répondre.
- Les notes internes de l’établissement ne sont pas affichées à l’enseignant.
- Les réponses sont horodatées ; un entretien ayant déjà reçu une réponse ne peut pas être répondu une seconde fois via le formulaire.
- Les routes sont authentifiées, vérifiées et limitées par rôle ; les policies vérifient aussi la relation métier.

## Limites fonctionnelles actuelles
- La demande de report est enregistrée, mais ne replanifie pas automatiquement le rendez-vous : l’établissement doit proposer une nouvelle date.
- Les notifications e-mail/SMS/WhatsApp ne sont pas encore branchées.
- La gestion des fuseaux horaires est basée sur le fuseau applicatif Laravel ; configurer celui-ci sur Africa/Dakar pour le pilote.
- Les tests sont écrits mais n'ont pas été exécutés dans cette session.

## Migration
Après intégration des fichiers dans un squelette Laravel 12, exécuter :

```bash
php artisan migrate
php artisan test --filter=InterviewWorkflowTest
```
