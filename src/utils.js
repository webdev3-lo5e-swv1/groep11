// ── Cookie helpers ────────────────────────────────────────────────────────────
export function setCookie(name, value, days) {
    const exp = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)};expires=${exp};path=/;SameSite=Lax`;
}

export function getCookie(name) {
    return decodeURIComponent(
        (document.cookie.match('(?:^|; )' + name + '=([^;]*)') || [])[1] || ''
    );
}

// ── Bevestig verwijderen ──────────────────────────────────────────────────────
export function confirmDelete(title) {
    return confirm(`Ben je zeker dat je "${title}" wilt verwijderen?`);
}
// Expose globally for inline onclick handlers
window.confirmDelete = confirmDelete;
