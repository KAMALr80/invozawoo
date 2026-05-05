const CACHE_NAME = 'invoza-v16';
const POS_PATH = '/sales/create';

const ASSETS_TO_CACHE = [
    '/',
    POS_PATH,
    '/css/app.css',
    '/css/common.css',
    '/js/app.js',
    '/js/pos-data-service.js',
    '/manifest.json'
];

// Install Event
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('SW: Installing v16...');
            return Promise.allSettled(
                ASSETS_TO_CACHE.map(url => {
                    return fetch(url).then(res => {
                        if (res.ok) return cache.put(url, res);
                    }).catch(err => console.log('SW: Pre-cache failed for', url, err));
                })
            );
        })
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(keys.map(k => k !== CACHE_NAME && caches.delete(k)));
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    const path = url.pathname;
    
    // Broad POS detection
    const isPOSRequest = path.includes('/sales/create');

    // 1. NAVIGATION (Page Loads)
    if (event.request.mode === 'navigate' || (isPOSRequest && !path.includes('.'))) {
        event.respondWith(
            fetch(event.request).then((response) => {
                // Online: Update POS cache
                if (response.ok && isPOSRequest) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(POS_PATH, clone));
                }
                return response;
            }).catch(() => {
                // Offline
                if (isPOSRequest) {
                    console.log('SW: Serving POS Offline');
                    return caches.match(POS_PATH, { ignoreSearch: true }).then(match => {
                        return match || caches.match('/sales/create', { ignoreSearch: true });
                    });
                }
                
                // Block other navigation offline
                return caches.match(event.request);
            })
        );
        return;
    }

    // 2. STATIC ASSETS
    event.respondWith(
        caches.match(event.request).then((response) => {
            if (response) return response;
            
            return fetch(event.request).then((fetchRes) => {
                const isAsset = (
                    path.endsWith('.css') || 
                    path.endsWith('.js') || 
                    path.endsWith('.woff2') ||
                    path.endsWith('.png') ||
                    path.endsWith('.jpg') ||
                    url.hostname.includes('cdnjs') || 
                    url.hostname.includes('unpkg') ||
                    url.hostname.includes('datatables') ||
                    url.hostname.includes('jsdelivr')
                );

                if (fetchRes.ok && isAsset) {
                    const clone = fetchRes.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return fetchRes;
            }).catch(() => {
                // Return offline response for assets if fetch fails
                return new Response('Offline', { status: 503 });
            });
        })
    );
});
