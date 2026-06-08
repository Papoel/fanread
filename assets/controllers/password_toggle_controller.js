import { Controller } from "@hotwired/stimulus";

/**
 * PasswordToggle — Stimulus Controller
 * =====================================
 * Affiche / masque un champ mot de passe.
 *
 * Targets :
 *   - input      → le <input type="password">
 *   - button     → le bouton toggle (pour aria-*)
 *   - iconShow   → icône œil ouvert  (visible quand mdp masqué)
 *   - iconHide   → icône œil barré   (visible quand mdp affiché)
 *
 * Usage :
 *   <div data-controller="password-toggle">
 *     <input type="password" data-password-toggle-target="input">
 *     <button type="button"
 *             data-action="click->password-toggle#toggle"
 *             data-password-toggle-target="button">
 *       <svg data-password-toggle-target="iconShow">…</svg>
 *       <svg data-password-toggle-target="iconHide" class="hidden">…</svg>
 *     </button>
 *   </div>
 *
 * Exportable : dépend uniquement de @hotwired/stimulus.
 */
export default class extends Controller {
    static targets = ["input", "button", "iconShow", "iconHide"];

    /** @type {boolean} */
    #visible = false;

    connect() {
        this.#visible = false;
        this.#syncUI();
    }

    toggle(event) {
        event.preventDefault();
        this.#visible = !this.#visible;
        this.#syncUI();
        this.inputTarget.focus();
    }

    // ── Privé ────────────────────────────────────────────────────────

    #syncUI() {
        this.inputTarget.type = this.#visible ? "text" : "password";

        if (this.hasIconShowTarget) {
            this.iconShowTarget.classList.toggle("hidden", this.#visible);
        }
        if (this.hasIconHideTarget) {
            this.iconHideTarget.classList.toggle("hidden", !this.#visible);
        }
        if (this.hasButtonTarget) {
            this.buttonTarget.setAttribute(
                "aria-label",
                this.#visible
                    ? "Masquer le mot de passe"
                    : "Afficher le mot de passe",
            );
            this.buttonTarget.setAttribute(
                "aria-pressed",
                String(this.#visible),
            );
        }
    }
}
