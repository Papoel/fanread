import { Controller } from "@hotwired/stimulus";

/**
 * Dropdown — Stimulus Controller
 * ================================
 * Gère l'ouverture / fermeture d'un menu déroulant.
 * Ferme automatiquement sur clic extérieur et touche Escape.
 *
 * Targets :
 *   - menu    → le panneau à afficher/masquer
 *   - chevron → (optionnel) icône qui pivote à l'ouverture
 *
 * Usage :
 *   <div data-controller="dropdown">
 *     <button data-action="click->dropdown#toggle">Ouvrir</button>
 *     <div data-dropdown-target="menu" class="hidden">…</div>
 *   </div>
 *
 * Exportable : dépend uniquement de @hotwired/stimulus.
 */
export default class extends Controller {
    static targets = ["menu", "chevron"];

    #isOpen = false;
    #onOutsideClick = null;
    #onEscKey = null;

    connect() {
        this.#isOpen = false;
        this.#onOutsideClick = this.#handleOutsideClick.bind(this);
        this.#onEscKey = this.#handleEscKey.bind(this);
    }

    disconnect() {
        this.#removeListeners();
    }

    toggle(event) {
        event.stopPropagation();
        this.#isOpen ? this.#close() : this.#show();
    }

    // ── Privé ────────────────────────────────────────────────────────

    #show() {
        this.#isOpen = true;
        this.menuTarget.classList.remove("hidden");
        if (this.hasChevronTarget) {
            this.chevronTarget.style.transform = "rotate(180deg)";
        }
        document.addEventListener("click", this.#onOutsideClick);
        document.addEventListener("keydown", this.#onEscKey);
    }

    #close() {
        this.#isOpen = false;
        this.menuTarget.classList.add("hidden");
        if (this.hasChevronTarget) {
            this.chevronTarget.style.transform = "";
        }
        this.#removeListeners();
    }

    #handleOutsideClick(event) {
        if (!this.element.contains(event.target)) {
            this.#close();
        }
    }

    #handleEscKey(event) {
        if (event.key === "Escape") {
            this.#close();
        }
    }

    #removeListeners() {
        document.removeEventListener("click", this.#onOutsideClick);
        document.removeEventListener("keydown", this.#onEscKey);
    }
}
