import { calculateMatchScore, getMatchBadgeHtml } from './matching.js';
const supabaseUrl = 'https://anjyvyghdkdhaajhjwlt.supabase.co';
const supabaseAnonKey = 'sb_publishable_zi_3bUmD4wNd-aAmRNrhew_Cd6gzblL';
const supabase = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

let teacherProfileData = null;
let myUserId = null;
let myApplicationsRaw = [];
let allMissionsRaw = [];
let currentChatAppId = null;

const Jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

// --- TABS & UI ---
window.switchTab = (tab) => {
    ['Missions', 'Applications', 'Profile'].forEach(t => {
        document.getElementById('tab' + t).classList.add('hidden');
        document.getElementById('nav' + t).className = "w-full flex items-center gap-3 px-4 py-3 text-indigo-100 hover:bg-indigo-600/50 rounded-xl text-left font-medium transition";
    });
    document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');
    document.getElementById('nav' + tab.charAt(0).toUpperCase() + tab.slice(1)).className = "w-full flex items-center gap-3 px-4 py-3 bg-indigo-600 rounded-xl text-white font-medium transition shadow-inner";
    
    if(tab === 'applications') renderApplications();
};

// --- INIT ---
async function initDashboard() {
    try {
        const { data: { user } } = await supabase.auth.getUser();
        if (!user) { window.location.href = 'index.html'; return; }
        myUserId = user.id;

        const { data: userProfile, error } = await supabase
            .from('profiles')
            .select('*, teacher_details(*)')
            .eq('id', user.id)
            .single();

        if (error) throw error;
        teacherProfileData = userProfile;

        renderValidationBanner(userProfile.is_validated);
        populateProfileForm(userProfile);
        await fetchAvailabilities();
        
        if (userProfile.is_validated) {
            await fetchMissionsAndApps();
        } else {
            document.getElementById('missionsLoading').classList.add('hidden');
            document.getElementById('noMissions').classList.remove('hidden');
        }
    } catch (err) {
        console.error(err);
    }
}

function renderValidationBanner(isValidated) {
    const banner = document.getElementById('validationBanner');
    if(!banner) return;
    banner.classList.remove('hidden');
    if (isValidated) {
        banner.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
        banner.innerHTML = `<span class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Profil vérifié et actif sur la plateforme !</span>`;
    } else {
        banner.classList.add('bg-yellow-50', 'text-yellow-800', 'border', 'border-yellow-200');
        banner.innerHTML = `<span class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> En attente de vérification administrateur.</span>`;
    }
}

// --- PROFILE & DISPO ---
function populateProfileForm(p) {
    document.getElementById('profFullName').value = p.full_name || '';
    document.getElementById('profCity').value = p.city || '';
    document.getElementById('profDiploma').value = p.teacher_details?.diploma || '';
    document.getElementById('profExp').value = p.teacher_details?.experience_years || 0;
    document.getElementById('profSpecialties').value = p.teacher_details?.specialties || '';
    document.getElementById('profAlerts').checked = p.email_alerts !== false;

    if (p.teacher_details?.cv_url) {
        document.getElementById('uploadStatus').innerText = "✓ Un CV est déjà enregistré. (Sélectionner pour remplacer)";
    } else {
        document.getElementById('uploadStatus').innerText = "";
    }

    // Generate Calendar grid
    const tbody = document.getElementById('calendarBody');
    if(!tbody) return;
    tbody.innerHTML = Jours.map(j => `
        <tr class="bg-white border-b hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-900 capitalize border-r">${j}</td>
            <td class="px-4 py-3 text-center border-r">
                <input type="checkbox" id="dispo_${j}_matin" class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary">
            </td>
            <td class="px-4 py-3 text-center">
                <input type="checkbox" id="dispo_${j}_apres" class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary">
            </td>
        </tr>
    `).join('');
}

async function fetchAvailabilities() {
    const { data } = await supabase.from('availabilities').select('*').eq('teacher_id', myUserId);
    if(data) {
        data.forEach(d => {
            const elM = document.getElementById(`dispo_${d.jour_semaine}_matin`);
            const elA = document.getElementById(`dispo_${d.jour_semaine}_apres`);
            if(elM) elM.checked = d.matin;
            if(elA) elA.checked = d.apres_midi;
        });
    }
}

window.saveProfile = async () => {
    const btn = document.getElementById('btnSaveProfile');
    btn.innerText = 'Sauvegarde...'; btn.disabled = true;
    try {
        await supabase.from('profiles').update({
            full_name: document.getElementById('profFullName').value,
            city: document.getElementById('profCity').value,
            email_alerts: document.getElementById('profAlerts').checked
        }).eq('id', myUserId);

        const cvInput = document.getElementById('cvUpload');
        let newCvUrl = teacherProfileData.teacher_details?.cv_url;
        if(cvInput.files.length > 0) {
            document.getElementById('uploadStatus').innerText = "Upload du CV en cours...";
            const file = cvInput.files[0];
            const filePath = `${myUserId}/${Date.now()}_${file.name}`;
            const { error: uploadError } = await supabase.storage.from('cvs').upload(filePath, file);
            if(!uploadError) {
                newCvUrl = filePath;
                document.getElementById('uploadStatus').innerText = "CV mis à jour !";
                teacherProfileData.teacher_details.cv_url = newCvUrl;
            } else {
                document.getElementById('uploadStatus').innerText = "Erreur upload CV.";
            }
        }

        await supabase.from('teacher_details').update({
            diploma: document.getElementById('profDiploma').value,
            experience_years: parseInt(document.getElementById('profExp').value) || 0,
            specialties: document.getElementById('profSpecialties').value,
            cv_url: newCvUrl
        }).eq('id', myUserId);

        for(const j of Jours) {
            const m = document.getElementById(`dispo_${j}_matin`).checked;
            const a = document.getElementById(`dispo_${j}_apres`).checked;
            await supabase.from('availabilities').delete().match({ teacher_id: myUserId, jour_semaine: j });
            if(m || a) {
                await supabase.from('availabilities').insert({ teacher_id: myUserId, jour_semaine: j, matin: m, apres_midi: a });
            }
        }

        showToast("Profil mis à jour avec succès !");
    } catch (e) {
        showToast("Erreur: "+e.message, "error");
    }
    btn.innerText = 'Enregistrer'; btn.disabled = false;
};

window.viewMyCv = async () => {
        if(!teacherProfileData?.teacher_details?.cv_url) return showToast('Aucun CV défini', 'error');
        const { data } = await supabase.storage.from('cvs').createSignedUrl(teacherProfileData.teacher_details.cv_url, 60);
        if (data?.signedUrl) window.open(data.signedUrl, '_blank');
};

// --- MISSIONS & APPS ---
async function fetchMissionsAndApps() {
    try {
        const { data: appsData } = await supabase.from('applications')
            .select('*, missions(*, profiles(*))')
            .eq('teacher_id', myUserId)
            .order('created_at', { ascending: false });
        
        myApplicationsRaw = appsData || [];

        const { data: missions } = await supabase.from('missions')
            .select('*, profiles(full_name)')
            .eq('is_active', true)
            .order('created_at', { ascending: false });
        
        const enrichedMissions = (missions || []).map(m => {
            m.matchScore = calculateMatchScore(teacherProfileData, m);
            return m;
        });
        
        enrichedMissions.sort((a, b) => b.matchScore - a.matchScore);
        
        allMissionsRaw = enrichedMissions;

        renderMissionsFeed(allMissionsRaw);
    } catch (err) {
        console.error(err);
    }
}

window.filterMissions = () => {
    const query = (document.getElementById('missionSearchInput')?.value || '').toLowerCase();
    const filtered = allMissionsRaw.filter(m => {
        const term = query.trim();
        return (m.title || '').toLowerCase().includes(term) || (m.location || '').toLowerCase().includes(term) || (m.profiles?.full_name || '').toLowerCase().includes(term);
    });
    renderMissionsFeed(filtered);
};


function renderMissionsFeed(missions) {
    document.getElementById('missionsLoading').classList.add('hidden');
    const list = document.getElementById('missionsList');
    const noM = document.getElementById('noMissions');

    if (missions.length === 0) {
        noM.classList.remove('hidden'); list.classList.add('hidden'); return;
    }

    noM.classList.add('hidden'); list.classList.remove('hidden');
    
    const myAppsMap = {};
    myApplicationsRaw.forEach(a => myAppsMap[a.mission_id] = a);

    list.innerHTML = missions.map(m => {
        const app = myAppsMap[m.id];
        let btnHtml = '';
        if(!app) {
            btnHtml = `<button onclick="applyToMission('${m.id}')" class="w-full mt-4 btn-primary py-2.5 rounded-xl text-sm font-medium flex justify-center items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Postuler</button>`;
        } else {
            btnHtml = `<div class="w-full mt-4 py-2.5 border rounded-xl text-sm font-medium text-center bg-gray-50 text-gray-500">Candidature envoyée</div>`;
        }

        return `
        <div class="card-aesthetic p-6 flex flex-col group h-full">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <img src="https://api.dicebear.com/7.x/shapes/svg?seed=${m.profiles?.full_name || m.id}" alt="avatar" class="w-10 h-10 rounded-full border border-gray-100 shadow-sm" />
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-primary inline-block">${m.profiles?.full_name || 'Établissement'}</span>
                        </div>
                    </div>
                    ${getMatchBadgeHtml(m.matchScore)}
                </div>
                <h3 class="font-bold text-lg text-gray-900 leading-tight mb-3 group-hover:text-primary transition">${m.title}</h3>
                <div class="flex flex-col gap-2 mb-4">
                    <span class="flex items-center text-xs text-gray-500 bg-gray-50 p-2 rounded-lg"><svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 011.66 4.277 11.94 11.94 0 01-1.66 4.277M12 14v5m0 0a12.083 12.083 0 011.16 4.277 11.94 11.94 0 01-1.16 4.277m0 0l-6.16-3.422"></path></svg> ${m.level}</span>
                    <span class="flex items-center text-xs text-gray-500 bg-gray-50 p-2 rounded-lg"><svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> ${m.location}</span>
                </div>
                <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed">${m.description}</p>
            </div>
            ${btnHtml}
        </div>`;
    }).join('');
}

window.applyToMission = async (missionId) => {
    if(!confirm("Êtes-vous sûr de vouloir envoyer votre candidature ?")) return;
    try {
        await supabase.from('applications').insert({ mission_id: missionId, teacher_id: myUserId });
        showToast("Candidature envoyée avec succès !");
        await fetchMissionsAndApps();
        switchTab('applications');
    } catch (err) { showToast(err.message, "error"); }
};

// --- MY APPLICATIONS ---
function renderApplications() {
    const list = document.getElementById('applicationsList');
    if(myApplicationsRaw.length === 0) {
        list.innerHTML = `<div class="p-10 text-center bg-white rounded-2xl border border-gray-100 shadow-sm"><div class="text-4xl mb-4">👻</div><p class="text-gray-500 font-medium">Vous n'avez pas encore envoyé de CV.</p></div>`;
        return;
    }

    list.innerHTML = myApplicationsRaw.map(app => {
        const m = app.missions;
        const rName = m.profiles?.full_name || 'Établissement';
        
        let statusUI = '';
        let actionsUI = '';

        if(app.status === 'pending') {
            statusUI = `<span class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center shadow-sm"><span class="w-2 h-2 rounded-full bg-yellow-400 mr-2 animate-pulse"></span>En attente</span>`;
        } else if(app.status === 'rejected') {
            statusUI = `<span class="bg-red-50 border border-red-200 text-red-800 px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center shadow-sm"><span class="w-2 h-2 rounded-full bg-red-400 mr-2"></span>Non retenu</span>`;
        } else if(app.status === 'accepted') {
            statusUI = `<span class="bg-green-50 border border-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center shadow-sm"><span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span>Accepté 🎉</span>`;
            actionsUI = `
                <div class="mt-5 pt-4 border-t border-gray-100 flex flex-col items-center">
                    <p class="text-sm font-medium text-gray-500 mb-3">Félicitations, l'établissement souhaite échanger avec vous !</p>
                    <button onclick="openChat('${app.id}')" class="w-full bg-primary hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        Ouvrir la Discussion
                    </button>
                </div>
            `;
        }

        return `
        <div class="card-aesthetic p-6 flex flex-col">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4 gap-4">
                <div class="flex items-center gap-4">
                    <img src="https://api.dicebear.com/7.x/shapes/svg?seed=${rName}" alt="avatar" class="w-12 h-12 rounded-xl shadow-sm border border-gray-100 hidden sm:block" />
                    <div>
                        <p class="text-xs text-primary font-bold mb-1 uppercase tracking-wider">${rName}</p>
                        <h3 class="text-xl font-bold text-gray-900">${m.title}</h3>
                        <p class="text-sm text-gray-500 mt-1 flex items-center"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Candidature envoyée le ${new Date(app.created_at).toLocaleDateString('fr-FR')}</p>
                    </div>
                </div>
                <div class="shrink-0">${statusUI}</div>
            </div>
            ${app.message ? `<div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-2 rounded-r-lg text-sm text-blue-800"><p class="font-semibold text-xs uppercase mb-1">Message d'origine</p><p class="italic">"${app.message}"</p></div>` : ''}
            ${actionsUI}
        </div>`;
    }).join('');
}


// --- CHAT SYSTEM ---
window.openChat = async (appId) => {
    currentChatAppId = appId;
    document.getElementById('chatModal').classList.remove('hidden');
    document.getElementById('chatMessages').innerHTML = '<div class="text-center text-gray-500 mt-10"><div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-200 border-t-primary rounded-full"></div></div>';
    await loadMessages();
};
window.closeChat = () => document.getElementById('chatModal').classList.add('hidden');

async function loadMessages() {
    const { data } = await supabase.from('chat_messages')
        .select('*, profiles(full_name)')
        .eq('application_id', currentChatAppId)
        .order('created_at', { ascending: true });
    
    const div = document.getElementById('chatMessages');
    if(!data || data.length === 0) {
        div.innerHTML = `<div class="text-center text-sm text-gray-400 mt-10">Faites le premier pas, dites bonjour ! 👋</div>`;
        return;
    }
    
    div.innerHTML = data.map(msg => {
        const isMe = msg.sender_id === myUserId;
        const avatarUrl = isMe ? `https://api.dicebear.com/7.x/adventurer/svg?seed=${myUserId}` : `https://api.dicebear.com/7.x/shapes/svg?seed=${msg.profiles?.full_name}`;
        
        return `
        <div class="flex ${isMe ? 'justify-end' : 'justify-start'} w-full">
            ${!isMe ? `<img src="${avatarUrl}" class="w-8 h-8 rounded-full mr-2 self-end mb-1" />` : ''}
            <div class="max-w-[70%] rounded-2xl px-4 py-2 text-sm shadow-sm ${isMe ? 'bg-primary text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                ${!isMe ? `<div class="text-[10px] font-bold text-gray-500 mb-1 leading-none">${msg.profiles?.full_name || 'Correspondant'}</div>` : ''}
                ${msg.content}
            </div>
        </div>`;
    }).join('');
    div.scrollTop = div.scrollHeight;
}

document.getElementById('sendChatBtn')?.addEventListener('click', async () => {
    const input = document.getElementById('chatInput');
    const txt = input.value.trim();
    if(!txt) return;
    input.value = '';
    
    await supabase.from('chat_messages').insert({
        application_id: currentChatAppId, sender_id: myUserId, content: txt
    });
    await loadMessages();
});

// Event listener for Enter on chat
document.getElementById('chatInput')?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        document.getElementById('sendChatBtn').click();
    }
});

window.logout = async () => {
    await supabase.auth.signOut();
    window.location.href = 'index.html';
};

// Initialize display
if(document.getElementById('navMissions')){
    document.getElementById('navMissions').click();
    initDashboard();
}
