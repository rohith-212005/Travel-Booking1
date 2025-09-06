document.getElementById('search-button').addEventListener('click', function () {
    // Get the selected locations
    const fromLocation = document.getElementById('from-input').value;
    const toLocation = document.getElementById('to-input').value;

    // Get all flight items and hide them initially
    const flightItems = document.querySelectorAll('.flight-item');
    flightItems.forEach(item => item.style.display = 'none');

    // Filter and display matching flights
    flightItems.forEach(item => {
        if (item.getAttribute('data-from') === fromLocation && item.getAttribute('data-to') === toLocation) {
            item.style.display = 'block';
        }
    });
});
