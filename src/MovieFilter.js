// ── MovieFilter class — haalt films op via fetch en filtert live ──────────────
export class MovieFilter {
    #cards;
    #cache = null;
    #lastParams = '';

    constructor(gridId) {
        this.#cards = document.querySelectorAll(`#${gridId} .movie-card`);
    }

    /**
     * Haal films op via de API (met caching).
     * Onnodige aanvragen worden voorkomen via #lastParams.
     */
    async fetchAndFilter(genre = '', search = '') {
        const params = `genre=${encodeURIComponent(genre)}&search=${encodeURIComponent(search)}`;

        // Gebruik cache als parameters hetzelfde zijn
        if (params === this.#lastParams && this.#cache !== null) {
            this.#renderFromCache(this.#cache);
            return;
        }

        try {
            const res  = await fetch(`api/movies.php?${params}`);
            const data = await res.json();
            this.#cache      = data.movies;
            this.#lastParams = params;
            this.#renderFromCache(data.movies);
        } catch (e) {
            // Fallback: filter de bestaande DOM-cards
            this.#filterDOM(genre, search);
        }
    }

    #renderFromCache(movies) {
        const ids = new Set(movies.map(m => String(m.id)));
        this.#cards.forEach(card => {
            card.style.display = ids.has(card.dataset.id) ? '' : 'none';
        });
    }

    #filterDOM(genre, search) {
        this.#cards.forEach(card => {
            const matchGenre  = !genre  || (card.dataset.genre  || '').toLowerCase().includes(genre.toLowerCase());
            const matchSearch = !search || (card.dataset.title  || '').toLowerCase().includes(search.toLowerCase());
            card.style.display = (matchGenre && matchSearch) ? '' : 'none';
        });
    }
}
