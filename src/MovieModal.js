// ── MovieModal class ──────────────────────────────────────────────────────────
export class MovieModal {
    #modal;
    #currentTitle  = '';
    #currentId     = null;
    #showtimeCache = {};

    constructor(modalId) {
        this.#modal = document.getElementById(modalId);
        if (!this.#modal) return;
        this.#bindClose();
        this.#bindTicketBtn();
    }

    #bindClose() {
        document.getElementById('closeModal')?.addEventListener('click', () => this.close());
        this.#modal.addEventListener('click', e => { if (e.target === this.#modal) this.close(); });
    }

    #bindTicketBtn() {
        document.getElementById('showTicketsBtn')?.addEventListener('click', () => {
            const box = document.getElementById('modalShowtimes');
            if (!box) return;
            const isHidden = box.style.display === 'none' || box.style.display === '';
            if (isHidden) {
                this.#loadShowtimes();
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        });
    }

    open(movie) {
        this.#currentTitle = movie.title;
        this.#currentId    = movie.id ?? null;

        document.getElementById('modalPoster').src                  = movie.posterPath ?? '';
        document.getElementById('modalTitle').textContent           = movie.title;
        document.getElementById('modalDescription').textContent     = movie.description ?? '';
        document.getElementById('modalMeta').innerHTML = `
            <span class="badge age">${this.#esc(movie.ageRating ?? '')}</span>
            <span class="badge">${this.#esc(movie.genre ?? '')}</span>
            <span class="badge">${this.#esc(movie.duration ?? '')}</span>
            <span class="badge">${this.#esc(String(movie.year ?? ''))}</span>
        `;

        const box = document.getElementById('modalShowtimes');
        if (box) {
            box.style.display = 'none';
            box.innerHTML = '';
        }

        this.#modal.classList.add('show');
    }

    async #loadShowtimes() {
        if (!this.#currentId) {
            this.#renderNoShowtimes('Geen film-ID beschikbaar.');
            return;
        }

        const box = document.getElementById('modalShowtimes');
        if (!box) return;

        // Cache: niet opnieuw fetchen voor dezelfde film
        if (this.#showtimeCache[this.#currentId]) {
            this.#renderShowtimes(this.#showtimeCache[this.#currentId]);
            return;
        }

        box.innerHTML = '<p style="color:#aaa;font-size:.9rem;">Tijden laden...</p>';

        try {
            const res  = await fetch(`api/showtimes.php?movie_id=${this.#currentId}`);
            const data = await res.json();
            this.#showtimeCache[this.#currentId] = data.showtimes ?? [];
            this.#renderShowtimes(this.#showtimeCache[this.#currentId]);
        } catch (e) {
            this.#renderNoShowtimes('Kon tijden niet laden.');
        }
    }

    #renderShowtimes(showtimes) {
        const box = document.getElementById('modalShowtimes');
        if (!box) return;

        if (!showtimes.length) {
            this.#renderNoShowtimes('Geen vertoningen gepland voor deze film.');
            return;
        }

        // Groepeer op locatie (city)
        const byCity = {};
        showtimes.forEach(s => {
            if (!byCity[s.city]) byCity[s.city] = [];
            byCity[s.city].push(s);
        });

        let html = '<p class="showtimes-label">Kies een tijdstip:</p>';

        Object.entries(byCity).forEach(([city, times]) => {
            html += `<p class="showtime-city">${this.#esc(city)}</p><div class="showtime-times">`;
            times.forEach(s => {
                const label     = `${s.time} — ${this.#esc(s.hall)}`;
                const timeValue = s.start_time;
                html += `<button class="time-btn modal-time"
                                  data-time="${this.#esc(timeValue)}"
                                  data-showtime-id="${s.id}"
                                  title="${this.#esc(s.cinema)} · ${this.#esc(s.hall)} · ${s.seats} stoelen">
                             ${s.time}
                             <span class="time-hall">${this.#esc(s.hall)}</span>
                         </button>`;
            });
            html += '</div>';
        });

        box.innerHTML = html;

        // Bind click events op de nieuwe knoppen
        box.querySelectorAll('.modal-time').forEach(btn => {
            btn.addEventListener('click', () => {
                window.goToReserve?.(this.#currentTitle, btn.dataset.time, this.#currentId, btn.dataset.showtimeId);
            });
        });
    }

    #renderNoShowtimes(msg) {
        const box = document.getElementById('modalShowtimes');
        if (box) box.innerHTML = `<p style="color:#aaa;font-size:.9rem;">${msg}</p>`;
    }

    close() {
        this.#modal.classList.remove('show');
    }

    getCurrentTitle() { return this.#currentTitle; }
    getCurrentId()    { return this.#currentId; }

    #esc(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
}
