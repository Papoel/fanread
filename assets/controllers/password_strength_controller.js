import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input", "bar", "label", "critLength", "critUpper", "critDigit", "critSpecial"];
    static values  = { min: { type: Number, default: 8 } };

    evaluate() {
        const password = this.inputTarget.value;
        const criteria = this._assess(password);
        const score    = this._score(password, criteria);
        this._syncBar(score);
        this._syncLabel(score);
        this._syncCriteria(criteria);
    }

    _assess(password) {
        return {
            length:  password.length >= this.minValue,
            upper:   /[A-Z]/.test(password),
            digit:   /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password),
        };
    }

    _score(password, criteria) {
        if (password.length === 0) return 0;
        const met = Object.values(criteria).filter(Boolean).length;
        return Math.min(4, Math.max(1, met));
    }

    _syncBar(score) {
        if (!this.hasBarTarget) return;
        const colors = ['transparent', 'hsl(0 84% 60%)', 'hsl(25 95% 53%)', 'hsl(45 93% 47%)', 'hsl(142 71% 45%)'];
        const pcts   = [0, 25, 50, 75, 100];
        this.barTarget.style.width           = `${pcts[score]}%`;
        this.barTarget.style.backgroundColor = colors[score];
    }

    _syncLabel(score) {
        if (!this.hasLabelTarget) return;
        const labels = ['', 'Très faible', 'Faible', 'Correct', 'Fort'];
        const colors = ['', 'hsl(0 84% 60%)', 'hsl(25 95% 53%)', 'hsl(45 93% 47%)', 'hsl(142 71% 45%)'];
        this.labelTarget.textContent = labels[score];
        this.labelTarget.style.color = colors[score];
    }

    _syncCriteria(criteria) {
        const map = {
            critLength:  criteria.length,
            critUpper:   criteria.upper,
            critDigit:   criteria.digit,
            critSpecial: criteria.special,
        };
        for (const [name, met] of Object.entries(map)) {
            const hasProp = `has${name[0].toUpperCase()}${name.slice(1)}Target`;
            if (!this[hasProp]) continue;
            const el = this[`${name}Target`];
            el.style.color = met ? 'hsl(142 71% 45%)' : 'hsl(var(--muted-foreground))';
            el.querySelector('[data-check]')?.classList.toggle('hidden', !met);
            el.querySelector('[data-cross]')?.classList.toggle('hidden', met);
        }
    }
}
