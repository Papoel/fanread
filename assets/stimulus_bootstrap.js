import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
app.debug = false; // 🔎 DEBUG : log chaque controller (dé)connecté
window.Stimulus = app; // 🔎 DEBUG : inspectable en console
