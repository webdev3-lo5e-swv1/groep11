import { setCookie, getCookie } from './utils.js';
import { MovieModal }           from './MovieModal.js';
import { MovieFilter }          from './MovieFilter.js';
import { ReservationForm }      from './ReservationForm.js';

// ── Locaties dropdown ─────────────────────────────────────────────────────────
const bar      = document.getElementById('locationsBar');
const arrow    = document.getElementById('locationsArrow');
const dropdown = document.getElementById('locationsDropdown');
const label    = document.getElementById('locationsLabel');

if (bar && dropdown) {
    bar.addEventListener('click', () => {
        const isOpen = dropdown.classList.toggle('open');
        arrow?.classList.toggle('open', isOpen);
    });
    dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
        opt.addEventListener('click', e => {
            e.stopPropagation();
            dropdown.querySelectorAll('.dropdown-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            if (label) label.textContent = opt.textContent;
            dropdown.classList.remove('open');
            arrow?.classList.remove('open');
            setCookie('locatie', opt.dataset.value, 30);
            triggerFilter();
        });
    });
    document.addEventListener('click', e => {
        if (!bar.contains(e.target)) {
            dropdown.classList.remove('open');
            arrow?.classList.remove('open');
        }
    });
    const savedLocatie = getCookie('locatie');
    if (savedLocatie) {
        dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
            if (opt.dataset.value === savedLocatie) {
                opt.classList.add('active');
                if (label) label.textContent = opt.textContent;
            }
        });
    }
}

// ── Film filter + zoeken via API ──────────────────────────────────────────────
const filter = new MovieFilter('movieGrid');

const genreSelect     = document.getElementById('genreSelect');
const movieSearchHome = document.getElementById('movieSearchHome');

let filterTimer = null;
function triggerFilter() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        filter.fetchAndFilter(genreSelect?.value ?? '', movieSearchHome?.value ?? '');
    }, 200);
}
genreSelect?.addEventListener('change', triggerFilter);
movieSearchHome?.addEventListener('input', triggerFilter);

// ── Film modal ────────────────────────────────────────────────────────────────
const modal = new MovieModal('movieModal');

document.querySelectorAll('.movie-card').forEach(card => {
    card.addEventListener('click', () => {
        modal.open({
            id:          card.dataset.id,
            title:       card.dataset.title,
            posterPath:  card.dataset.poster,
            description: card.dataset.description,
            ageRating:   card.dataset.age,
            genre:       card.dataset.genre,
            duration:    card.dataset.duration,
            year:        card.dataset.year,
        });
    });
});

// ── Navigeer naar reserveringspagina ─────────────────────────────────────────
window.goToReserve = function(movie, time, movieId, showtimeId) {
    const pending = JSON.parse(localStorage.getItem('pendingReservation') || '{}');
    pending.movie      = movie;
    pending.time       = time;
    pending.movieId    = movieId    ?? null;
    pending.showtimeId = showtimeId ?? null;
    localStorage.setItem('pendingReservation', JSON.stringify(pending));

    let url = 'reservation_create.php?movie=' + encodeURIComponent(movie)
            + '&time='  + encodeURIComponent(time);
    if (movieId)    url += '&movie_id='    + encodeURIComponent(movieId);
    if (showtimeId) url += '&showtime_id=' + encodeURIComponent(showtimeId);
    window.location.href = url;
};

// ── Reserveringsformulier ─────────────────────────────────────────────────────
new ReservationForm('reservationForm');

// ── Medewerker filmzoek ───────────────────────────────────────────────────────
const searchInput = document.getElementById('movieSearch');
searchInput?.addEventListener('keyup', () => {
    const f = searchInput.value.toLowerCase();
    document.querySelectorAll('.movieItem').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(f) ? '' : 'none';
    });
});
