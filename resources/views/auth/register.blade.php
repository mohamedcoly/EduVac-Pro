<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte — EduVac Pro</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f6fb;color:#172033;font-family:Inter,system-ui,sans-serif}
        .wrap{max-width:620px;margin:48px auto;padding:0 18px}.brand{font-weight:800;font-size:22px;margin-bottom:22px}
        .panel{background:#fff;border:1px solid #e3e8f1;border-radius:18px;padding:30px;box-shadow:0 12px 36px #1720330a}
        h1{margin:0 0 8px;font-size:28px;letter-spacing:-.04em}.muted{color:#64748b;line-height:1.5}
        label{display:block;font-size:14px;font-weight:650;margin:16px 0 7px}input,select{width:100%;padding:12px 13px;border:1px solid #d5dce8;border-radius:9px;font:inherit;background:#fff}
        .choices{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:20px 0}.choice{border:1px solid #d5dce8;border-radius:10px;padding:13px;cursor:pointer}.choice input{width:auto}
        button{width:100%;margin-top:22px;background:#1d4ed8;color:#fff;border:0;padding:14px;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer}
        .error{color:#b42318;font-size:13px;margin-top:5px}.alert{background:#fff1f0;color:#9f1c13;padding:12px;border-radius:8px;margin:14px 0}
        a{color:#1d4ed8;text-decoration:none}.footer{text-align:center;color:#64748b;margin-top:18px;font-size:14px}
        @media(max-width:520px){.panel{padding:22px}.choices{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="brand">EduVac Pro</div>
    <div class="panel">
        <h1>Créer votre compte</h1>
        <p class="muted">Rejoignez la plateforme de recrutement des professeurs vacataires au Sénégal.</p>
        @if ($errors->any())
            <div class="alert">Veuillez corriger les informations signalées.</div>
        @endif
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <label>Je souhaite créer un compte :</label>
            <div class="choices">
                <label class="choice"><input type="radio" name="account_type" value="teacher" {{ old('account_type','teacher') === 'teacher' ? 'checked' : '' }} onchange="toggleFields()"> Enseignant</label>
                <label class="choice"><input type="radio" name="account_type" value="school" {{ old('account_type') === 'school' ? 'checked' : '' }} onchange="toggleFields()"> Établissement</label>
            </div>
            @error('account_type')<div class="error">{{ $message }}</div>@enderror

            <label for="name">Nom complet du responsable / utilisateur</label>
            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
            @error('name')<div class="error">{{ $message }}</div>@enderror

            <div id="teacher-fields">
                <label for="first_name">Prénom</label><input id="first_name" name="first_name" value="{{ old('first_name') }}">
                @error('first_name')<div class="error">{{ $message }}</div>@enderror
                <label for="last_name">Nom</label><input id="last_name" name="last_name" value="{{ old('last_name') }}">
                @error('last_name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div id="school-fields" hidden>
                <label for="school_name">Nom de l'établissement</label><input id="school_name" name="school_name" value="{{ old('school_name') }}">
                @error('school_name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror

            <label for="phone">Téléphone</label><input id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="+221 77 000 00 00">
            @error('phone')<div class="error">{{ $message }}</div>@enderror

            <label for="city">Ville</label><input id="city" name="city" value="{{ old('city','Dakar') }}">
            @error('city')<div class="error">{{ $message }}</div>@enderror

            <label for="password">Mot de passe (10 caractères minimum)</label><input id="password" type="password" name="password" autocomplete="new-password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror

            <label for="password_confirmation">Confirmer le mot de passe</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>

            <button type="submit">Créer mon compte</button>
        </form>
    </div>
    <div class="footer">Vous avez déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></div>
</div>
<script>
function toggleFields(){
    const type=document.querySelector('input[name="account_type"]:checked')?.value;
    document.getElementById('teacher-fields').hidden=type!=='teacher';
    document.getElementById('school-fields').hidden=type!=='school';
}
toggleFields();
</script>
</body>
</html>
