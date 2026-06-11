// ── ReservationForm class ─────────────────────────────────────────────────────
export class ReservationForm {
    #form;
    #storageKey = 'pendingReservation';
    #pricePerTicket = 10;

    constructor(formId) {
        this.#form = document.getElementById(formId);
        if (!this.#form) return;
        this.#restoreFromStorage();
        this.#bindEvents();
    }

    // Herstel eerder ingevulde gegevens uit local storage
    #restoreFromStorage() {
        try {
            const saved = JSON.parse(localStorage.getItem(this.#storageKey) || '{}');
            if (saved.naam)    this.#field('naam').value    = saved.naam;
            if (saved.email)   this.#field('email').value   = saved.email;
            if (saved.tickets) this.#field('tickets').value = saved.tickets;
        } catch (e) {}
    }

    #bindEvents() {
        // Sla voortgang op bij elke wijziging
        ['naam', 'email', 'tickets'].forEach(id => {
            this.#field(id)?.addEventListener('input', () => this.#saveProgress());
        });

        // Live prijsberekening
        this.#field('tickets')?.addEventListener('input', () => this.#updatePrice());

        // Validatie bij submit
        this.#form.addEventListener('submit', e => {
            if (!this.#validate()) e.preventDefault();
        });
    }

    #saveProgress() {
        const existing = JSON.parse(localStorage.getItem(this.#storageKey) || '{}');
        existing.naam    = this.#field('naam')?.value    ?? '';
        existing.email   = this.#field('email')?.value   ?? '';
        existing.tickets = this.#field('tickets')?.value ?? '1';
        localStorage.setItem(this.#storageKey, JSON.stringify(existing));
    }

    #updatePrice() {
        const n     = Math.max(1, parseInt(this.#field('tickets')?.value) || 1);
        const el    = document.getElementById('totalPrice');
        if (el) el.textContent = 'Totaal: €' + (n * this.#pricePerTicket).toFixed(2).replace('.', ',');
    }

    #validate() {
        let valid = true;
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        [this.#field('naam'), this.#field('email'), this.#field('tickets')]
            .forEach(el => el?.classList.remove('input-error'));

        const naam = this.#field('naam');
        if (!naam?.value.trim()) {
            this.#setError('err-naam', 'Naam is verplicht.', naam);
            valid = false;
        }

        const email = this.#field('email');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email?.value.trim() ?? '')) {
            this.#setError('err-email', 'Vul een geldig e-mailadres in.', email);
            valid = false;
        }

        const tickets = this.#field('tickets');
        const t = parseInt(tickets?.value ?? '0');
        if (isNaN(t) || t < 1 || t > 10) {
            this.#setError('err-tickets', 'Kies 1 tot 10 tickets.', tickets);
            valid = false;
        }

        return valid;
    }

    #setError(errId, msg, field) {
        const el = document.getElementById(errId);
        if (el) el.textContent = msg;
        field?.classList.add('input-error');
    }

    #field(id) {
        return document.getElementById(id);
    }

    clearStorage() {
        localStorage.removeItem(this.#storageKey);
    }
}
