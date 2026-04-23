// Logistics Google Maps Integration
class LogisticsGoogleMaps {
    constructor() {
        this.map = null;
        this.markers = [];
        this.infoWindows = [];
        this.autocompleteInputs = [];
        this.directionsService = null;
        this.directionsRenderer = null;
    }

    async initMap(elementId, centerLat = 28.6139, centerLng = 77.2090, zoom = 12) {
        // Load Google Maps API (make sure API key is in your blade template)
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { Places } = await google.maps.importLibrary("places");

        this.map = new Map(document.getElementById(elementId), {
            center: { lat: centerLat, lng: centerLng },
            zoom: zoom,
            mapId: 'logistics-map',
            mapTypeControl: true,
            fullscreenControl: true,
            streetViewControl: false,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });

        // Initialize Directions service for route planning
        this.directionsService = new google.maps.DirectionsService();
        this.directionsRenderer = new google.maps.DirectionsRenderer({
            map: this.map,
            suppressMarkers: false,
            polylineOptions: {
                strokeColor: "#667eea",
                strokeWeight: 6,
                strokeOpacity: 0.8
            }
        });

        return this.map;
    }

    // Initialize address autocomplete
    initAutocomplete(inputElementId, onPlaceSelected) {
        const input = document.getElementById(inputElementId);
        if (!input) return;

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],
            componentRestrictions: { country: 'IN' } // Restrict to India, change as needed
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry && onPlaceSelected) {
                onPlaceSelected({
                    placeId: place.place_id,
                    address: place.formatted_address,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    name: place.name
                });
            }
        });

        this.autocompleteInputs.push(autocomplete);
        return autocomplete;
    }

    // Add a marker
    addMarker(lat, lng, options = {}) {
        const {
            title = '',
            icon = null,
            draggable = false,
            infoContent = '',
            color = 'red'
        } = options;

        // Custom icons for different types
        let iconUrl = this.getIconUrl(color);
        if (icon) iconUrl = icon;

        const marker = new google.maps.Marker({
            position: { lat, lng },
            map: this.map,
            title: title,
            draggable: draggable,
            icon: {
                url: iconUrl,
                scaledSize: new google.maps.Size(32, 32)
            }
        });

        if (infoContent) {
            const infoWindow = new google.maps.InfoWindow({
                content: infoContent
            });

            marker.addListener('click', () => {
                infoWindow.open(this.map, marker);
            });

            this.infoWindows.push(infoWindow);
        }

        this.markers.push(marker);
        return marker;
    }

    getIconUrl(color) {
        const icons = {
            'red': 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
            'blue': 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            'green': 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
            'yellow': 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
            'purple': 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png',
            'truck': 'https://maps.google.com/mapfiles/ms/icons/truck.png'
        };
        return icons[color] || icons.red;
    }

    // Add delivery agent marker
    addAgentMarker(agent) {
        return this.addMarker(agent.lat, agent.lng, {
            title: agent.name,
            color: 'blue',
            infoContent: `
                <div class="p-3">
                    <h5 class="font-bold">${agent.name}</h5>
                    <p>📞 ${agent.phone}</p>
                    <p>📦 Deliveries: ${agent.deliveries_today || 0}</p>
                    <p>⭐ Rating: ${agent.rating || 'N/A'}</p>
                </div>
            `
        });
    }

    // Add shipment marker
    addShipmentMarker(shipment) {
        const statusColors = {
            'pending': 'yellow',
            'picked': 'purple',
            'in_transit': 'blue',
            'out_for_delivery': 'orange',
            'delivered': 'green',
            'failed': 'red'
        };

        return this.addMarker(shipment.lat, shipment.lng, {
            title: `Shipment #${shipment.shipment_number}`,
            color: statusColors[shipment.status] || 'red',
            infoContent: `
                <div class="p-3">
                    <h5 class="font-bold">Shipment: ${shipment.shipment_number}</h5>
                    <p>📦 Status: ${shipment.status}</p>
                    <p>👤 Receiver: ${shipment.receiver_name}</p>
                    <p>📞 ${shipment.receiver_phone}</p>
                    <p>📍 ${shipment.city}</p>
                </div>
            `
        });
    }

    // Draw route for delivery
    async drawDeliveryRoute(waypoints) {
        if (waypoints.length < 2) return;

        const origin = waypoints[0];
        const destination = waypoints[waypoints.length - 1];
        const midPoints = waypoints.slice(1, -1).map(wp => ({
            location: new google.maps.LatLng(wp.lat, wp.lng),
            stopover: true
        }));

        const request = {
            origin: new google.maps.LatLng(origin.lat, origin.lng),
            destination: new google.maps.LatLng(destination.lat, destination.lng),
            waypoints: midPoints,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING
        };

        return new Promise((resolve, reject) => {
            this.directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    this.directionsRenderer.setDirections(result);

                    // Calculate totals
                    let totalDistance = 0;
                    let totalDuration = 0;
                    result.routes[0].legs.forEach(leg => {
                        totalDistance += leg.distance.value;
                        totalDuration += leg.duration.value;
                    });

                    resolve({
                        totalDistance: totalDistance / 1000, // km
                        totalDuration: totalDuration / 60, // minutes
                        waypointOrder: result.routes[0].waypoint_order
                    });
                } else {
                    reject(status);
                }
            });
        });
    }

    // Clear all markers
    clearMarkers() {
        this.markers.forEach(marker => marker.setMap(null));
        this.markers = [];
        this.infoWindows = [];
    }

    // Fit map to bounds of all markers
    fitToMarkers() {
        if (this.markers.length === 0) return;

        const bounds = new google.maps.LatLngBounds();
        this.markers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        this.map.fitBounds(bounds);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.logisticsMap = new LogisticsGoogleMaps();
});
