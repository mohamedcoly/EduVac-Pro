// toast.js - Popup notification system
window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    const isError = type === 'error' || String(message).toLowerCase().includes("erreur");
    const bgColor = isError ? 'bg-red-500' : 'bg-green-500';
    
    // Tailwind classes for the popup
    toast.className = `fixed bottom-5 right-5 ${bgColor} text-white px-6 py-3 rounded-xl shadow-2xl z-[9999] flex items-center gap-3 transition-all duration-500 transform translate-y-20 opacity-0`;
    
    const icon = isError
        ? `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
        : `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

    toast.innerHTML = `${icon} <span class="font-medium text-sm">${message}</span>`;
    document.body.appendChild(toast);

    // Animation in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-20', 'opacity-0');
        });
    });

    // Animation out
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
        setTimeout(() => toast.remove(), 500); // match transition-duration
    }, 4000);
};
