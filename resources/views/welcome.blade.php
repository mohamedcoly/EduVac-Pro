<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EduVac Pro — Recrutement scolaire</title>
    <style>
        :root { color-scheme: light; font-family: Inter, system-ui, sans-serif; }
        body { margin: 0; background: #f6f8fc; color: #172033; }
        main { max-width: 1080px; margin: 0 auto; padding: 7vh 24px; }
        nav { display:flex; justify-content:space-between; align-items:center; }
        .brand { font-weight:800; letter-spacing:-.04em; font-size:1.35rem; }
        .hero { padding: 12vh 0 8vh; max-width: 760px; }
        .eyebrow { color:#2563eb; font-weight:700; text-transform:uppercase; letter-spacing:.12em; font-size:.78rem; }
        h1 { font-size:clamp(2.6rem,7vw,5rem); line-height:1.02; letter-spacing:-.06em; margin:.7rem 0 1.2rem; }
        p { color:#58647a; font-size:1.1rem; line-height:1.7; }
        a.cta { display:inline-block; background:#1d4ed8; color:white; text-decoration:none; padding:14px 22px; border-radius:10px; font-weight:700; margin-top:18px; }
        .meta { margin-top:70px; border-top:1px solid #dce2ed; padding-top:20px; color:#69758a; font-size:.9rem; }
    </style>
</head>
<body>
<main>
    <nav><div class="brand">EduVac Pro</div><span>Éducation · Recrutement · Sénégal</span></nav>
    <section class="hero">
        <div class="eyebrow">Le recrutement scolaire, simplifié</div>
        <h1>Trouvez le bon enseignant au bon moment.</h1>
        <p>EduVac Pro connecte les établissements scolaires avec des professeurs vacataires qualifiés et disponibles. Une plateforme pensée pour les réalités du secteur éducatif sénégalais.</p>
        <a class="cta" href="{{ route('register') }}">Créer un compte</a>
    </section>
    <div class="meta">Dakar, Sénégal · EduVac Pro V2</div>
</main>
</body>
</html>
