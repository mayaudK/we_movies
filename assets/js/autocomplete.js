document.addEventListener('DOMContentLoaded', function() {
    const suggestionsContainer = document.getElementById('suggestions');
    let moviesData = {};
    document.getElementById('autocomplete').addEventListener('input', async function () {
        const query = this.value;
        if (query.length < 2) {
            clearSuggestions();
            return;
        }

        const response = await fetch(`/autocomplete?query=${query}`)
        const movies = await response.json();
        moviesData = {};
        suggestionsContainer.innerHTML = movies.map(movie => {
            moviesData[movie.id] = movie;
            return `
                    <div class="suggestion" data-movie-id="${movie.id}">
                        ${movie.title}
                    </div>
                `;
        }).join('');
        if (movies.length) {
            suggestionsContainer.style.display = 'block';
        } else {
            clearSuggestions();
        }
    });

    suggestionsContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('suggestion')) {
            const movieId = event.target.getAttribute('data-movie-id');
            const movie = moviesData[movieId];

            const moviesContainer = document.getElementById('movieList');
            moviesContainer.innerHTML = `
                <div class="movie">
                    <div class="container-image">
                        <img class="image" src="https://image.tmdb.org/t/p/original/${movie.poster_path}" alt="${movie.title}">
                    </div>
                    <div class="movieContent">
                        <div class="titleRateContainer">
                            <h2>${movie.title}</h2>
                            <div class="titleRate">Rating: ${movie.vote_average}</div>
                            <div class="titleRate">(${movie.vote_count} votes)</div>
                        </div>
                        <div class="release-date">${new Date(movie.release_date).getFullYear()}</div>
                        <div class="movie-overview">${movie.overview}</div>
                        <div class="button-container">
                            <button class="button" id="${movie.id}">Lire le détail</button>
                        </div>
                    </div>
                </div>
            `;
        }
    });

});

function clearSuggestions() {
    const suggestionsContainer = document.getElementById('suggestions');
    suggestionsContainer.innerHTML = '';
    suggestionsContainer.style.display = 'none';
}