// resources/js/delivery-tracker.js

class DeliveryTracker {
    constructor(shipmentId, agentId) {
        this.shipmentId = shipmentId;
        this.agentId = agentId;
        this.watchId = null;
        this.map = null;
        this.marker = null;
        this.polyline = null;
        this.path = [];
    }

    // Start tracking
    startTracking() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported');
            return;
        }

        this.watchId = navigator.geolocation.watchPosition(
            (position) => this.updatePosition(position),
            (error) => this.handleError(error),
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );

        this.initMap();
    }

    // Stop tracking
    stopTracking() {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }

    // Initialize map
    initMap() {
        this.map = L.map('agent-map').setView([28.6139, 77.2090], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(this.map);

        // Delivery icon
        var deliveryIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        this.marker = L.marker([28.6139, 77.2090], {icon: deliveryIcon}).addTo(this.map);
        this.polyline = L.polyline([], {color: 'red'}).addTo(this.map);
    }

    // Update position
    updatePosition(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const accuracy = position.coords.accuracy;

        // Update marker
        this.marker.setLatLng([lat, lng]);
        this.marker.bindPopup(`
            <b>Current Location</b><br>
            Accuracy: ${accuracy.toFixed(0)}m<br>
            Time: ${new Date().toLocaleTimeString()}
        `).openPopup();

        // Update path
        this.path.push([lat, lng]);
        this.polyline.setLatLngs(this.path);

        // Center map on current location
        this.map.setView([lat, lng]);

        // Send to server
        this.sendLocationToServer(lat, lng, accuracy);
    }

    // Send location to server
    sendLocationToServer(lat, lng, accuracy) {
        fetch(`/logistics/api/shipments/${this.shipmentId}/location`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                accuracy: accuracy,
                timestamp: new Date().toISOString()
            })
        })
        .then(response => response.json())
        .then(data => console.log('Location updated'))
        .catch(error => console.error('Error:', error));
    }

    // Handle error
    handleError(error) {
        let message = 'Location error: ';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                message += 'Permission denied';
                break;
            case error.POSITION_UNAVAILABLE:
                message += 'Position unavailable';
                break;
            case error.TIMEOUT:
                message += 'Timeout';
                break;
        }
        alert(message);
    }
}

// Initialize tracker
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('agent-map')) {
        const tracker = new DeliveryTracker(
            document.getElementById('shipment-id').value,
            document.getElementById('agent-id').value
        );

        document.getElementById('start-tracking').addEventListener('click', () => {
            tracker.startTracking();
        });

        document.getElementById('stop-tracking').addEventListener('click', () => {
            tracker.stopTracking();
        });
    }
});
