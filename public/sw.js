const CACHE_NAME = 'invoza-v1';
const ASSETS_TO_CACHE = [
    '/dashboard',
    '/sales',
    '/sales/create',
    '/css/app.css',
    '/js/app.js',
    '/js/pos-data-service.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('SW: Caching App Shell');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('SW: Clearing Old Cache');
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    // Only cache GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Navigation Requests: Network First, then Cache, then Fallback to Create Page
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(event.request)
                        .then(response => {
                            if (response) return response;
                            
                            // If navigation to any /sales URL fails, fallback to either index or create
                            if (url.pathname.startsWith('/sales')) {
                                return caches.match('/sales');
                            }
                            return caches.match('/dashboard');
                        })
                        .then(response => response || Response.error());
                })
        );
        return;
    }

    // Static Assets & CDN: Cache First, then Network
    event.respondWith(
        caches.match(event.request).then((response) => {
            if (response) return response;
            
            return fetch(event.request).then((fetchResponse) => {
                // Cache static assets from our own domain or common CDNs
                if (fetchResponse.ok && (
                    url.origin === self.location.origin || 
                    url.hostname.includes('cdnjs') || 
                    url.hostname.includes('unpkg') ||
                    url.hostname.includes('fonts.googleapis.com')
                )) {
                    const responseClone = fetchResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return fetchResponse;
            });
        }).catch(() => {
            // Silence errors for non-navigation requests
            return new Response('Offline content unavailable', { status: 503, statusText: 'Service Unavailable' });
        })
    );
});

// Background Sync
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-invoices') {
        console.log('SW: Background Sync Triggered');
        // This will be handled by the main thread usually via postMessage 
        // or we can attempt to fetch here if we use IndexedDB
    }
});
