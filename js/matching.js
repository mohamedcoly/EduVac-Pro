/**
 * Algorithme de Matching pour EduVac Pro
 * Calcule un score de pertinence entre 0% et 100%
 */

export function calculateMatchScore(teacher, mission) {
    let score = 0;

    // 1. LOCALISATION (Max 40 points)
    const tCity = (teacher.city || '').toLowerCase().trim();
    const mLoc = (mission.location || '').toLowerCase().trim();

    if (tCity && mLoc) {
        if (tCity === mLoc) {
            score += 40;
        } else if (mLoc.includes(tCity) || tCity.includes(mLoc)) {
            score += 20; // Partial match
        }
    }

    // 2. DIPLÔME (Max 40 points)
    // Les diplômes dans le système: "Bac+3", "Bac+4", "Bac+5", "Doctorat"
    const diploma = (teacher.teacher_details?.diploma || '').toLowerCase();
    if (diploma.includes('doctorat')) {
        score += 40;
    } else if (diploma.includes('bac+5') || diploma.includes('ingénieur')) {
        score += 35;
    } else if (diploma.includes('bac+4') || diploma.includes('master 1')) {
        score += 30;
    } else if (diploma.includes('bac+3') || diploma.includes('licence')) {
        score += 25;
    } else if (diploma.length > 0) {
        score += 15; // Au moins un niveau renseigné
    }

    // 3. EXPÉRIENCE (Max 20 points)
    // On considère que 5 ans d'expérience au moins donne le maximum des points
    const exp = parseInt(teacher.teacher_details?.experience_years) || 0;
    let expScore = (exp / 5) * 20;
    if (expScore > 20) expScore = 20;
    score += expScore;

    return Math.round(score);
}

/**
 * Retourne le HTML du badge de couleur selon le score
 */
export function getMatchBadgeHtml(score) {
    if (score >= 80) {
        return `<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold border border-green-200 shadow-sm"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> ${score}% Match</span>`;
    } else if (score >= 50) {
        return `<span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold border border-yellow-200 shadow-sm"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> ${score}% Match</span>`;
    } else {
        return `<span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-bold border border-gray-200 shadow-sm"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> ${score}% Match</span>`;
    }
}
