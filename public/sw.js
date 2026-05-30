/**
 * StudAI Hire — Progressive Web App Service Worker
 * Caching strategy: Cache-first for static assets, Network-first for pages/API.
 */

const CACHE_NAME = 'studai-hire-v2';
const STATIC_CACHE = 'studai-static-v2';
const API_CACHE = 'studai-api-v2';

// Assets to pre-cache on install
const PRECACHE_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/icons/icon-192x192.svg',
    '/icons/icon-512x512.svg',
];

// Routes that should be served from cache-first strategy
const CACHE_FIRST_PATTERNS = [
    /\/icons\//,
    /\/images\//,
    /\/fonts\//,
    /\.(?:woff2?|ttf|eot|otf)$/,
    /\.(?:png|jpe?g|gif|webp|svg|ico)$/,
    /\.(?:css|js)$/,
];

// Routes that should NEVER be cached (sensitive/auth routes)
const NEVER_CACHE_PATTERNS = [
    /\/studai($|\/)/,   // Filament admin panel
    /\/logout/,
    /\/sanctum\//,
    /\/api\/user/,
    /\/_debugbar/,
];

// ── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[SW] Pre-cache failed (some assets may not exist yet):', err);
            });
        })
    );
});

// ── Activate ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME && key !== STATIC_CACHE && key !== API_CACHE)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin GET requests
    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Never cache admin/auth routes
    if (NEVER_CACHE_PATTERNS.some((pattern) => pattern.test(url.pathname))) {
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts)
    if (CACHE_FIRST_PATTERNS.some((pattern) => pattern.test(url.pathname))) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first for all HTML pages (with offline fallback)
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirstWithOfflineFallback(request));
        return;
    }

    // Network-first for everything else
    event.respondWith(networkFirst(request));
});

// ── Push Notifications ────────────────────────────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let data = {};
    try {
        data = event.data.json();
    } catch {
        data = { title: 'StudAI Hire', body: event.data.text() };
    }

    const options = {
        body: data.body || '',
        icon: '/icons/icon-192x192.svg',
        badge: '/icons/icon-72x72.svg',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' },
        actions: data.actions || [],
        tag: data.tag || 'studai-notification',
        renotify: true,
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'StudAI Hire', options)
    );
});

// ── Notification Click ────────────────────────────────────────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// ── Background Sync ───────────────────────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-offline-applications') {
        event.waitUntil(syncOfflineApplications());
    }
});

// ── Strategies ────────────────────────────────────────────────────────────────

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 503, statusText: 'Service Unavailable' });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response('', { status: 503 });
    }
}

async function networkFirstWithOfflineFallback(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Return offline page
        const offlinePage = await caches.match('/offline');
        return offlinePage || new Response(
            '<html><body><h1>You are offline</h1><p>Please check your connection.</p></body></html>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function syncOfflineApplications() {
    // Retrieve stored offline applications from IndexedDB and submit them
    try {
        const db = await openDB('studai-offline', 1);
        const applications = await db.getAll('pendingApplications');

        for (const application of applications) {
            try {
                const response = await fetch('/applications', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(application.data),
                });
                if (response.ok) {
                    await db.delete('pendingApplications', application.id);
                }
            } catch {
                // Will retry on next sync
            }
        }
    } catch {
        // IndexedDB not available or no pending applications
    }
}

// Minimal IndexedDB helper
function openDB(name, version) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(name, version);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('pendingApplications')) {
                db.createObjectStore('pendingApplications', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}
