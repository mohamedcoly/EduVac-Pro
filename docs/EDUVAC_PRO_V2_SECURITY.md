# Autorisations et modération — EduVac Pro V2

## Principes

L'authentification prouve l'identité du compte. Les Policies déterminent ensuite si ce compte peut consulter ou modifier une ressource. Les rôles globaux ne remplacent jamais la vérification de l'appartenance à un établissement.

## Matrice d'accès

| Ressource / action | Enseignant | Membre d'école | Administrateur |
|---|---|---|---|
| Voir son propre profil | Oui | — | Oui |
| Voir un profil enseignant | — | Seulement si profil vérifié et école vérifiée | Oui |
| Modifier son profil enseignant | Son profil uniquement | Non | Oui |
| Consulter une école | Non | Écoles dont il est membre | Toutes |
| Modifier une école | Non | Propriétaire / gestionnaire | Toutes |
| Valider une école | Non | Non | Oui |
| Créer une offre | Non | Membre d'une école vérifiée | Oui |
| Modifier une offre | Non | Membre de l'école concernée | Oui |
| Postuler | Profil enseignant vérifié, disponible et offre ouverte | Non | Contrôle de modération |
| Consulter une candidature | Sa candidature | Membres de l'école de l'offre | Oui |
| Gérer une candidature | Retrait selon statut | Membres de l'école concernée | Oui |
| Valider un profil enseignant | Non | Non | Oui |

## Policies enregistrées

- SchoolPolicy : lecture, modification, suppression et validation d'un établissement.
- TeacherProfilePolicy : visibilité du profil et validation administrative.
- JobOfferPolicy : accès aux offres, création, modification, publication et suppression.
- ApplicationPolicy : lecture, gestion par l'école et retrait par le candidat.

Les Policies sont déclarées dans AppServiceProvider.

## Scopes de listing

Les Policies contrôlent l'accès à une ressource donnée. Elles ne filtrent pas automatiquement une collection. Les listes doivent donc utiliser les scopes suivants :

- School::visibleTo($user)
- JobOffer::visibleTo($user)
- TeacherProfile::visibleTo($user)

Un contrôleur ne doit pas remplacer ces scopes par un simple Model::all().

## Modération

Les routes PATCH suivantes sont protégées par auth, verified et l'ability verify de la Policy correspondante :

- admin.schools.verification
- admin.teachers.verification

Le contrôleur de modération :
1. vérifie à nouveau l'autorisation ;
2. valide le nouveau statut ;
3. enregistre la décision ;
4. écrit une entrée dans audit_logs avec l'auteur, la ressource, l'adresse IP et le motif.

## Règles à respecter dans les prochains contrôleurs

- Appeler authorize() pour chaque ressource chargée ou modifiée.
- Charger une ressource dans le périmètre de l'utilisateur quand c'est possible (ex. school->jobOffers()->findOrFail(id)).
- Ne jamais accepter school_id, created_by, teacher_profile_id, role ou un statut de validation sans contrôle serveur.
- Une école pending, rejected ou suspended ne peut pas publier une offre.
- Un enseignant non vérifié ne peut pas postuler.
- Les documents doivent être téléchargés via un contrôleur privé qui vérifie la Policy ; ne pas exposer directement storage/app/private.

## Limites de validation

Les fichiers ont été poussés sur la branche GitHub. Ils n'ont pas été exécutés dans un environnement Laravel/MySQL pendant cette session. Les tests automatisés, la validation des migrations, la revue des requêtes de listing et le test d'intrusion restent obligatoires avant toute mise en production.
