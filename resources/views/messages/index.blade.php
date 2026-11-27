<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Messagerie EduVac Pro</title>
<style>body{font-family:system-ui;background:#f5f7fb;color:#172033;margin:0}.wrap{max-width:900px;margin:36px auto;padding:20px}.top,.item{background:#fff;border:1px solid #e5eaf2;border-radius:14px;padding:18px;margin-bottom:12px}.item{display:flex;justify-content:space-between;gap:20px}.muted{color:#64748b}a{color:#155eef;text-decoration:none;font-weight:600}</style></head>
<body><main class="wrap"><section class="top"><h1>Messagerie</h1><p class="muted">Échanges sécurisés entre enseignants et établissements, liés aux candidatures.</p><a href="{{ route('dashboard') }}">← Tableau de bord</a></section>
@if(session('status'))<p>{{ session('status') }}</p>@endif
@forelse($conversations as $conversation)
<article class="item"><div><strong>{{ $conversation->school->name ?? 'Établissement' }}</strong><p class="muted">Enseignant : {{ $conversation->teacherProfile->user->name ?? 'Profil' }}</p><small>{{ $conversation->last_message_at?->format('d/m/Y H:i') ?? 'Conversation créée' }}</small></div><div><a href="{{ route('messages.show',$conversation) }}">Ouvrir</a>@if($conversation->unread_count)<p>{{ $conversation->unread_count }} non lu(s)</p>@endif</div></article>
@empty<p>Aucune conversation pour le moment. Une conversation peut être ouverte depuis une candidature.</p>@endforelse
{{ $conversations->links() }}</main></body></html>
