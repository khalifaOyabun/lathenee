<?php
require_once "ServiceWorker.php";

$files = array();// Files to cache
$sw = new ServiceWorker($files);

header('Content-Type: application/javascript');
?>

var staticCacheName = "<?php=$sw->getCacheName();?>-<?php=$sw->getVersion();?>";

self.addEventListener("install", function(event) {
    event.waitUntil(
        caches.open(staticCacheName).then(function(cache) {
            return cache.addAll([
                "/",
                <?php foreach ($sw->getFiles() as $file):?>
                "/<?php=$file;?>",
                <?php endforeach;?>
            ]);
        })
    );
});

self.addEventListener("activate", function(event) {
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
        return Promise.all(
            cacheNames.filter(function(cacheName) {
                return cacheName.startsWith("<?php=$sw->getCacheName();?>-") &&
                cacheName != staticCacheName;
            }).map(function(cacheName) {
                return caches.delete(cacheName);
            })
        );
    })
  );
});

self.addEventListener("fetch", function(event) {
    event.respondWith(
    caches.match(event.request).then(function(response) {
        return response || fetch(event.request);
    })
  );
});

self.addEventListener("message", function(event) {
    if (event.data.action === "skipWaiting") {
        self.skipWaiting();
    }
});