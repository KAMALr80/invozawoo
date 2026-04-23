{{-- resources/views/components/address-search-with-map.blade.php --}}
@props(['id', 'label', 'required' => false])

<div x-data="addressSearchWithMap()" x-init="init()" class="address-search-component mb-3">
    <div class="form-group">
        <label class="form-label">
            {{ $label }}
            @if ($required)
                <span class="required-star">*</span>
            @endif
        </label>

        <div class="position-relative">
            {{-- Address Input with Map Button --}}
            <div class="d-flex gap-2">
                <div class="flex-grow-1 position-relative">
                    <input type="text" x-model="search" @input.debounce.500ms="searchAddress"
                        @focus="showResults = true" class="form-control" placeholder="Type address to search..."
                        autocomplete="off" :class="{ 'border-primary': loading }">

                    {{-- Loading Spinner --}}
                    <div x-show="loading" class="position-absolute" style="right: 10px; top: 12px; z-index: 10;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                {{-- Map Button --}}
                <button type="button" class="btn btn-outline-primary" @click="openMapModal"
                    style="width: 45px; height: 45px; border-radius: 8px;" title="Select on Map">
                    <i class="fas fa-map-marked-alt"></i>
                </button>
            </div>

            {{-- Search Results Dropdown --}}
            <div x-show="showResults && results.length > 0" @click.away="showResults = false"
                class="list-group mt-1 position-absolute"
                style="max-height: 250px; overflow-y: auto; z-index: 1000; width: calc(100% - 60px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white;">
                <template x-for="(result, index) in results" :key="index">
                    <button type="button" class="list-group-item list-group-item-action"
                        @click="selectAddress(result)">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-map-pin text-primary mt-1"></i>
                            <div class="text-start">
                                <strong
                                    x-text="result.display_name.substring(0, 60) + (result.display_name.length > 60 ? '...' : '')"></strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-location-dot"></i>
                                    <span
                                        x-text="parseFloat(result.lat).toFixed(6) + ', ' + parseFloat(result.lon).toFixed(6)"></span>
                                </small>
                            </div>
                        </div>
                    </button>
                </template>
            </div>

            {{-- Selected Address Display --}}
            <div x-show="selectedAddress" class="mt-2 p-3 bg-light rounded border">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success">✓ Selected</span>
                            <small class="text-muted">Click map icon to change</small>
                        </div>
                        <p class="mb-1 fw-medium" x-text="selectedAddress"></p>
                        <div class="d-flex gap-3 small text-muted">
                            <span><i class="fas fa-latitude"></i> Lat: <span
                                    x-text="selectedLat.toFixed(6)"></span></span>
                            <span><i class="fas fa-longitude"></i> Lng: <span
                                    x-text="selectedLng.toFixed(6)"></span></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" @click="clearSelection" title="Clear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Hidden Inputs for Form Submission --}}
            <input type="hidden" name="destination_latitude" :value="selectedLat" id="destination_latitude">
            <input type="hidden" name="destination_longitude" :value="selectedLng" id="destination_longitude">
            <input type="hidden" name="formatted_address" :value="selectedAddress" id="formatted_address">
        </div>
    </div>

    {{-- Map Modal --}}
    <div class="modal fade" id="mapModal{{ $id }}" tabindex="-1" x-ref="mapModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Select Location on Map
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="closeMapModal"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    {{-- Map Container --}}
                    <div id="map{{ $id }}" style="height: 400px; width: 100%;"></div>

                    {{-- Search Box Overlay --}}
                    <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 1000; pointer-events: none;">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <input type="text" class="form-control shadow"
                                    placeholder="Search location on map..." x-ref="mapSearch"
                                    @keyup.enter="searchMapLocation" style="pointer-events: auto;">
                            </div>
                        </div>
                    </div>

                    {{-- Selected Coordinates Info --}}
                    <div class="position-absolute bottom-0 start-0 w-100 p-3" style="z-index: 1000;">
                        <div class="bg-white p-2 rounded shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-map-pin text-primary"></i>
                                    <span x-text="mapLat ? mapLat.toFixed(6) : '0.000000'"></span>,
                                    <span x-text="mapLng ? mapLng.toFixed(6) : '0.000000'"></span>
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success me-2" @click="confirmMapLocation">
                                    <i class="fas fa-check me-1"></i> Confirm
                                </button>
                                <button class="btn btn-sm btn-secondary" @click="closeMapModal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Crosshair Center Marker --}}
                    <div class="position-absolute top-50 start-50 translate-middle"
                        style="z-index: 1000; pointer-events: none;">
                        <div
                            style="
                            width: 30px;
                            height: 30px;
                            border: 3px solid #3b82f6;
                            border-radius: 50%;
                            background: rgba(59, 130, 246, 0.2);
                            transform: translate(-50%, -50%);
                            box-shadow: 0 0 0 2px white;
                        ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .address-search-component .border-primary {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .leaflet-container {
            border-radius: 0;
        }

        .leaflet-control-attribution {
            font-size: 8px;
        }

        .list-group-item:hover {
            background-color: #f8fafc;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function addressSearchWithMap() {
            return {
                search: '',
                results: [],
                showResults: false,
                loading: false,
                selectedAddress: '',
                selectedLat: 0,
                selectedLng: 0,
                map: null,
                marker: null,
                mapLat: 22.524768,
                mapLng: 72.955568,
                mapInitialized: false,

                init() {
                    // Load from old input if validation fails
                    const oldLat = document.getElementById('destination_latitude')?.value;
                    const oldLng = document.getElementById('destination_longitude')?.value;
                    const oldAddress = document.getElementById('formatted_address')?.value;

                    if (oldLat && oldLng && oldLat != 0 && oldLng != 0) {
                        this.selectedLat = parseFloat(oldLat);
                        this.selectedLng = parseFloat(oldLng);
                        this.selectedAddress = oldAddress;
                        this.mapLat = this.selectedLat;
                        this.mapLng = this.selectedLng;
                    }
                },

                async searchAddress() {
                    if (this.search.length < 3) {
                        this.results = [];
                        this.showResults = false;
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.search)}&limit=5&countrycodes=in`
                        );
                        const data = await response.json();
                        this.results = data;
                        this.showResults = true;
                    } catch (error) {
                        console.error('Search error:', error);
                        this.results = [];
                        showToast('Address search failed', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                selectAddress(result) {
                    this.selectedAddress = result.display_name;
                    this.selectedLat = parseFloat(result.lat);
                    this.selectedLng = parseFloat(result.lon);
                    this.search = result.display_name;
                    this.mapLat = this.selectedLat;
                    this.mapLng = this.selectedLng;
                    this.results = [];
                    this.showResults = false;

                    // Trigger change event for any listeners
                    this.$dispatch('address-selected', {
                        lat: this.selectedLat,
                        lng: this.selectedLng,
                        address: this.selectedAddress
                    });

                    showToast('Address selected successfully', 'success');
                },

                clearSelection() {
                    this.selectedAddress = '';
                    this.selectedLat = 0;
                    this.selectedLng = 0;
                    this.search = '';
                    this.mapLat = 22.524768;
                    this.mapLng = 72.955568;

                    // Clear hidden inputs
                    document.getElementById('destination_latitude').value = '';
                    document.getElementById('destination_longitude').value = '';
                    document.getElementById('formatted_address').value = '';

                    showToast('Address cleared', 'info');
                },

                openMapModal() {
                    const modal = new bootstrap.Modal(document.getElementById('mapModal{{ $id }}'));

                    // Set initial map position
                    if (this.selectedLat && this.selectedLng) {
                        this.mapLat = this.selectedLat;
                        this.mapLng = this.selectedLng;
                    }

                    modal.show();

                    // Initialize map after modal is shown
                    setTimeout(() => {
                        this.initMap();
                    }, 300);
                },

                initMap() {
                    if (this.mapInitialized && this.map) {
                        this.map.invalidateSize();
                        return;
                    }

                    // Create map
                    this.map = L.map('map{{ $id }}').setView([this.mapLat, this.mapLng], 15);

                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(this.map);

                    // Add draggable marker
                    this.marker = L.marker([this.mapLat, this.mapLng], {
                        draggable: true,
                        icon: L.divIcon({
                            html: `<div style="
                        width: 30px;
                        height: 30px;
                        background: linear-gradient(135deg, #3b82f6, #2563eb);
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                    "></div>`,
                            className: 'custom-div-icon',
                            iconSize: [30, 30]
                        })
                    }).addTo(this.map);

                    // Update coordinates on marker drag
                    this.marker.on('dragend', (e) => {
                        const position = e.target.getLatLng();
                        this.mapLat = position.lat;
                        this.mapLng = position.lng;
                    });

                    // Update on map click
                    this.map.on('click', (e) => {
                        this.mapLat = e.latlng.lat;
                        this.mapLng = e.latlng.lng;
                        this.marker.setLatLng([this.mapLat, this.mapLng]);
                    });

                    // Add search control
                    const searchInput = this.$refs.mapSearch;
                    if (searchInput) {
                        searchInput.addEventListener('keypress', (e) => {
                            if (e.key === 'Enter') {
                                this.searchMapLocation();
                            }
                        });
                    }

                    this.mapInitialized = true;
                },

                async searchMapLocation() {
                    const searchInput = this.$refs.mapSearch;
                    const query = searchInput.value;

                    if (!query || query.length < 3) {
                        showToast('Please enter at least 3 characters', 'warning');
                        return;
                    }

                    showToast('Searching location...', 'info');

                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=in`
                        );
                        const data = await response.json();

                        if (data && data.length > 0) {
                            const result = data[0];
                            this.mapLat = parseFloat(result.lat);
                            this.mapLng = parseFloat(result.lon);

                            this.map.setView([this.mapLat, this.mapLng], 16);
                            this.marker.setLatLng([this.mapLat, this.mapLng]);

                            searchInput.value = result.display_name;
                            showToast('Location found', 'success');
                        } else {
                            showToast('Location not found', 'error');
                        }
                    } catch (error) {
                        console.error('Map search error:', error);
                        showToast('Search failed', 'error');
                    }
                },

                async confirmMapLocation() {
                    showToast('Getting address...', 'info');

                    // Reverse geocode to get address
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.mapLat}&lon=${this.mapLng}`
                        );
                        const data = await response.json();

                        this.selectedAddress = data.display_name ||
                            `${this.mapLat.toFixed(6)}, ${this.mapLng.toFixed(6)}`;
                        this.selectedLat = this.mapLat;
                        this.selectedLng = this.mapLng;
                        this.search = this.selectedAddress;

                        // Close modal
                        this.closeMapModal();
                        showToast('Location confirmed!', 'success');

                    } catch (error) {
                        console.error('Reverse geocoding error:', error);
                        this.selectedAddress = `${this.mapLat.toFixed(6)}, ${this.mapLng.toFixed(6)}`;
                        this.selectedLat = this.mapLat;
                        this.selectedLng = this.mapLng;
                        this.search = this.selectedAddress;
                        this.closeMapModal();
                        showToast('Address saved with coordinates', 'success');
                    }
                },

                closeMapModal() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mapModal{{ $id }}'));
                    if (modal) {
                        modal.hide();
                    }

                    if (this.map) {
                        this.map.remove();
                        this.map = null;
                        this.mapInitialized = false;
                    }
                }
            }
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            // Remove existing toast
            document.querySelectorAll('.toast-notification').forEach(el => el.remove());

            const toast = document.createElement('div');
            toast.className = 'toast-notification';

            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
            const icon = type === 'success' ? '✓' : type === 'error' ? '⚠' : 'ℹ';

            toast.style.cssText = `
        position: fixed;
        top: 30px;
        right: 30px;
        padding: 16px 24px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        background: ${bgColor};
        color: white;
        animation: slideInRight 0.3s ease-out;
    `;

            toast.innerHTML = `
        <span style="font-size: 20px;">${icon}</span>
        <span style="flex: 1;">${message}</span>
    `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }
    </script>
@endpush
