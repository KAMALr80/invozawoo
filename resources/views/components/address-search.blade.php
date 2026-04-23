{{-- resources/views/components/address-search.blade.php --}}
<div class="address-search-component">
    <div class="form-group">
        <label>{{ $label ?? 'Search Address' }}</label>
        <div class="input-group">
            <input type="text"
                   id="address-search-{{ $id ?? '1' }}"
                   class="form-control"
                   placeholder="Type address..."
                   autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="use-current-location-{{ $id ?? '1' }}">
                    <i class="fas fa-location-dot"></i> Current
                </button>
            </div>
        </div>
        <div id="search-results-{{ $id ?? '1' }}" class="search-results"></div>
        <input type="hidden" id="latitude-{{ $id ?? '1' }}" name="latitude" value="{{ $latitude ?? '' }}">
        <input type="hidden" id="longitude-{{ $id ?? '1' }}" name="longitude" value="{{ $longitude ?? '' }}">
    </div>
</div>

@push('styles')
<style>
    .search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .search-result-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    .search-result-item:hover {
        background: #f0f0f0;
    }
    .search-result-item small {
        color: #666;
        display: block;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('address-search-{{ $id ?? '1' }}');
    const resultsDiv = document.getElementById('search-results-{{ $id ?? '1' }}');
    const latInput = document.getElementById('latitude-{{ $id ?? '1' }}');
    const lngInput = document.getElementById('longitude-{{ $id ?? '1' }}');
    const currentBtn = document.getElementById('use-current-location-{{ $id ?? '1' }}');

    let searchTimeout;

    // Search as you type
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;

        if (query.length < 3) {
            resultsDiv.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=in&limit=5`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <strong>${item.display_name}</strong>
                                <small>Lat: ${item.lat}, Lng: ${item.lon}</small>
                            `;
                            div.addEventListener('click', function() {
                                searchInput.value = item.display_name;
                                latInput.value = item.lat;
                                lngInput.value = item.lon;
                                resultsDiv.style.display = 'none';

                                // Trigger change event
                                const event = new Event('change', { bubbles: true });
                                latInput.dispatchEvent(event);
                            });
                            resultsDiv.appendChild(div);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                });
        }, 500);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    // Use current location
    currentBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported');
            return;
        }

        currentBtn.disabled = true;
        currentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        searchInput.value = data.display_name;
                        latInput.value = lat;
                        lngInput.value = lng;

                        currentBtn.disabled = false;
                        currentBtn.innerHTML = '<i class="fas fa-location-dot"></i> Current';

                        // Trigger change event
                        const event = new Event('change', { bubbles: true });
                        latInput.dispatchEvent(event);
                    });
            },
            function(error) {
                alert('Error getting location: ' + error.message);
                currentBtn.disabled = false;
                currentBtn.innerHTML = '<i class="fas fa-location-dot"></i> Current';
            }
        );
    });
});
</script>
@endpush
