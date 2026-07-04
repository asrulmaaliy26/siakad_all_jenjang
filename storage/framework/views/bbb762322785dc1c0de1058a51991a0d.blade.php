
                    <meta property="og:image" content="{{ asset("logokampus.jpg") }}" />
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

                        @auth
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                tipe: "INFO_SISWA",
                                nim: "{{ auth()->user()->nim ?? auth()->user()->username ?? auth()->user()->email ?? "-" }}", 
                                nama: "{{ auth()->user()->name }}"
                            }, "*");
                        }
                        @endauth
                    </script>
                