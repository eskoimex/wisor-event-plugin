//INFINITE SCROLL
document.addEventListener('DOMContentLoaded', function () {
    let page = 2;
    const eventsList = document.querySelector('.wisor-events-list');
    const loadMoreButton = document.getElementById('load-more-events');

    if (loadMoreButton && eventsList) {
        loadMoreButton.addEventListener('click', function () {
            fetch(`/wp-json/wp/v2/events?page=${page}`)
                .then(response => response.json())
                .then(events => {
                    if (events.length > 0) {
                        events.forEach(event => {
                            const eventItem = document.createElement('li');
                            eventItem.innerHTML = `
                                <h3>${event.title.rendered}</h3>
                                <p>${event.content.rendered}</p>
                                <span>${event.meta.event_date}</span>
                            `;
                            eventsList.appendChild(eventItem);
                        });
                        page++;
                    } else {
                        loadMoreButton.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading more events:', error));
        });
    }
});

//DATE FILTER
document.addEventListener('DOMContentLoaded', function () {
    const dateFilter = document.getElementById('date-filter');
    const eventsList = document.querySelector('.wisor-events-list');

    if (dateFilter && eventsList) {
        dateFilter.addEventListener('change', function () {
            const selectedFilter = this.value;
            const eventItems = eventsList.querySelectorAll('li');

            eventItems.forEach(item => {
                const eventDate = item.querySelector('span').textContent;
                const currentDate = new Date().toISOString().split('T')[0];

                if (selectedFilter === 'all') {
                    item.style.display = 'block';
                } else if (selectedFilter === 'past' && eventDate < currentDate) {
                    item.style.display = 'block';
                } else if (selectedFilter === 'future' && eventDate >= currentDate) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});