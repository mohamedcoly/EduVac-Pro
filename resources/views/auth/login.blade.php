<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — EduVac Pro</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f6fb;color:#172033;font-family:Inter,system-ui,sans-serif}
        .wrap{max-width:480px;margin:9vh auto;padding:0 18px}.brand{font-weight:800;font-size:22px;margin-bottom:22px}
        .panel{background:#fff;border:1px solid #e3e8f1;border-radius:18px;padding:30px;box-shadow:0 12px 36px #1720330a}
        h1{margin:0 0 8px;font-size:28px;letter-spacing:-.04em}.muted{color:#64748b;line-height:1.5}
        label{display:block;font-size:14px;font-weight:650;margin:17px 0 7px}input[type=email],input[type=password]{width:100%;padding:12px 13px;border:1px solid #d5dce8;border-radius:9px;font:inherit}
        button{width:100%;margin-top:22px;background:#1d4ed8;color:#fff;border:0;padding:14px;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer}
        .error{color:#b42318;font-size:13px;margin-top:5px}.footer{text-align:center;color:#64748b;margin-top:18px;font-size:14px}
        a{color:#1d4ed8;text-decoration:none}.alert{background:#ecfdf3;color:#067647;padding:12px;border-radius:8px;margin:14px 0}
    </style>
</head>
<body><div class="wrap">
    <div class="brand">EduVac Pro</div>
    <div class="panel">
        <h1>Bon retour</h1><p class="muted">Connectez-vous à votre espace EduVac Pro.</p>
        @if(session('status'))<div class="alert">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')<div class="error">{{ $message }}</div>@enderror
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            <label style="font-weight:400"><input type="checkbox" name="remember" value="1"> Rester connecté</label>
            <button type="submit">Se connecter</button>
        </form>
    </div>
    <div class="footer">Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a></div>
</div></body></html>
