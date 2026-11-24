<article class="card" style="border:1px solid #dbe3ea;border-radius:10px;padding:14px;margin:12px 0">
    <h3>Entretien du {{ $interview->scheduled_at->format('d/m/Y à H:i') }}</h3>
    <p>Format : {{ ['in_person' => 'Présentiel', 'online' => 'En ligne', 'phone' => 'Téléphonique'][$interview->format] ?? $interview->format }}</p>
    @if($interview->location_or_link)<p>Lieu / lien : {{ $interview->location_or_link }}</p>@endif
    <p>État : {{ $interview->status }} · Réponse enseignant : {{ $interview->teacher_response }}</p>
    @if($interview->teacher_response_note)<p>Note de l’enseignant : {{ $interview->teacher_response_note }}</p>@endif

    @if(auth()->user()->hasAnyRole(['school_manager', 'school_recruiter', 'platform_admin']) && $interview->status === 'scheduled')
        <form method="POST" action="{{ route('school-interviews.cancel', $interview) }}">
            @csrf @method('PATCH')
            <button type="submit">Annuler l’entretien</button>
        </form>
    @endif

    @if(auth()->user()->hasRole('teacher') && $interview->status === 'scheduled' && $interview->teacher_response === 'pending')
        <form method="POST" action="{{ route('teacher-interviews.respond', $interview) }}" style="margin-top:12px">
            @csrf @method('PATCH')
            <label for="response-{{ $interview->id }}">Votre réponse</label>
            <select id="response-{{ $interview->id }}" name="teacher_response" required>
                <option value="accepted">Confirmer ma présence</option>
                <option value="reschedule_requested">Demander un autre créneau</option>
                <option value="declined">Décliner</option>
            </select>
            <textarea name="teacher_response_note" placeholder="Commentaire ou disponibilités proposées (obligatoire pour un report)" maxlength="2000"></textarea>
            <button type="submit">Envoyer ma réponse</button>
        </form>
    @endif
</article>