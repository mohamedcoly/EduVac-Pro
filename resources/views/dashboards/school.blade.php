<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Espace établissement — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0}.wrap{max-width:1000px;margin:auto;padding:34px 20px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-top:20px}.muted{color:#64748b}.links{display:flex;gap:12px;flex-wrap:wrap}a,button{display:inline-block;padding:10px 15px;border:0;border-radius:8px;background:#1d4ed8;color:white;text-decoration:none;font-weight:700}</style></head>
<body><main class="wrap"><h1>Bienvenue {{ $user->name }}</h1><p class="muted">Espace établissement EduVac Pro</p>
<div class="card"><h2>{{ $school?->name ?? 'Votre établissement' }}</h2><p>Statut : <strong>{{ $school?->verification_status ?? 'Non configuré' }}</strong></p>
@if($school?->verification_status !== 'verified')
<p>Votre établissement doit être vérifié par l’équipe EduVac Pro avant de pouvoir publier des offres.</p>
@else
<p>Votre établissement est vérifié. Vous pouvez gérer vos recrutements depuis cet espace.</p>
<div class="links"><a href="{{ route('school-offers.index') }}">Mes offres</a><a href="{{ route('school-offers.create') }}">Publier un besoin</a><a href="{{ route('school-teachers.index') }}">Rechercher un enseignant</a><a href="{{ route('school-applications.index') }}">Candidatures reçues</a></div>
@endif</div>
<form method="POST" action="{{ route('logout') }}" style="margin-top:20px">@csrf<button type="submit">Se déconnecter</button></form>
</main></body></html>
