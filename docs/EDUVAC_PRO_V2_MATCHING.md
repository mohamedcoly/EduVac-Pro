# EduVac Pro V2 — Moteur de matching

## Pondération

Le moteur classe les profils enseignants vérifiés et disponibles selon un score sur 100 :

| Critère | Points | Règle actuelle |
|---|---:|---|
| Matière | 30 | 30 si la matière de l’offre est enseignée |
| Niveau | 25 | 25 si le niveau de l’offre est couvert |
| Localisation | 20 | 20 si la ville correspond ; 16 si la ville figure dans les zones de mobilité déclarées |
| Disponibilité | 15 | 15 si l'enseignant est disponible et a renseigné son planning ; 8 si disponible sans planning |
| Expérience | 10 | 10 à partir de 5 ans ; 8 à partir de 3 ans ; 5 à partir d'un an ; 2 en début de parcours |

Le moteur ne retourne que les enseignants dont le profil est vérifié et dont le statut de disponibilité est actif. Les profils sont classés par score décroissant. Une ville différente sans mobilité déclarée n'obtient aucun point géographique.

## Limites connues et améliorations prévues

- Les offres ne comportent pas encore de jours ou plages horaires obligatoires. Les 15 points de disponibilité mesurent donc le statut et la présence d'un planning, pas un chevauchement horaire réel.
- Les coordonnées géographiques des enseignants ne sont pas collectées. La localisation utilise la ville et les zones de mobilité déclarées ; aucun calcul de distance GPS n'est prétendu.
- Le niveau d'expérience est une règle métier initiale à calibrer avec les établissements pilotes.
- Le score est une aide au tri, pas une décision automatique de recrutement. Le recruteur doit examiner le CV et conduire un entretien.

## Accès

Le matching est disponible depuis la liste des offres dans l'espace établissement, pour les rôles établissement et administration. La policy de l'offre contrôle l'accès à la fiche avant le calcul.
