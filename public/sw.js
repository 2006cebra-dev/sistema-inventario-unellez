// Instalación del Service Worker
self.addEventListener('install', (event) => {
    console.log('[OSWA-INV] Service Worker Instalado. App lista para móviles.');
});

// Estrategia de red (Dejamos que todo fluya normal con internet)
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            console.log('[OSWA-INV] Estás sin conexión.');
        })
    );
});