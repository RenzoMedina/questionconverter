/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "../templates/**/*.mustache", 
    "../**/*.php",
    "../js/**/*.js",

  ],
  theme: {
    extend: {
     width: {
       '800': '800px',
     },
     inset: {
       '100': '40rem',
       '18.7': '18.7rem',
     },
    },
  },
  plugins: [],
}
