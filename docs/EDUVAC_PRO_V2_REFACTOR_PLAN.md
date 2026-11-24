# Plan de refonte du dépôt — EduVac Pro V2

## État de départ
Le dépôt master contient un prototype HTML/CSS/JavaScript connecté à Supabase. Il ne constitue pas encore une application SaaS Laravel. Des règles SQL de démonstration désactivent RLS et ouvrent l'accès aux CV ; les fonctions privilégiées ne vérifient pas correctement le rôle administrateur. L'inscription peut également marquer un profil comme validé immédiatement.

## Stratégie
- Travailler dans la branche `refonte/laravel-v2`.
- Conserver `master` intact jusqu'à validation.
- Ne pas migrer les mots de passe ni réutiliser les tokens Supabase.
- Recréer les comptes via le système de réinitialisation/invitation Laravel ; exporter uniquement les données métier nécessaires après audit.
- Conserver une copie chiffrée et vérifiée de la base existante avant toute migration.
- Ne jamais exécuter un script destructif directement sur la base de production.

## Correspondance prototype → V2
| Prototype actuel | Cible Laravel |
|---|---|
| Supabase Auth | Laravel Breeze |
| profiles | users + teacher_profiles |
| teacher_details | teacher_profiles + qualifications |
| recruiter dashboard | school workspace |
| missions / offers | job_offers + missions |
| applications | applications |
| CV Supabase Storage | private Laravel storage |
| RPC admin SECURITY DEFINER | Admin Policies + contrôleurs autorisés |
| RLS désactivé en mode démo | Policies Laravel + contraintes DB |

## Critères d'acceptation
- Un enseignant ne peut lire/modifier que son profil et ses candidatures.
- Un membre d'école ne peut agir que dans son établissement.
- Un établissement non vérifié ne peut pas publier d'offre.
- Une offre ne peut être modifiée que par son établissement propriétaire.
- Un CV n'est jamais accessible par URL publique.
- Le rôle platform_admin ne peut être attribué que par une procédure administrative sécurisée.
- Une école ne peut dépasser les limites de son plan via une requête HTTP forgée.
- Les transitions candidature → entretien → sélection → mission sont journalisées.
- Les tests couvrent les accès autorisés et interdits.

## Décisions à ne pas réintroduire
- Aucun mot de passe dans une table métier.
- Aucun secret Supabase/service-role dans le navigateur.
- Aucun statut de validation contrôlé par l'utilisateur.
- Aucun document candidat dans un espace public.
- Aucune autorisation reposant uniquement sur un bouton masqué ou une redirection JavaScript.
- Aucun paiement réel activé avant définition du prestataire, des webhooks et du rapprochement.

## Déploiement
Développer d'abord en local, configurer un .env séparé, exécuter migrations et tests sur une base de staging, vérifier les sauvegardes, puis déployer. Les identifiants et clés de production seront fournis uniquement dans l'environnement d'hébergement.