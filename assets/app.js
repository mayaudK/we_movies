/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


document.addEventListener('DOMContentLoaded', function () {
    // Event listener for fetching movie details and showing the modal{
    document.body.addEventListener('click', async function (event) {
        if (event.target.tagName === 'BUTTON') {
            const movieId = event.target.id;
            const response = await fetch(`/movie/${movieId}`);
            const movie = await response.json();
            const videoKey = await getYoutubeVideoKey(movie.id);
            await showMovieModal(movie, videoKey);
        }
    });

    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener('click', async function () {
            const genre = this.value;
            const response = await fetch(`/moviesByGenre/${genre}`);
            const movies = await response.json();
            const moviesContainer = document.getElementById('movieList');
            moviesContainer.innerHTML = movies.map(movie => `
                <div class="movie">
                    <div class="container-image">
                        <img class="image" src="https://image.tmdb.org/t/p/original/${movie.poster_path}" alt="${movie.title}">
                    </div>
                    <div class="movieContent">
                        <div class="titleRateContainer">
                            <h2>${movie.title}</h2>
                            <div class="titleRate">Rating: ${movie.rating}</div>
                            <div class="titleRate">(${movie.vote_count} votes)</div>
                        </div>
                        <div class="release-date">${new Date(movie.release_date).getFullYear()}</div><!--Studios-->
                        <div class="movie-overview">${movie.overview}</div>
                        <div class="button-container">
                            <button class="button" id="${movie.id}">Lire le détail</button>
                        </div>
                    </div>
                </div>
            `).join('');
        });
    });

    async function showMovieModal(movie, videoKey) {
        document.getElementById('modalTitleH2').innerText = `${movie.title} Bande-annonce`;
        document.getElementById('modalTitle').innerText = `Film: ${movie.title} - Année de sortie: ${new Date(movie.release_date).getFullYear()} - `;
        document.getElementById('modalVoteCount').innerText = ` pour ${movie.vote_count} utilisateurs`;
        document.getElementById('modalVideoPlayer').innerHTML = `
                <iframe src="https://www.youtube.com/embed/${videoKey}" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        `;
        const movieModal = document.getElementById('movieModal');
        movieModal.style.display = "block";

        document.querySelector('.close').onclick = function () {
            movieModal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == movieModal) {
                movieModal.style.display = "none";
            }
        }
    }

    async function getYoutubeVideoKey(movieId) {
        const response = await fetch(`/movie/${movieId}/trailer`);
        const video = await response.json();
        return video.key;
    }


    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', async function () {
            const rating = this.getAttribute('data-value');
            const movieId = this.closest('.rating').getAttribute('data-movie-id');

            try {
                const response = await fetch(`/rateMovie/${movieId}/${rating}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({rating: rating})
                });

                if (response.ok) {
                    updateStarDisplay(this);
                } else {
                    console.error('Error submitting rating');
                }
            } catch (error) {
                console.error('Fetch error:', error);
            }
        });
    });


    function updateStarDisplay(selectedStar) {
        const stars = selectedStar.closest('.rating').querySelectorAll('.star');
        stars.forEach(star => {
            star.classList.remove('selected');
        });
        selectedStar.classList.add('selected');
        let previousSibling = selectedStar.previousElementSibling;
        while (previousSibling) {
            previousSibling.classList.add('selected');
            previousSibling = previousSibling.previousElementSibling;
        }
    }

    const modalVote = document.getElementById('voteModal');
    const closeModal = document.querySelector('.modal .close');

    function showModalVote() {
        modalVote.style.display = 'block';
    }

    function hideModalVote() {
        modalVote.style.display = 'none';
    }

    function handleStarClick() {
        showModalVote();
    }

    function initStarRating() {
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', handleStarClick);
        });
    }

    closeModal.addEventListener('click', hideModalVote);
    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            hideModalVote();
        }
    });

    initStarRating();
});




