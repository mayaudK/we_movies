/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// fetch movies by genre with a click on a radio button

document.querySelectorAll('input[type="radio"]').forEach((radio) => {
    radio.addEventListener('click', async function() {
        const genre = this.value;
        console.log("Fetch genre: " + genre);
        const response = await fetch(`/moviesByGenre/${genre}`);
        const movies = await response.json();
        const moviesContainer = document.getElementById('movieList');
        moviesContainer.innerHTML = movies.map(movie => `<li>${movie.title}</li>`).join('');
    });
});

