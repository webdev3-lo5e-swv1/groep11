// ════════════════════════════════════════════════════════════════════════════
// MBO Cinemas — app.js
// ════════════════════════════════════════════════════════════════════════════

// ── Cookie helpers ───────────────────────────────────────────────────────────
function setCookie(name, value, days) {
    const exp = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)};expires=${exp};path=/;SameSite=Lax`;
}
function getCookie(name) {
    return decodeURIComponent(
        (document.cookie.match('(?:^|; )' + name + '=([^;]*)') || [])[1] || ''
    );
}

// ── Locaties dropdown ────────────────────────────────────────────────────────
const bar      = document.getElementById('locationsBar');
const arrow    = document.getElementById('locationsArrow');
const dropdown = document.getElementById('locationsDropdown');
const label    = document.getElementById('locationsLabel');

if (bar) {
    bar.addEventListener('click', () => {
        const isOpen = dropdown.classList.toggle('open');
        arrow.classList.toggle('open', isOpen);
    });

    dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.querySelectorAll('.dropdown-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            label.textContent = opt.textContent;
            dropdown.classList.remove('open');
            arrow.classList.remove('open');
            // Cookie: onthoud gekozen locatie
            setCookie('locatie', opt.dataset.value, 30);
            applyFilters();
        });
    });

    document.addEventListener('click', (e) => {
        if (bar && !bar.contains(e.target)) {
            dropdown.classList.remove('open');
            arrow.classList.remove('open');
        }
    });

    // Herstel locatiekeuze uit cookie
    const savedLocatie = getCookie('locatie');
    if (savedLocatie) {
        dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
            if (opt.dataset.value === savedLocatie) {
                opt.click();
                label.textContent = opt.textContent;
            }
        });
    }
}

// ── Gecombineerde filter: genre + zoekterm ───────────────────────────────────
const genreSelect    = document.getElementById('genreSelect');
const movieSearchHome = document.getElementById('movieSearchHome');

function applyFilters() {
    const genre  = genreSelect  ? genreSelect.value.toLowerCase()  : '';
    const search = movieSearchHome ? movieSearchHome.value.toLowerCase() : '';

    document.querySelectorAll('.movie-card').forEach(card => {
        const cardGenre = (card.dataset.genre || '').toLowerCase();
        const cardTitle = (card.dataset.title || '').toLowerCase();
        const matchGenre  = !genre  || cardGenre.includes(genre);
        const matchSearch = !search || cardTitle.includes(search);
        card.style.display = (matchGenre && matchSearch) ? '' : 'none';
    });
}

if (genreSelect)     genreSelect.addEventListener('change', applyFilters);
if (movieSearchHome) movieSearchHome.addEventListener('input', applyFilters);

// ── Film modal ───────────────────────────────────────────────────────────────
const modal = document.getElementById('movieModal');
let currentMovieTitle = '';

if (modal) {
    document.querySelectorAll('.movie-card').forEach(card => {
        card.addEventListener('click', () => {
            currentMovieTitle = card.dataset.title;

            document.getElementById('modalPoster').src      = card.dataset.poster;
            document.getElementById('modalTitle').textContent = card.dataset.title;
            document.getElementById('modalDescription').textContent = card.dataset.description;
            document.getElementById('modalMeta').innerHTML = `
                <span class="badge age">${card.dataset.age}</span>
                <span class="badge">${card.dataset.genre}</span>
                <span class="badge">${card.dataset.duration}</span>
                <span class="badge">${card.dataset.year}</span>
            `;
            document.getElementById('modalShowtimes').style.display = 'none';
            modal.classList.add('show');
        });
    });

    document.getElementById('closeModal').addEventListener('click', () => {
        modal.classList.remove('show');
    });

    // Sluit modal bij klik buiten content
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
    });

    document.getElementById('showTicketsBtn').addEventListener('click', () => {
        const times = document.getElementById('modalShowtimes');
        times.style.display = times.style.display === 'none' ? 'block' : 'none';
    });

    // Klik op tijdstip → ga naar reserveringspagina
    document.querySelectorAll('.modal-time').forEach(btn => {
        btn.addEventListener('click', () => {
            goToReserve(currentMovieTitle, btn.dataset.time);
        });
    });
}

// ── Navigeer naar reserveringspagina ────────────────────────────────────────
function goToReserve(movie, time) {
    // Sla ook op in local storage als tussentijdse voortgang
    const existing = JSON.parse(localStorage.getItem('pendingReservation') || '{}');
    existing.movie = movie;
    existing.time  = time;
    localStorage.setItem('pendingReservation', JSON.stringify(existing));

    window.location.href = 'reservation_create.php'
        + '?movie=' + encodeURIComponent(movie)
        + '&time='  + encodeURIComponent(time);
}

// ── Medewerker: zoekbalk filmlijst ───────────────────────────────────────────
const searchInput = document.getElementById('movieSearch');
if (searchInput) {
    searchInput.addEventListener('keyup', () => {
        const filter = searchInput.value.toLowerCase();
        document.querySelectorAll('.movieItem').forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
}

// ── Medewerker: bevestig verwijderen ─────────────────────────────────────────
function confirmDelete(title) {
    return confirm(`Ben je zeker dat je "${title}" wilt verwijderen?`);
}

// ── Reserveringen: live zoeken (manage pagina) ───────────────────────────────
const resSearch = document.getElementById('resSearch');
if (resSearch) {
    resSearch.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.res-row').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
}
