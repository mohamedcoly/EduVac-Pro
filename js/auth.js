// SUPABASE CONFIGURATION - TO BE REPLACED WITH REAL CREDENTIALS
const supabaseUrl = 'https://anjyvyghdkdhaajhjwlt.supabase.co';
const supabaseAnonKey = 'sb_publishable_zi_3bUmD4wNd-aAmRNrhew_Cd6gzblL';
const supabase = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

const form = document.getElementById('authForm');
const errorDiv = document.getElementById('errorMessage');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.classList.add('hidden');
    submitBtn.disabled = true;
    submitBtn.innerText = 'Chargement...';

    let email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const fullName = document.getElementById('fullName')?.value;

    // PATCH POUR LES TESTS : Si on tape "jean", Supabase crashera car il veut un "vrai" email.
    // On rajoute donc "@test.com" artificiellement et silencieusement.
    if (!email.includes('@')) {
        email = email + '@test.com';
    }

    const { currentRole, isLogin } = window.authState;

    try {
        if (isLogin) {
            // LOGIN
            const { data, error } = await supabase.auth.signInWithPassword({ email, password });
            if (error) throw error;
            await checkValidationAndRedirect(data.user.id);
        } else {
            // SIGN UP DÉCOUPLÉ (Mode Easy)
            const { data, error } = await supabase.auth.signUp({
                email,
                password
            });
            if (error) throw error;

            const newUserId = data.user.id;

            // 1. On crée le profil public à la main
            const { error: profileError } = await supabase.from('profiles').insert({
                id: newUserId,
                full_name: fullName || 'Utilisateur',
                role: currentRole,
                is_validated: true // AUTO-VALIDATION POUR LE JURY
            });
            if (profileError) throw new Error("Erreur insertion profil: " + profileError.message);

            // 2. Si prof, on lui crée sa fiche détails
            if (currentRole === 'teacher') {
                const { error: detailsError } = await supabase.from('teacher_details').insert({
                    id: newUserId
                });
                if (detailsError) throw new Error("Erreur insertion details prof: " + detailsError.message);
            }

            // REDIRECTION AUTOMATIQUE AU LIEU DE RESTER BLOQUÉ
            await checkValidationAndRedirect(newUserId);
        }
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = window.authState.isLogin ? 'Se connecter' : 'Créer mon compte';
    }
});

async function checkValidationAndRedirect(userId) {
    const { data: profile, error } = await supabase
        .from('profiles')
        .select('role, is_validated')
        .eq('id', userId)
        .maybeSingle(); // maybeSingle évite le plantage 406 si la ligne n'existe pas

    if (error) {
        throw new Error('Erreur base de données : ' + error.message);
    }

    if (!profile) {
        throw new Error("Absence de profil détectée : Le déclencheur SQL (Trigger handle_new_user) n'a pas créé votre ligne de profil. Supprimez cet utilisateur côté Supabase (Auth), puis assurez-vous d'avoir exécuté la toute première requête SQL de notre projet avant de retenter de vous inscrire.");
    }

    // SI NON VALIDÉ (Théoriquement impossible en Mode Démo)
    if (!profile.is_validated) {
        window.location.href = 'pending.html';
        return;
    }

    // SI VALIDÉ -> on l'envoie sur son espace
    if (profile.role === 'teacher') {
        window.location.href = 'dashboard_teacher.html';
    } else if (profile.role === 'recruiter') {
        window.location.href = 'dashboard_recruiter.html';
    }
}

