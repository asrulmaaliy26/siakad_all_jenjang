
                    <meta property="og:image" content="<?php echo e(asset("logokampus.jpg")); ?>" />
                    <link rel="manifest" href="/manifest.json">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="default">
                    <meta name="apple-mobile-web-app-title" content="SIAKAD">
                    <link rel="apple-touch-icon" href="/logokampus.jpg">
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", function() {
                                navigator.serviceWorker.register("/sw.js").then(function(registration) {
                                    console.log("ServiceWorker registration successful with scope: ", registration.scope);
                                }, function(err) {
                                    console.log("ServiceWorker registration failed: ", err);
                                });
                            });
                        }

                        <?php if(auth()->guard()->check()): ?>
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                tipe: "INFO_SISWA",
                                nim: "<?php echo e(auth()->user()->nim ?? auth()->user()->username ?? auth()->user()->email ?? "-"); ?>", 
                                nama: "<?php echo e(auth()->user()->name); ?>"
                            }, "*");
                        }
                        <?php endif; ?>
                    </script>
                <?php /**PATH C:\Users\losts\Desktop\web STAI AL Mannan\siakad\storage\framework\views/bbb762322785dc1c0de1058a51991a0d.blade.php ENDPATH**/ ?>