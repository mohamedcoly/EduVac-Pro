<article style="border:1px solid #dbe3ea;border-radius:10px;padding:14px;margin:12px 0">
    <h3>Mission proposée</h3>
    <p>{{ $mission->mission_description }}</p>
    <p>Début : {{ $mission->starts_on->format('d/m/Y') }} · Fin : {{ $mission->ends_on?->format('d/m/Y') ?? 'Non précisée' }}</p>
    <p>Volume : {{ $mission->weekly_hours ? $mission->weekly_hours.' h/semaine' : 'À convenir' }}</p>
    <p>Rémunération : {{ number_format($mission->pay_amount_fcfa, 0, ',', ' ') }} FCFA ·
        {{ ['hourly' => 'heure', 'weekly' => 'semaine', 'monthly' => 'mois', 'fixed' => 'forfait'][$mission->payment_frequency] ?? $mission->payment_frequency }}</p>
    @if($mission->schedule_details)<p>Horaires : {{ $mission->schedule_details }}</p>@endif
    @if($mission->teacher_response_note)<p>Commentaire de l’enseignant : {{ $mission->teacher_response_note }}</p>@endif
    @if($mission->workplace_address)<p>Lieu : {{ $mission->workplace_address }}</p>@endif
    <p>État : {{ $mission->status }} · Réponse : {{ $mission->teacher_confirmation }}</p>
    @if(auth()->user()->hasRole('teacher') && $mission->status === 'planned' && $mission->teacher_confirmation === 'pending')
        <form method="POST" action="{{ route('teacher-missions.respond', $mission) }}">
            @csrf @method('PATCH')
            <label>Votre décision</label>
            <select name="teacher_confirmation" required>
                <option value="accepted">J’accepte les conditions</option>
                <option value="declined">Je décline la proposition</option>
            </select>
            <textarea name="teacher_response_note" maxlength="2000" placeholder="Commentaire (facultatif)"></textarea>
            <button type="submit">Confirmer ma décision</button>
        </form>
    @endif
</article>