import { Controller } from "@hotwired/stimulus";

/**
 * AutoSubmit — Stimulus Controller
 * ===================================
 * Soumet le formulaire parent dès qu'un champ change.
 * Utilise requestSubmit() pour déclencher la validation HTML native.
 *
 * Usage :
 *   <form data-controller="auto-submit">
 *     <select data-action="change->auto-submit#submit">…</select>
 *   </form>
 */
export default class extends Controller {
    submit() {
        this.element.requestSubmit();
    }
}
