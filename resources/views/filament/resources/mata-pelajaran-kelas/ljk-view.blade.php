<div class="flex flex-col w-full max-w-7xl mx-auto gap-4 p-4">
    <!-- Container PDF/LJK - HANYA TAMPIL JIKA ADA FILE -->
    @if($url)
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative"
        style="height: 600px;">
        @php
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        @endphp

        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
        <div class="w-full h-full flex items-center justify-center overflow-auto p-4 bg-gray-50">
            <img src="{{ $url }}" alt="Preview" class="max-w-full max-h-full w-auto h-auto object-contain rounded shadow-sm">
        </div>
        @elseif(strtolower($extension) === 'pdf')
        <iframe
            src="{{ $url }}#toolbar=0&navpanes=0&scrollbar=1&view=FitH"
            class="w-full h-full"
            style="border: none; width: 100%; height: 100%;"
            frameborder="0"
            allowfullscreen></iframe>
        @else
        <div class="flex flex-col items-center justify-center h-full p-8 text-center bg-gray-50">
            <p class="text-gray-500 mb-3">File tidak dapat dipreview.</p>
            <a href="{{ $url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500 transition-colors text-sm">
                Download File
            </a>
        </div>
        @endif
    </div>

    <!-- Metadata LJK - HANYA TAMPIL JIKA ADA FILE -->
    <div class="flex-shrink-0 flex justify-between items-center text-xs text-gray-500 pt-3 border-t border-gray-100 mt-2">
        <div class="flex items-center gap-3">
            <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-medium tracking-wide">{{ strtoupper($extension ?? '-') }}</span>

            <a href="{{ $url }}" target="_blank"
                class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white hover:bg-primary-700 rounded-md transition-colors font-medium shadow-sm hover:shadow">
                Download / Lihat Full
            </a>
        </div>
        <span class="text-gray-400">{{ now()->format('d M Y H:i') }}</span>
    </div>
    @endif

    <!-- Section Catatan - TETAP TAMPIL, MENJADI FOKUS UTAMA JIKA TIDAK ADA FILE -->
    @if($notes)
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="font-medium text-gray-900 text-sm">
                Catatan / Jawaban
                <span class="text-xs text-gray-500 ml-2">({{ str_word_count(strip_tags($notes)) }} kata)</span>
            </h3>
        </div>
        <div class="p-4 max-h-[400px] overflow-y-auto bg-white">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! $notes !!}
            </div>
        </div>
    </div>
    @elseif(!$url)
    <!-- Hanya tampil jika TIDAK ADA FILE DAN TIDAK ADA CATATAN -->
    <div class="w-full bg-gray-50 rounded-xl border border-gray-200 border-dashed p-8 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <p class="text-sm">Tidak ada file LJK dan tidak ada catatan.</p>
        </div>
    </div>
    @endif
</div>