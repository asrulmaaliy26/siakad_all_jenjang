<div class="flex flex-col items-center justify-center space-y-4 p-4 text-center">
    @php
    $url = url('/pendaftaran?ref=' . $record->kode);
    @endphp

    <div class="flex flex-col items-center p-4 bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- We use an img tag pointing to an external QR service or a package like simple-qrcode -->
        <img id="qr-image-{{ $record->id }}" src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($url) }}&margin=10" alt="QR Code" class="w-48 h-48 mb-4 border border-gray-100 rounded shadow-sm" crossorigin="anonymous" />

        <button type="button"
            onclick="downloadQR('{{ $record->id }}', '{{ $record->kode }}', this)"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download QR
        </button>
    </div>

    <div class="w-full max-w-md">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Link Pendaftaran</label>
        <div class="flex mt-1 relative">
            <input type="text" readonly value="{{ $url }}"
                class="block w-full rounded-l-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                id="referral-link-{{ $record->id }}">
            <button type="button"
                onclick="navigator.clipboard.writeText(document.getElementById('referral-link-{{ $record->id }}').value); alert('Link disalin!')"
                class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M15.5 2h-9A1.5 1.5 0 005 3.5v9A1.5 1.5 0 006.5 14h9a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0015.5 2zM6.5 3.5v9h9v-9h-9z" clip-rule="evenodd" />
                    <path d="M3.5 6A1.5 1.5 0 002 7.5v9A1.5 1.5 0 003.5 18h9a1.5 1.5 0 001.5-1.5V15h-1v1.5a.5.5 0 01-.5.5h-9a.5.5 0 01-.5-.5v-9a.5.5 0 01.5-.5H5V6H3.5z" />
                </svg>
            </button>
        </div>
    </div>

    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Kode: <span class="font-bold text-primary-600">{{ $record->kode }}</span>
    </div>

    <script>
        if (typeof downloadQR === 'undefined') {
            function downloadQR(id, kode, buttonElement) {
                try {
                    const originalText = buttonElement.innerHTML;
                    buttonElement.innerHTML = 'Proses...';
                    buttonElement.disabled = true;

                    const imgUrl = document.getElementById('qr-image-' + id).src;

                    // Try fetching first (works if CORS is configured on the API)
                    fetch(imgUrl)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response not ok: ' + response.status);
                            return response.blob();
                        })
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = 'QR-Referal-' + kode + '.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(err => {
                            console.error('Download error:', err);
                            alert('Pemberitahuan: Sistem keamanan browser memblokir unduhan langsung. Gambar akan dibuka di tab baru. (' + err.message + ')');

                            // Fallback: create anchor and open in new tab
                            const a = document.createElement('a');
                            a.href = imgUrl;
                            a.target = '_blank';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        })
                        .finally(() => {
                            buttonElement.innerHTML = originalText;
                            buttonElement.disabled = false;
                        });
                } catch (error) {
                    alert('Terjadi kesalahan script: ' + error.message);
                    console.error(error);
                }
            }
        }
    </script>
</div>