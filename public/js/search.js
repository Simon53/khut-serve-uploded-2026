// search.js
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');

    if (!searchInput) return;

    searchInput.addEventListener('keydown', function (e) {

        if (e.key !== 'Enter') {
            return;
        }

        e.preventDefault();

        const q = this.value.trim();

        if (q.length < 1) {
            return;
        }

        fetch(`${window.BASE_URL}/search?q=${encodeURIComponent(q)}`)
            .then(res => {

                if (!res.ok) {
                    throw new Error('Search request failed');
                }

                return res.json();
            })

            .then(data => {

                if (!data || !data.search_url) {

                    alert('Product not available');

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ALL MATCHING PRODUCTS-এর Search Page
                |--------------------------------------------------------------------------
                */

                window.location.href = data.search_url;

            })

            .catch(err => {

                console.error('Search Error:', err);

                alert('Something went wrong while searching.');

            });

    });

});