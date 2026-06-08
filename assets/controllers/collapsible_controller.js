import { Controller } from "@hotwired/stimulus";

/**
 * Collapsible — Stimulus Controller
 * ====================================
 * Affiche / masque un panneau avec animation max-height fluide.
 *
 * Targets :
 *   - content  → le panneau à ouvrir/fermer
 *   - trigger  → le bouton (classes actives/inactives)
 *   - label    → texte du bouton (data-open-text / data-closed-text)
 *   - icon     → icône + qui pivote à 45° quand ouvert
 */
export default class extends Controller {
    static values = { open: Boolean };
    static targets = ["content", "trigger", "label", "icon"];

    connect() {
        // Initialise sans animation au montage
        this.#applyState(false);
    }

    toggle() {
        this.openValue = !this.openValue;
        this.#applyState(true);
    }

    // ── Privé ────────────────────────────────────────────────────────

    #applyState(animate) {
        const isOpen = this.openValue;

        if (this.hasContentTarget) {
            isOpen
                ? this.#expand(this.contentTarget, animate)
                : this.#collapse(this.contentTarget, animate);
        }

        if (this.hasIconTarget) {
            this.iconTarget.classList.toggle("rotate-45", isOpen);
        }

        if (this.hasLabelTarget) {
            const t = this.labelTarget;
            t.textContent = isOpen
                ? (t.dataset.openText ?? "Fermer")
                : (t.dataset.closedText ?? "Ouvrir");
        }

        if (this.hasTriggerTarget) {
            const btn = this.triggerTarget;
            btn.classList.toggle("bg-muted", isOpen);
            btn.classList.toggle("text-foreground", isOpen);
            btn.classList.toggle("bg-primary", !isOpen);
            btn.classList.toggle("text-white", !isOpen);
            btn.classList.toggle("shadow-primary-lg", !isOpen);
        }
    }

    /**
     * Annule toute animation en cours avant d'en démarrer une nouvelle.
     * Évite les états intermédiaires bloqués.
     */
    #cancelAnimation(el) {
        if (el._animHandler) {
            el.removeEventListener("transitionend", el._animHandler);
            el._animHandler = null;
        }
        // Remet les styles à zéro proprement
        el.style.transition = "";
        el.style.maxHeight = "";
        el.style.overflow = "";
    }

    #expand(el, animate) {
        this.#cancelAnimation(el);
        el.classList.remove("hidden");

        if (!animate) return;

        const targetHeight = el.scrollHeight;

        el.style.overflow = "hidden";
        el.style.maxHeight = "0";
        el.style.transition = "";

        // Force reflow : garantit que le navigateur enregistre
        // l'état initial (max-height: 0) avant de démarrer la transition.
        // Sans ça, certains navigateurs ignorent l'animation.
        el.offsetHeight; // eslint-disable-line no-unused-expressions

        el.style.transition = "max-height 0.3s ease-out";
        el.style.maxHeight = targetHeight + "px";

        const handler = (e) => {
            // Ignore les transitionend qui remontent des éléments enfants
            // (inputs, buttons ont transition-all → émettent leurs propres events)
            if (e.target !== el || e.propertyName !== "max-height") return;

            el.removeEventListener("transitionend", handler);
            el._animHandler = null;
            // Libère la contrainte pour que le contenu puisse changer de taille
            el.style.maxHeight = "";
            el.style.overflow = "";
            el.style.transition = "";
        };

        el._animHandler = handler;
        el.addEventListener("transitionend", handler);
    }

    #collapse(el, animate) {
        this.#cancelAnimation(el);

        if (!animate) {
            el.classList.add("hidden");
            return;
        }

        const currentHeight = el.scrollHeight;

        el.style.overflow = "hidden";
        el.style.maxHeight = currentHeight + "px";
        el.style.transition = "";

        // Force reflow : même raison que dans #expand
        el.offsetHeight; // eslint-disable-line no-unused-expressions

        el.style.transition = "max-height 0.3s ease-out";
        el.style.maxHeight = "0";

        const handler = (e) => {
            if (e.target !== el || e.propertyName !== "max-height") return;

            el.removeEventListener("transitionend", handler);
            el._animHandler = null;
            el.classList.add("hidden"); // ← Masque réellement l'élément
            el.style.maxHeight = "";
            el.style.overflow = "";
            el.style.transition = "";
        };

        el._animHandler = handler;
        el.addEventListener("transitionend", handler);
    }
}
