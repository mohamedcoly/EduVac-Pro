<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mot de passe oublié — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0;padding:8vh 18px}.card{max-width:480px;margin:auto;background:white;padding:28px;border:1px solid #e3e8f1;border-radius:16px}label{display:block;margin:18px 0 7px;font-weight:650}input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #d5dce8;border-radius:8px}button{margin-top:18px;background:#1d4ed8;color:white;padding:12px 16px;border:0;border-radius:8px;font-weight:700;cursor:pointer}.muted{color:#64748b;line-height:1.6}.error{color:#b42318}.success{color:#067647}</style></head>
<body><main class="card"><h1>Mot de passe oublié ?</h1><p class="muted">Indiquez l’adresse e-mail associée à votre compte. Nous vous enverrons un lien de réinitialisation.</p>
@if(session('status'))<p class="success">{{ session('status') }}</p>@endif
<form method="POST" action="{{ route('password.email') }}">@csrf
<label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
@error('email')<p class="error">{{ $message }}</p>@enderror
<button type="submit">Envoyer le lien</button></form><p><a href="{{ route('login') }}">Retour à la connexion</a></p></main></body></html>
