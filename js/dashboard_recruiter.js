import { calculateMatchScore, getMatchBadgeHtml } from './matching.js';
const supabaseUrl = 'https://anjyvyghdkdhaajhjwlt.supabase.co';
const supabaseAnonKey = 'sb_publishable_zi_3bUmD4wNd-aAmRNrhew_Cd6gzblL';
const supabase = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

let teachersData = [];
let myUserId = null;
let recruiterProfileData = null;
let myMissionsListRaw = [];
let currentChatAppId = null;

// --- MAIN TABS ---
window.switchTab = (tab) => {
    ['Search', 'Missions', 'Profile'].forEach(t => {
        document.getElementById('tab' + t).classList.add('hidden');
        document.getElementById('nav' + t).className = "w-full flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl text-left font-medium transition";
    });
    document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');
    document.getElementById('nav' + tab.charAt(0).toUpperCase() + tab.slice(1)).className = "w-full flex items-center gap-3 px-4 py-3 bg-gray-800 rounded-xl text-white font-medium transition shadow-inner";
    if (tab === 'missions') { fetchMyMissions(); fetchAllCandidatures(); }
};

// --- SUB TABS ---
window.switchSubTab = (sub) => {
    const cBtn = document.getElementById('subTabBtnCandidatures');
    const oBtn = document.getElementById('subTabBtnOffres');
    const cDiv = document.getElementById('subTabCandidatures');
    const oDiv = document.getElementById('subTabOffres');
    const activeClass = "px-5 py-2.5 rounded-lg text-sm font-semibold transition-all bg-white text-gray-900 shadow-sm";
    const inactiveClass = "px-5 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500 hover:text-gray-700";
    if (sub === 'candidatures') {
        cBtn.className = activeClass; oBtn.className = inactiveClass;
        cDiv.classList.remove('hidden'); oDiv.classList.add('hidden');
    } else {
        oBtn.className = activeClass; cBtn.className = inactiveClass;
        oDiv.classList.remove('hidden'); cDiv.classList.add('hidden');
    }
};

// --- INIT ---
async function init() {
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) { window.location.href = 'index.html'; return; }
    myUserId = user.id;
    const { data } = await supabase.from('profiles').select('*').eq('id', myUserId).single();
    recruiterProfileData = data;
    if (data) {
        document.getElementById('recFullName').value = data.full_name || '';
        document.getElementById('recCity').value = data.city || '';
        document.getElementById('recBio').value = data.bio || '';
        document.getElementById('recAlerts').checked = data.email_alerts !== false;
    }
    const { data: missionsData } = await supabase.from('missions').select('*').eq('recruiter_id', myUserId).eq('is_active', true);
    myMissionsListRaw = missionsData || [];
    fetchTeachers();
}

// --- PROFILE ---
window.saveRecruiterProfile = async () => {
    const btn = document.getElementById('btnSaveRecruiter');
    btn.innerText = "Sauvegarde..."; btn.disabled = true;
    try {
        await supabase.from('profiles').update({
            full_name: document.getElementById('recFullName').value,
            city: document.getElementById('recCity').value,
            bio: document.getElementById('recBio').value,
            email_alerts: document.getElementById('recAlerts').checked
        }).eq('id', myUserId);
        showToast("Profil mis à jour !");
    } catch (e) { showToast(e.message, "error") }
    btn.innerText = "Enregistrer"; btn.disabled = false;
}

// --- TEACHERS SEARCH ---
async function fetchTeachers() {
    try {
        const { data, error } = await supabase.from('profiles')
            .select('id, full_name, city, teacher_details(diploma, experience_years, cv_url, specialties)')
            .eq('role', 'teacher').eq('is_validated', true);
        if (error) throw error;
        const enriched = data.map(t => {
            let best = 0;
            if (myMissionsListRaw.length > 0) {
                myMissionsListRaw.forEach(m => { const s = calculateMatchScore(t, m); if (s > best) best = s; });
            } else { best = calculateMatchScore(t, { location: recruiterProfileData?.city, level: '' }); }
            t.matchScore = best; return t;
        });
        enriched.sort((a, b) => b.matchScore - a.matchScore);
        teachersData = enriched;
        renderTeachers(teachersData);
    } catch (err) {
        console.error(err);
        document.getElementById('loading').innerHTML = "<p class='text-red-500'>Erreur de chargement.</p>";
    }
}

function renderTeachers(teachers) {
    document.getElementById('loading').classList.add('hidden');
    const grid = document.getElementById('teachersGrid');
    const noResults = document.getElementById('noResults');
    grid.innerHTML = '';
    if (teachers.length === 0) { grid.classList.add('hidden'); noResults.classList.remove('hidden'); return; }
    grid.classList.remove('hidden'); noResults.classList.add('hidden');
    teachers.forEach(teacher => {
        const d = teacher.teacher_details || {};
        const card = document.createElement('div');
        card.className = "bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col group";
        card.innerHTML = `
            <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                <div class="flex items-center space-x-4">
                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=${teacher.id}" alt="avatar" class="w-12 h-12 rounded-full border border-gray-100 shadow-sm" />
                    <div>
                        <h3 class="font-bold text-gray-900 group-hover:text-primary transition-colors text-sm">${teacher.full_name}</h3>
                        <p class="text-xs text-gray-500 flex items-center mt-0.5">📍 ${teacher.city || 'Non renseigné'}</p>
                    </div>
                </div>
                ${getMatchBadgeHtml(teacher.matchScore)}
            </div>
            <div class="flex-1 space-y-2 mb-4">
                <div class="text-xs"><span class="text-gray-400 block mb-0.5">Diplôme</span><span class="font-medium text-gray-800 bg-gray-50 px-2.5 py-1 rounded-md inline-block border">${d.diploma || 'Aucun'}</span></div>
                ${d.specialties ? `<div class="text-xs"><span class="text-gray-400 block mb-0.5">Spécialités</span><span class="font-medium text-primary bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md inline-block">${d.specialties}</span></div>` : ''}
                <div class="text-xs"><span class="text-gray-400 block mb-0.5">Expérience</span><span class="font-medium text-gray-800">${d.experience_years || 0} an(s)</span></div>
            </div>
            ${d.cv_url
                ? `<button onclick="downloadCV('${d.cv_url}')" class="w-full text-primary bg-indigo-50 hover:bg-indigo-100 py-2 rounded-xl font-medium transition text-xs flex justify-center items-center gap-1.5 border border-indigo-100">📄 Voir CV PDF</button>`
                : `<button disabled class="w-full text-center text-gray-400 bg-gray-50 py-2 rounded-xl font-medium text-xs cursor-not-allowed">Aucun CV disponible</button>`}`;
        grid.appendChild(card);
    });
}

window.filterTeachers = () => {
    const q = document.getElementById('searchInput').value.toLowerCase();
    renderTeachers(teachersData.filter(t => (t.city||'').toLowerCase().includes(q) || (t.teacher_details?.diploma||'').toLowerCase().includes(q)));
};

window.downloadCV = async (filePath) => {
    try {
        const { data, error } = await supabase.storage.from('cvs').createSignedUrl(filePath, 60);
        if (!error && data?.signedUrl) window.open(data.signedUrl, '_blank');
    } catch (err) { showToast("Erreur lors de la lecture du CV.", "error"); }
};

// --- PUBLISH MISSION ---
document.getElementById('missionForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnPublish');
    btn.disabled = true; btn.innerText = 'Publication...';
    try {
        await supabase.from('missions').insert({
            recruiter_id: myUserId,
            title: document.getElementById('m_title').value,
            level: document.getElementById('m_level').value,
            location: document.getElementById('m_location').value,
            description: document.getElementById('m_desc').value,
            availability: document.getElementById('m_availability').value || null
        });
        document.getElementById('missionForm').reset();
        fetchMyMissions();
        showToast("Offre de vacation publiée !");
    } catch (e) { showToast("Erreur : " + e.message, "error"); }
    finally { btn.disabled = false; btn.innerText = "Publier l'offre"; }
});

// --- FETCH & RENDER MISSIONS (Offres tab) ---
async function fetchMyMissions() {
    const list = document.getElementById('myMissionsList');
    const { data, error } = await supabase.from('missions')
        .select('*')
        .eq('recruiter_id', myUserId)
        .order('created_at', { ascending: false });
    if (error) return list.innerHTML = `<p class="text-red-500">Erreur réseau.</p>`;
    if (!data || data.length === 0) return list.innerHTML = `<div class="p-10 text-center bg-white rounded-2xl border border-gray-100 shadow-sm"><div class="text-4xl mb-4">🚀</div><p class="text-gray-500 font-medium">Votre espace est vide ! Publiez votre première vacation.</p></div>`;
    list.innerHTML = data.map(m => `
        <div class="p-6 bg-white border border-gray-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-bold text-xl text-gray-900">${m.title}</h3>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-1">${m.level} • ${m.location}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openEditModal('${m.id}')" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">✏️ Modifier</button>
                    <span class="text-[10px] font-bold px-2 py-1 ${m.is_active ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'} rounded border uppercase tracking-wider">${m.is_active ? 'Active' : 'Inactive'}</span>
                </div>
            </div>
            ${m.availability ? `<div class="flex items-center gap-2 mb-2"><span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-lg font-medium">🕐 ${m.availability}</span></div>` : ''}
            <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-lg border border-dashed">${m.description}</p>
        </div>`).join('');
}

// --- FETCH ALL CANDIDATURES (Candidatures tab) ---
async function fetchAllCandidatures() {
    const list = document.getElementById('allCandidaturesList');
    const { data, error } = await supabase.from('missions')
        .select('*, applications(id, status, teacher_id, profiles(full_name, teacher_details(cv_url, diploma, specialties)))')
        .eq('recruiter_id', myUserId)
        .order('created_at', { ascending: false });
    if (error) return list.innerHTML = `<p class="text-red-500">Erreur réseau.</p>`;

    const missionsWithApps = (data || []).filter(m => m.applications && m.applications.length > 0);
    if (missionsWithApps.length === 0) {
        return list.innerHTML = `<div class="p-10 text-center bg-white rounded-2xl border border-gray-100 shadow-sm"><div class="text-4xl mb-4">🤞</div><p class="text-gray-500 font-medium">Aucune candidature reçue pour le moment.</p></div>`;
    }

    // Fetch availabilities for all teacher_ids
    const teacherIds = [];
    missionsWithApps.forEach(m => m.applications.forEach(a => { if (a.teacher_id && !teacherIds.includes(a.teacher_id)) teacherIds.push(a.teacher_id); }));
    let availMap = {};
    if (teacherIds.length > 0) {
        const { data: avails } = await supabase.from('availabilities').select('*').in('teacher_id', teacherIds);
        if (avails) {
            avails.forEach(av => {
                if (!availMap[av.teacher_id]) availMap[av.teacher_id] = [];
                availMap[av.teacher_id].push(av);
            });
        }
    }

    list.innerHTML = missionsWithApps.map(m => {
        const apps = m.applications;
        return `
        <div class="p-6 bg-white border border-gray-100 rounded-2xl shadow-sm mb-4">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-lg text-gray-900">${m.title}</h3>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-1">${m.level} • ${m.location}</p>
                </div>
                <span class="text-xs font-bold px-2 py-1 bg-indigo-50 text-primary rounded border border-indigo-200">${apps.length} candidature(s)</span>
            </div>
            ${m.availability ? `<div class="mb-4"><span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-lg font-medium">🕐 Dispo requise : ${m.availability}</span></div>` : ''}
            <div class="space-y-3">
                ${apps.map(a => renderCandidatureCard(a, availMap[a.teacher_id] || [])).join('')}
            </div>
        </div>`;
    }).join('');
}

function renderAvailabilityBadges(avails) {
    if (!avails || avails.length === 0) return '<span class="text-xs text-gray-400 italic">Non renseigné</span>';
    return avails.map(av => {
        let slots = [];
        if (av.matin) slots.push('Matin');
        if (av.apres_midi) slots.push('Après-midi');
        if (slots.length === 0) return '';
        return `<span class="text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">${av.jour_semaine.charAt(0).toUpperCase()+av.jour_semaine.slice(1)}: ${slots.join(', ')}</span>`;
    }).filter(Boolean).join(' ');
}

function renderCandidatureCard(a, avails) {
    const pName = a.profiles?.full_name || 'Inconnu';
    const pDip = a.profiles?.teacher_details?.diploma || '-';
    const pSpec = a.profiles?.teacher_details?.specialties || '';
    const pCv = a.profiles?.teacher_details?.cv_url;

    let actions = '';
    if (a.status === 'pending') {
        actions = `
            <button onclick="updateStatus('${a.id}', 'accepted')" class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded shadow-sm font-medium transition">Accepter</button>
            <button onclick="updateStatus('${a.id}', 'rejected')" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded transition font-medium">Refuser</button>`;
    } else {
        actions = `<span class="text-xs font-bold px-2 py-1 rounded ${a.status === 'accepted' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'} mr-2">${a.status === 'accepted' ? 'Accepté 🎉' : 'Rejeté'}</span>`;
        if (a.status === 'accepted') {
            actions += `<button onclick="openChat('${a.id}')" class="text-xs flex items-center bg-primary text-white px-3 py-1.5 rounded-lg shadow hover:bg-indigo-600 transition">💬 Discuter</button>`;
        }
    }

    return `
    <div class="flex flex-col p-4 bg-gray-50 border border-gray-100 hover:border-primary/30 hover:shadow-sm rounded-xl transition">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-3">
            <div class="mb-2 xl:mb-0 flex items-center gap-3">
                <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=${a.teacher_id}" class="w-10 h-10 rounded-full bg-white border border-gray-100 shadow-sm" />
                <div>
                    <div class="font-bold text-gray-900 flex items-center flex-wrap gap-2">
                        ${pName}
                        ${pCv ? `<button onclick="downloadCV('${pCv}')" class="text-xs bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded shadow-sm text-primary hover:bg-indigo-100 font-bold transition">📄 CV</button>` : ''}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">🎓 ${pDip} ${pSpec ? `<span class="mx-1 text-gray-300">|</span> 🧠 <span class="font-medium text-gray-700">${pSpec}</span>` : ''}</div>
                </div>
            </div>
            <div class="flex items-center gap-2">${actions}</div>
        </div>
        <div class="border-t border-gray-200 pt-2 mt-1">
            <span class="text-xs font-semibold text-gray-600 mr-2">📅 Disponibilités :</span>
            <div class="flex flex-wrap gap-1 mt-1">${renderAvailabilityBadges(avails)}</div>
        </div>
    </div>`;
}

// --- UPDATE STATUS + AUTO NOTIFICATION ---
window.updateStatus = async (appId, newStatus) => {
    if (newStatus === 'accepted') {
        if (!confirm("Accepter cette candidature ? Un message de notification sera envoyé au candidat.")) return;
    } else {
        if (!confirm("Refuser définitivement cette candidature ?")) return;
    }
    try {
        await supabase.from('applications').update({ status: newStatus }).eq('id', appId);

        // Auto-send notification message via chat
        if (newStatus === 'accepted') {
            const notifMsg = "🎉 Félicitations ! Votre candidature a été acceptée. N'hésitez pas à me contacter ici pour organiser les détails de la mission.";
            await supabase.from('chat_messages').insert({ application_id: appId, sender_id: myUserId, content: notifMsg });
        } else {
            const notifMsg = "Bonjour, nous avons bien étudié votre candidature. Malheureusement, nous ne pouvons pas y donner suite pour cette mission. Nous vous souhaitons bonne chance pour la suite.";
            await supabase.from('chat_messages').insert({ application_id: appId, sender_id: myUserId, content: notifMsg });
        }

        fetchAllCandidatures();
        showToast(newStatus === 'accepted' ? "Candidature acceptée ! Notification envoyée." : "Candidature refusée. Notification envoyée.");
    } catch (e) { showToast("Erreur : " + e.message, "error"); }
}

// --- EDIT MISSION MODAL ---
let editingMissionData = null;

window.openEditModal = async (missionId) => {
    const { data, error } = await supabase.from('missions').select('*').eq('id', missionId).single();
    if (error || !data) return showToast("Impossible de charger cette offre.", "error");
    editingMissionData = data;
    document.getElementById('edit_m_id').value = data.id;
    document.getElementById('edit_m_title').value = data.title || '';
    document.getElementById('edit_m_level').value = data.level || '';
    document.getElementById('edit_m_location').value = data.location || '';
    document.getElementById('edit_m_availability').value = data.availability || '';
    document.getElementById('edit_m_desc').value = data.description || '';
    document.getElementById('editMissionModal').classList.remove('hidden');
};

window.closeEditModal = () => document.getElementById('editMissionModal').classList.add('hidden');

document.getElementById('editMissionForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('edit_m_id').value;
    try {
        await supabase.from('missions').update({
            title: document.getElementById('edit_m_title').value,
            level: document.getElementById('edit_m_level').value,
            location: document.getElementById('edit_m_location').value,
            availability: document.getElementById('edit_m_availability').value || null,
            description: document.getElementById('edit_m_desc').value
        }).eq('id', id);
        closeEditModal();
        fetchMyMissions();
        fetchAllCandidatures();
        showToast("Offre modifiée avec succès !");
    } catch (e) { showToast("Erreur : " + e.message, "error"); }
});

// --- CHAT SYSTEM ---
window.openChat = async (appId) => {
    currentChatAppId = appId;
    document.getElementById('chatModal').classList.remove('hidden');
    document.getElementById('chatMessages').innerHTML = '<div class="text-center mt-10"><div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-200 border-t-primary rounded-full"></div></div>';
    await loadMessages();
};
window.closeChat = () => document.getElementById('chatModal').classList.add('hidden');

async function loadMessages() {
    const { data } = await supabase.from('chat_messages').select('*, profiles(full_name)').eq('application_id', currentChatAppId).order('created_at', { ascending: true });
    const div = document.getElementById('chatMessages');
    if (!data || data.length === 0) return div.innerHTML = `<div class="text-center text-sm text-gray-400 mt-10">La discussion est vide. Faites le premier pas ! 👋</div>`;
    div.innerHTML = data.map(msg => {
        const isMe = msg.sender_id === myUserId;
        const avatarUrl = isMe ? `https://api.dicebear.com/7.x/shapes/svg?seed=${myUserId}` : `https://api.dicebear.com/7.x/adventurer/svg?seed=${msg.sender_id}`;
        return `
        <div class="flex ${isMe ? 'justify-end' : 'justify-start'} w-full">
            ${!isMe ? `<img src="${avatarUrl}" class="w-8 h-8 rounded-full mr-2 self-end mb-1 shadow-sm" />` : ''}
            <div class="max-w-[70%] rounded-2xl px-4 py-2.5 text-sm shadow-sm flex flex-col ${isMe ? 'bg-primary text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                ${!isMe ? `<div class="text-[10px] font-bold text-gray-500 mb-1 leading-none uppercase tracking-wider">${msg.profiles?.full_name || 'Professeur'}</div>` : ''}
                <span class="leading-relaxed">${msg.content}</span>
            </div>
        </div>`;
    }).join('');
    div.scrollTop = div.scrollHeight;
}

document.getElementById('sendChatBtn')?.addEventListener('click', async () => {
    const input = document.getElementById('chatInput');
    if (!input.value.trim()) return;
    await supabase.from('chat_messages').insert({ application_id: currentChatAppId, sender_id: myUserId, content: input.value.trim() });
    input.value = ''; await loadMessages();
});
document.getElementById('chatInput')?.addEventListener('keypress', function (e) { if (e.key === 'Enter') document.getElementById('sendChatBtn').click(); });

window.logout = async () => { await supabase.auth.signOut(); window.location.href = 'index.html'; };

// Initialize
if (document.getElementById('navSearch')) {
    document.getElementById('navSearch').click();
    init();
}
