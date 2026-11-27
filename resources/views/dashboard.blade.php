<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord — EduVac Pro</title>
</head>
<body>
    <main>
        <nav aria-label="Navigation principale">
            <a href="{{ route('notifications.index') }}">Notifications ({{ auth()->user()->unreadNotifications()->count() }})</a>
        </nav>
        <h1>Bienvenue sur EduVac Pro</h1>
        <p>Votre espace sécurisé est prêt à être configuré.</p>
        <p>Connecté en tant que {{ auth()->user()->email }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Se déconnecter</button>
        </form>
    </main>
</body>
</html>
