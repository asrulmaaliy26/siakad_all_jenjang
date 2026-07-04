<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="font-family: inherit;">
        
        <!-- Rekap Mahasiswa Baru -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800 flex flex-col h-full overflow-hidden">
            <div class="bg-[#0d6efd] text-white p-3 text-sm font-semibold">
                Rekap Mahasiswa Baru
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-center">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold border-b dark:border-gray-700">
                        <tr>
                            <th class="p-2 py-3 border-r dark:border-gray-700">No</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Jenjang</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Selesai (Lulus/Tolak)</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Proses</th>
                            <th class="p-2 py-3">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        @php
                            $totalSelesai = 0;
                            $totalProses = 0;
                            $totalKeseluruhan = 0;
                        @endphp
                        @foreach($jenjangStats as $index => $stat)
                        @php
                            $totalSelesai += $stat['selesai'];
                            $totalProses += $stat['proses'];
                            $totalKeseluruhan += $stat['jumlah'];
                        @endphp
                        <tr class="border-b dark:border-gray-700 {{ $index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900' }}">
                            <td class="p-2 border-r dark:border-gray-700">{{ $index + 1 }}.</td>
                            <td class="p-2 border-r dark:border-gray-700">
                                <span class="px-2 py-0.5 text-xs font-bold text-white rounded {{ $stat['jenjang'] == 'S1' ? 'bg-[#17a2b8]' : 'bg-[#28a745]' }}">
                                    {{ $stat['jenjang'] }}
                                </span>
                            </td>
                            <td class="p-2 border-r dark:border-gray-700">{{ $stat['selesai'] }}</td>
                            <td class="p-2 border-r dark:border-gray-700">{{ $stat['proses'] }}</td>
                            <td class="p-2 font-bold">{{ $stat['jumlah'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-[#0d6efd] text-white font-bold text-sm">
                <table class="w-full text-center">
                    <tbody>
                        <tr>
                            <td class="p-3 text-center uppercase border-r border-[#0a58ca] w-[40%]">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[20%]">{{ $totalSelesai }}</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[20%]">{{ $totalProses }}</td>
                            <td class="p-3 w-[20%]">{{ $totalKeseluruhan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Berdasarkan Program Studi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800 flex flex-col h-full overflow-hidden">
            <div class="bg-[#0d6efd] text-white p-3 text-sm font-semibold">
                Berdasarkan Program Studi
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-center">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold border-b dark:border-gray-700">
                        <tr>
                            <th class="p-2 py-3 border-r dark:border-gray-700">No</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Jenjang</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Prodi</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Selesai (Lulus/Tolak)</th>
                            <th class="p-2 py-3 border-r dark:border-gray-700">Proses</th>
                            <th class="p-2 py-3">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        @php
                            $totalSelesaiProdi = 0;
                            $totalProsesProdi = 0;
                            $totalKeseluruhanProdi = 0;
                        @endphp
                        @foreach($prodiStats as $index => $stat)
                        @php
                            $totalSelesaiProdi += $stat['selesai'];
                            $totalProsesProdi += $stat['proses'];
                            $totalKeseluruhanProdi += $stat['jumlah'];
                        @endphp
                        <tr x-on:click="$dispatch('filterByJurusan', { id: {{ $stat['id'] }} })" class="border-b dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-800 {{ $index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900' }}">
                            <td class="p-2 border-r dark:border-gray-700">{{ $index + 1 }}.</td>
                            <td class="p-2 border-r dark:border-gray-700">
                                <span class="px-2 py-0.5 text-xs font-bold text-white rounded {{ $stat['jenjang'] == 'S1' ? 'bg-[#17a2b8]' : 'bg-[#28a745]' }}">
                                    {{ $stat['jenjang'] }}
                                </span>
                            </td>
                            <td class="p-2 font-bold text-[#0d6efd] dark:text-blue-400 hover:underline border-r dark:border-gray-700">{{ $stat['prodi'] }}</td>
                            <td class="p-2 border-r dark:border-gray-700">{{ $stat['selesai'] }}</td>
                            <td class="p-2 border-r dark:border-gray-700">{{ $stat['proses'] }}</td>
                            <td class="p-2 font-bold text-gray-800 dark:text-gray-200">{{ $stat['jumlah'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-[#0d6efd] text-white font-bold text-sm">
                <table class="w-full text-center">
                    <tbody>
                        <tr>
                            <td class="p-3 text-center uppercase border-r border-[#0a58ca] w-[50%]">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[16%]">{{ $totalSelesaiProdi }}</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[17%]">{{ $totalProsesProdi }}</td>
                            <td class="p-3 w-[17%]">{{ $totalKeseluruhanProdi }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
