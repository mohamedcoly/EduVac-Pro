<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Espace établissement — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0}.wrap{max-width:1000px;margin:auto;padding:34px 20px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-top:20px}.muted{color:#64748b}button{padding:10px 15px;border:0;border-radius:8px;background:#1d4ed8;color:white;font-weight:700}</style></head>
<body><main class="wrap"><h1>Bienvenue {{ $user->name }}</h1><p class="muted">Espace établissement EduVac Pro</p>
<div class="card"><h2>{{ $school?->name ?? 'Votre établissement' }}</h2><p>Statut : <strong>{{ $school?->verification_status ?? 'Non configuré' }}</strong></p>
@if($school?->verification_status !== 'verified')
<p>Votre établissement doit être vérifié par l’équipe EduVac Pro avant de pouvoir publier des offres.</p>
@else
<p>Votre établissement est vérifié. La gestion des offres et des candidatures sera disponible dans les prochains modules.</p>
@endif
</div>
<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Se déconnecter</button></form>
</main></body></html>
