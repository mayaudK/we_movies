// public/js/autocomplete.js
document.getElementById('autocomplete').addEventListener('input', function() {
    const query = this.value;

    if (query.length < 2) {
        document.getElementById('suggestions').innerHTML = '';
        return;
    }

    fetch(`/autocomplete?query=${query}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('suggestions').innerHTML = data.map(item => `<div>${item.title}</div>`).join('');
        });
});