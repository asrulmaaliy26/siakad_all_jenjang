<div style="margin-bottom: 1rem; text-align: right;">
    <a href="{{ route('cetak.absensi.terisi', ['id_mata_pelajaran_kelas' => $id_mata_pelajaran_kelas]) }}" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: white; background-color: #2563eb; border-radius: 0.375rem; text-decoration: none; transition: background-color 0.2s;">
        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Cetak Data Absensi (Terisi)
    </a>
</div>

<div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left; color: #6b7280;">
        <thead style="background-color: #f9fafb; color: #374151; text-transform: uppercase; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em;">
            <tr>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb;">Nama Siswa</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb;">NIM</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 5rem;">Hadir</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 5rem;">Izin</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 5rem;">Sakit</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 5rem;">Alpa</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 6rem;">Total</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 6rem;">% Hadir</th>
                <th style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; text-align: center; width: 8rem;">Status</th>
            </tr>
        </thead>
        <tbody style="background-color: white;">
            @forelse($records as $row)
            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.15s ease-in-out;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                <td style="padding: 1rem 1.5rem; font-weight: 500; color: #111827; white-space: nowrap;">
                    {{ $row->nama_siswa }}
                </td>
                <td style="padding: 1rem 1.5rem; font-family: monospace; color: #4b5563;">
                    {{ $row->nim }}
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.75rem; font-weight: 700; color: #15803d; background-color: #dcfce7; border-radius: 9999px;">
                        {{ $row->total_hadir }}
                    </span>
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.75rem; font-weight: 700; color: #a16207; background-color: #fef9c3; border-radius: 9999px;">
                        {{ $row->total_izin }}
                    </span>
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.75rem; font-weight: 700; color: #b91c1c; background-color: #fee2e2; border-radius: 9999px;">
                        {{ $row->total_sakit }}
                    </span>
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.75rem; font-weight: 700; color: #374151; background-color: #f3f4f6; border-radius: 9999px;">
                        {{ $row->total_alpha }}
                    </span>
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center; font-weight: 700; color: #111827;">
                    {{ $row->total_absensi }}
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: {{ $row->persentase_hadir >= 80 ? '#16a34a' : '#dc2626' }};">
                            {{ number_format($row->persentase_hadir, 0) }}%
                        </div>
                        <div style="width: 3rem; height: 0.375rem; background-color: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; border-radius: 9999px; background-color: {{ $row->persentase_hadir >= 80 ? '#22c55e' : '#ef4444' }}; width: {{ min($row->persentase_hadir, 100) }}%;"></div>
                        </div>
                    </div>
                </td>
                <td style="padding: 1rem 1.5rem; text-align: center;">
                    @if($row->status_kelulusan_absensi == 'LULUS ABSEN')
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #15803d; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9999px;">
                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Lulus
                    </span>
                    @elseif($row->status_kelulusan_absensi == 'BELUM ABSEN')
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #4b5563; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 9999px;">
                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Belum Ada
                    </span>
                    @else
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #b91c1c; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 9999px;">
                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tidak Lulus
                    </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding: 2rem 1.5rem; text-align: center; color: #6b7280;">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <svg style="width: 3rem; height: 3rem; margin-bottom: 0.75rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p style="font-size: 1rem; font-weight: 500;">Belum ada data absensi untuk kelas ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>