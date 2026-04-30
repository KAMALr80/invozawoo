const CACHE_NAME = 'invoza-pos-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/sales/create',
    '/sales',
    '/js/pos-data-service.js',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js',
    'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css',
    'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css',
    'https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css'
];

// Install Event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('SW: Pre-caching POS Shell');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Strategy: Network First, falling back to cache for the POS page
    if (url.pathname === '/sales/create' || url.pathname.startsWith('/sales/')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    const resClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, resClone);
                    });
                    return response;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    // Strategy: Cache First for assets
    event.respondWith(
        caches.match(event.request).then(cachedResponse => {
            if (cachedResponse) return cachedResponse;

            return fetch(event.request).then(response => {
                // Don't cache if not a successful response
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                const resClone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, resClone);
                });
                return response;
            });
        })
    );
});
