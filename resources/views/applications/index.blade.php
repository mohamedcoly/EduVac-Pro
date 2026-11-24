<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Candidatures — EduVac Pro</title>
<style>
body{font-family:system-ui;background:#f4f6f8;color:#17212b}.wrap{max-width:1100px;margin:35px auto;padding:0 18px}
.card{background:white;border:1px solid #e1e7ed;border-radius:12px;padding:20px;margin:14px 0}
button{background:#176b55;color:white;padding:9px 13px;border:0;border-radius:7px;cursor:pointer}
select,input,textarea{display:block;max-width:100%;padding:9px;margin:7px 0 12px;border:1px solid #cbd5df;border-radius:7px}
form{margin-top:12px}.muted{color:#586675}
</style>
</head>
<body><main class="wrap">
<h1>{{ auth()->user()->hasRole('teacher') ? 'Mes candidatures' : 'Candidatures reçues' }}</h1>
@if(session('status'))<p role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="card"><strong>Vérifiez les informations :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@forelse($applications as $application)
<article class="card">
<h2>{{ $application->jobOffer->title }}</h2>
<p>{{ $application->jobOffer->school->name }} · {{ $application->jobOffer->city }} · {{ $application->jobOffer->subject?->name }}</p>
<p>Enseignant : {{ $application->teacherProfile->first_name }} {{ $application->teacherProfile->last_name }}</p>
<p>Envoyée le {{ $application->submitted_at?->format('d/m/Y à H:i') }} · Statut : <strong>{{ $application->status }}</strong></p>
@if($application->cover_message)<p>{{ $application->cover_message }}</p>@endif
@if(auth()->user()->hasAnyRole(['school_manager','school_recruiter','platform_admin']))
    @if($application->teacherDocument)
        <p><a href="{{ route('teacher-documents.download',$application->teacherDocument) }}">Consulter le CV</a></p>
    @endif
    <form method="POST" action="{{ route('school-applications.status',$application) }}">
        @csrf @method('PATCH')
        <label>Statut de la candidature</label>
        <select name="status"><option value="shortlisted">Présélectionner</option><option value="interview">Entretien</option><option value="selected">Retenir</option><option value="rejected">Rejeter</option></select>
        <button>Mettre à jour</button>
    </form>
    @if(in_array($application->status, ['shortlisted','interview'], true))
    <form method="POST" action="{{ route('school-interviews.store',$application) }}">
        @csrf
        <h3>Planifier un entretien</h3>
        <label>Date et heure</label><input type="datetime-local" name="scheduled_at" min="{{ now()->format('Y-m-d\TH:i') }}" required>
        <label>Format</label><select name="format" required><option value="in_person">Présentiel</option><option value="online">En ligne</option><option value="phone">Téléphonique</option></select>
        <input name="location_or_link" maxlength="500" placeholder="Adresse ou lien de visioconférence">
        <textarea name="internal_notes" maxlength="3000" placeholder="Notes internes (non visibles par l’enseignant)"></textarea>
        <button>Planifier l’entretien</button>
    </form>
    @endif
@elseif($application->status==='submitted'||$application->status==='shortlisted')
    <form method="POST" action="{{ route('applications.withdraw',$application) }}">@csrf @method('PATCH')<button>Retirer ma candidature</button></form>
@endif
@if($application->interviews->isNotEmpty())
    <h3>Entretiens</h3>
    @foreach($application->interviews->sortByDesc('scheduled_at') as $interview)
        @include('interviews.partials.card', ['interview' => $interview])
    @endforeach
@endif
</article>
@empty<div class="card">Aucune candidature pour le moment.</div>@endforelse
{{ $applications->links() }}
</main></body></html>