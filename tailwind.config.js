/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig', // Tous les fichiers Twig dans le dossier templates
    './assets/**/*.js',          // Les fichiers JS dans assets
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
