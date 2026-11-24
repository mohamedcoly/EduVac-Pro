<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon profil enseignant - EduVac Pro</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f6f8;color:#17212b;margin:0}
        main{max-width:980px;margin:36px auto;padding:0 18px}.panel{background:#fff;border:1px solid #e3e8ee;border-radius:14px;padding:24px;margin:18px 0}
        h1,h2{margin-top:0}label{display:block;font-weight:600;margin:14px 0 6px}input,textarea,select{width:100%;box-sizing:border-box;padding:11px;border:1px solid #cbd5df;border-radius:8px}
        textarea{min-height:90px}button{background:#176b55;color:white;border:0;padding:12px 18px;border-radius:8px;cursor:pointer}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px}.checks{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px}
        .checks label{font-weight:400;margin:5px}.checks input,.inline input{width:auto}.notice{padding:12px;background:#e9f7ef;border-radius:8px}.errors{color:#a32121}
    </style>
</head>
<body><main>
    <h1>Mon profil enseignant</h1><p>Complétez vos informations pour améliorer votre visibilité auprès des établissements.</p>
    @if(session('status'))<p class="notice">{{ session('status') }}</p>@endif
    @if($errors->any())<div class="panel errors"><strong>Vérifiez les champs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="panel"><h2>Complétude : {{ $profile->profile_completion }}%</h2><progress value="{{ $profile->profile_completion }}" max="100" style="width:100%"></progress><p>Statut : {{ str_replace('_', ' ', $profile->verification_status) }}</p></section>

    <form method="POST" action="{{ route('teacher-profile.update') }}">@csrf @method('PUT')
        <section class="panel"><h2>Informations personnelles et professionnelles</h2>
            <div class="grid">
                <div><label>Prénom</label><input name="first_name" value="{{ old('first_name', $profile->first_name) }}" required></div>
                <div><label>Nom</label><input name="last_name" value="{{ old('last_name', $profile->last_name) }}" required></div>
                <div><label>Téléphone</label><input name="phone" value="{{ old('phone', $profile->phone) }}"></div>
                <div><label>Ville</label><input name="city" value="{{ old('city', $profile->city) }}" required></div>
                <div><label>Années d’expérience</label><input type="number" min="0" max="60" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" required></div>
                <div><label>Rayon de mobilité (km)</label><input name="mobility_radius_km" value="{{ old('mobility_radius_km', $profile->mobility_radius_km) }}" placeholder="Ex. 15"></div>
            </div>
            <label>Présentation professionnelle</label><textarea name="biography" maxlength="2000">{{ old('biography', $profile->biography) }}</textarea>
            <label class="inline"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $profile->is_available))> Disponible pour de nouvelles vacations</label>
            <label>Zones de mobilité (une zone par ligne)</label><textarea name="mobility_areas_text" placeholder="Yoff&#10;Plateau&#10;Parcelles Assainies">{{ implode("\n", $profile->mobility_areas ?? []) }}</textarea>
        </section>
        <section class="panel"><h2>Matières enseignées</h2><div class="checks">@foreach($subjects as $subject)<label><input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" @checked($profile->subjects->contains($subject->id))> {{ $subject->name }}</label>@endforeach</div></section>
        <section class="panel"><h2>Niveaux d’enseignement</h2><div class="checks">@foreach($schoolLevels as $level)<label><input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}" @checked($profile->schoolLevels->contains($level->id))> {{ $level->name }}</label>@endforeach</div></section>
        <section class="panel"><h2>Disponibilités hebdomadaires</h2><p>1 = lundi, 7 = dimanche. Vous pouvez définir jusqu’à trois créneaux ici.</p>
            @for($i=0; $i<max(3, $profile->availabilities->count()); $i++)
                @php($slot = $profile->availabilities->get($i))
                <div class="grid">
                    <div><label>Jour</label><select name="availabilities[{{ $i }}][weekday]"><option value="">—</option>@foreach([1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi',7=>'Dimanche'] as $day=>$label)<option value="{{ $day }}" @selected(old("availabilities.$i.weekday", $slot?->weekday) == $day)>{{ $label }}</option>@endforeach</select></div>
                    <div><label>Début</label><input type="time" name="availabilities[{{ $i }}][starts_at]" value="{{ old("availabilities.$i.starts_at", $slot ? substr($slot->starts_at,0,5) : '') }}"></div>
                    <div><label>Fin</label><input type="time" name="availabilities[{{ $i }}][ends_at]" value="{{ old("availabilities.$i.ends_at", $slot ? substr($slot->ends_at,0,5) : '') }}"></div>
                </div>
            @endfor
        </section><button type="submit">Enregistrer mon profil</button>
    </form>
    <section class="panel"><h2>CV, diplômes et certificats</h2>
        <form method="POST" action="{{ route('teacher-documents.store') }}" enctype="multipart/form-data">@csrf
            <label>Type de document</label><select name="type" required><option value="cv">CV</option><option value="diploma">Diplôme</option><option value="certificate">Certificat</option><option value="identity">Pièce d’identité</option><option value="other">Autre</option></select>
            <label>Fichier (PDF, DOC ou DOCX — 10 Mo maximum)</label><input type="file" name="document" accept=".pdf,.doc,.docx" required><p><button type="submit">Ajouter le document</button></p>
        </form>
        <ul>@foreach($profile->documents as $document)<li>{{ $document->original_name }} — {{ $document->type }} — {{ $document->verification_status }} — <a href="{{ route('teacher-documents.download', $document) }}">Télécharger</a></li>@endforeach</ul>
    </section>
</main></body></html>
