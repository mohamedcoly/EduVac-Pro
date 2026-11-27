<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Administration — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0}.wrap{max-width:1000px;margin:auto;padding:34px 20px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-top:20px}button{padding:10px 15px;border:0;border-radius:8px;background:#1d4ed8;color:white;font-weight:700}</style></head>
<body><main class="wrap"><h1>Administration EduVac Pro</h1><p>Connecté en tant que {{ $user->email }}</p><p><a href="{{ route('notifications.index') }}">Notifications ({{ $user->unreadNotifications()->count() }})</a></p><div class="card"><h2>Console de gestion</h2><p>Les outils de vérification, modération et suivi des abonnements seront ajoutés dans les modules suivants.</p></div>
<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Se déconnecter</button></form>
</main></body></html>
