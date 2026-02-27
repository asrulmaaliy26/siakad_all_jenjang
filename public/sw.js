self.addEventListener('install', function(event) {
    console.log('[Service Worker] Install');
});

self.addEventListener('fetch', function(event) {
    // Required to be a PWA, even if empty
});
