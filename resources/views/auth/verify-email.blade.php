<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Vérifier votre e-mail — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0;padding:8vh 18px}.card{max-width:560px;margin:auto;background:white;padding:30px;border:1px solid #e3e8f1;border-radius:16px}button{background:#1d4ed8;color:white;padding:12px 16px;border:0;border-radius:8px;font-weight:700;cursor:pointer}.muted{color:#64748b;line-height:1.6}</style></head>
<body><div class="card"><h1>Vérifiez votre adresse e-mail</h1><p class="muted">Un lien de vérification vous a été envoyé. Ouvrez-le pour confirmer votre adresse et accéder à votre espace EduVac Pro.</p>
@if(session('status') === 'verification-link-sent')<p>Un nouveau lien vient d'être envoyé.</p>@endif
<form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit">Renvoyer le lien</button></form>
<form method="POST" action="{{ route('logout') }}" style="margin-top:16px">@csrf<button type="submit" style="background:#475569">Se déconnecter</button></form>
</div></body></html>
