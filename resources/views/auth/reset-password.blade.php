<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Réinitialiser le mot de passe — EduVac Pro</title>
<style>body{font-family:system-ui;background:#f4f6fb;color:#172033;margin:0;padding:8vh 18px}.card{max-width:480px;margin:auto;background:white;padding:28px;border:1px solid #e3e8f1;border-radius:16px}label{display:block;margin:18px 0 7px;font-weight:650}input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #d5dce8;border-radius:8px}button{margin-top:18px;background:#1d4ed8;color:white;padding:12px 16px;border:0;border-radius:8px;font-weight:700;cursor:pointer}.muted{color:#64748b}.error{color:#b42318}</style></head>
<body><main class="card"><h1>Nouveau mot de passe</h1><p class="muted">Choisissez un mot de passe d’au moins 10 caractères, avec majuscules, minuscules et chiffres.</p>
<form method="POST" action="{{ route('password.update') }}">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
@error('email')<p class="error">{{ $message }}</p>@enderror
<label for="password">Nouveau mot de passe</label><input id="password" type="password" name="password" autocomplete="new-password" required>
@error('password')<p class="error">{{ $message }}</p>@enderror
<label for="password_confirmation">Confirmer le mot de passe</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
<button type="submit">Réinitialiser</button></form></main></body></html>
