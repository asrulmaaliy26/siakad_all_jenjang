<?php if (isset($component)) { $__componentOriginalb525200bfa976483b4eaa0b7685c6e24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                        <?php
                            $totalSelesai = 0;
                            $totalProses = 0;
                            $totalKeseluruhan = 0;
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $jenjangStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalSelesai += $stat['selesai'];
                            $totalProses += $stat['proses'];
                            $totalKeseluruhan += $stat['jumlah'];
                        ?>
                        <tr class="border-b dark:border-gray-700 <?php echo e($index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900'); ?>">
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($index + 1); ?>.</td>
                            <td class="p-2 border-r dark:border-gray-700">
                                <span class="px-2 py-0.5 text-xs font-bold text-white rounded <?php echo e($stat['jenjang'] == 'S1' ? 'bg-[#17a2b8]' : 'bg-[#28a745]'); ?>">
                                    <?php echo e($stat['jenjang']); ?>

                                </span>
                            </td>
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($stat['selesai']); ?></td>
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($stat['proses']); ?></td>
                            <td class="p-2 font-bold"><?php echo e($stat['jumlah']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-[#0d6efd] text-white font-bold text-sm">
                <table class="w-full text-center">
                    <tbody>
                        <tr>
                            <td class="p-3 text-center uppercase border-r border-[#0a58ca] w-[40%]">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[20%]"><?php echo e($totalSelesai); ?></td>
                            <td class="p-3 border-r border-[#0a58ca] w-[20%]"><?php echo e($totalProses); ?></td>
                            <td class="p-3 w-[20%]"><?php echo e($totalKeseluruhan); ?></td>
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
                        <?php
                            $totalSelesaiProdi = 0;
                            $totalProsesProdi = 0;
                            $totalKeseluruhanProdi = 0;
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prodiStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalSelesaiProdi += $stat['selesai'];
                            $totalProsesProdi += $stat['proses'];
                            $totalKeseluruhanProdi += $stat['jumlah'];
                        ?>
                        <tr x-on:click="$dispatch('filterByJurusan', { id: <?php echo e($stat['id']); ?> })" class="border-b dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-800 <?php echo e($index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900'); ?>">
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($index + 1); ?>.</td>
                            <td class="p-2 border-r dark:border-gray-700">
                                <span class="px-2 py-0.5 text-xs font-bold text-white rounded <?php echo e($stat['jenjang'] == 'S1' ? 'bg-[#17a2b8]' : 'bg-[#28a745]'); ?>">
                                    <?php echo e($stat['jenjang']); ?>

                                </span>
                            </td>
                            <td class="p-2 font-bold text-[#0d6efd] dark:text-blue-400 hover:underline border-r dark:border-gray-700"><?php echo e($stat['prodi']); ?></td>
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($stat['selesai']); ?></td>
                            <td class="p-2 border-r dark:border-gray-700"><?php echo e($stat['proses']); ?></td>
                            <td class="p-2 font-bold text-gray-800 dark:text-gray-200"><?php echo e($stat['jumlah']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-[#0d6efd] text-white font-bold text-sm">
                <table class="w-full text-center">
                    <tbody>
                        <tr>
                            <td class="p-3 text-center uppercase border-r border-[#0a58ca] w-[50%]">TOTAL KESELURUHAN</td>
                            <td class="p-3 border-r border-[#0a58ca] w-[16%]"><?php echo e($totalSelesaiProdi); ?></td>
                            <td class="p-3 border-r border-[#0a58ca] w-[17%]"><?php echo e($totalProsesProdi); ?></td>
                            <td class="p-3 w-[17%]"><?php echo e($totalKeseluruhanProdi); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $attributes = $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $component = $__componentOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php /**PATH C:\Users\losts\Desktop\web STAI AL Mannan\siakad\resources\views/filament/widgets/pendaftar-overview-stats.blade.php ENDPATH**/ ?>