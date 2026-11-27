<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Notifications — EduVac Pro</title>
    <style>
        body{font-family:system-ui,sans-serif;background:#f4f6f8;color:#17212b;margin:0}
        .wrap{max-width:900px;margin:35px auto;padding:0 18px}
        .top{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
        .card{background:#fff;border:1px solid #e1e7ed;border-radius:12px;padding:20px;margin:14px 0}
        .new{border-left:4px solid #4f46e5;background:#f8f8ff}
        button,a{color:#4f46e5;font-weight:600}button{background:none;border:0;cursor:pointer;font:inherit}
        .muted{color:#64748b;font-size:.88rem}.flash{padding:12px;background:#ecfdf5;border-radius:8px;color:#047857}
        .actions{display:flex;gap:14px;align-items:center}
    </style>
</head>
<body>
<main class="wrap">
    <div class="top">
        <div>
            <h1>Notifications</h1>
            <p class="muted">Suivez les nouvelles candidatures, entretiens et missions.</p>
        </div>
        <div class="actions">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            @if($notifications->getCollection()->contains(fn ($item) => is_null($item->read_at)))
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Tout marquer comme lu</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('status'))<p class="flash" role="status">{{ session('status') }}</p>@endif

    @forelse($notifications as $notification)
        <article class="card {{ $notification->read_at ? '' : 'new' }}">
            <div class="top">
                <div>
                    <h2>{{ $notification->data['title'] ?? 'Notification EduVac Pro' }}</h2>
                    <p>{{ $notification->data['message'] ?? '' }}</p>
                    <p class="muted">{{ $notification->created_at?->diffForHumans() }}
                        @unless($notification->read_at) · Non lue @endunless
                    </p>
                </div>
                @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit">Consulter</button>
                    </form>
                @else
                    <a href="{{ $notification->data['url'] ?? route('dashboard') }}">Ouvrir</a>
                @endunless
            </div>
        </article>
    @empty
        <div class="card">
            <h2>Vous êtes à jour.</h2>
            <p class="muted">Vos notifications apparaîtront ici lorsqu'une activité vous concernera.</p>
        </div>
    @endforelse

    {{ $notifications->links() }}
</main>
</body>
</html>
