<div class="flex flex-col w-full max-w-7xl mx-auto gap-4 p-4">
    <!-- Container PDF/LJK - HANYA TAMPIL JIKA ADA FILE -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($files)): ?>
        <div class="flex flex-col gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $url = asset('storage/' . $file);
                    $extension = pathinfo($url, PATHINFO_EXTENSION);
                ?>
                <div class="flex flex-col gap-2">
                    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative"
                        style="height: 600px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                        <div class="w-full h-full flex items-center justify-center overflow-auto p-4 bg-gray-50">
                            <img src="<?php echo e($url); ?>" alt="Preview" class="max-w-full max-h-full w-auto h-auto object-contain rounded shadow-sm">
                        </div>
                        <?php elseif(strtolower($extension) === 'pdf'): ?>
                        <iframe
                            src="<?php echo e($url); ?>#toolbar=0&navpanes=0&scrollbar=1&view=FitH"
                            class="w-full h-full"
                            style="border: none; width: 100%; height: 100%;"
                            frameborder="0"
                            allowfullscreen></iframe>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full p-8 text-center bg-gray-50">
                            <p class="text-gray-500 mb-3">File tidak dapat dipreview.</p>
                            <a href="<?php echo e($url); ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500 transition-colors text-sm">
                                Download File
                            </a>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Metadata LJK -->
                    <div class="flex-shrink-0 flex justify-between items-center text-xs text-gray-500 pt-2 px-2">
                        <div class="flex items-center gap-3">
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-medium tracking-wide"><?php echo e(strtoupper($extension ?? '-')); ?></span>
                            <a href="<?php echo e($url); ?>" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white hover:bg-primary-700 rounded-md transition-colors font-medium shadow-sm hover:shadow">
                                Download / Lihat Full
                            </a>
                        </div>
                        <span class="text-gray-400 truncate max-w-xs" title="<?php echo e(basename($file)); ?>"><?php echo e(basename($file)); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Section Catatan - TETAP TAMPIL, MENJADI FOKUS UTAMA JIKA TIDAK ADA FILE -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notes): ?>
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-2">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="font-medium text-gray-900 text-sm">
                Catatan / Jawaban
                <span class="text-xs text-gray-500 ml-2">(<?php echo e(str_word_count(strip_tags($notes))); ?> kata)</span>
            </h3>
        </div>
        <div class="p-4 max-h-[400px] overflow-y-auto bg-white">
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                <?php echo $notes; ?>

            </div>
        </div>
    </div>
    <?php elseif(empty($files)): ?>
    <!-- Hanya tampil jika TIDAK ADA FILE DAN TIDAK ADA CATATAN -->
    <div class="w-full bg-gray-50 rounded-xl border border-gray-200 border-dashed p-8 text-center mt-2">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <p class="text-sm">Tidak ada file LJK dan tidak ada catatan.</p>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\losts\Desktop\web STAI AL Mannan\siakad\resources\views/filament/resources/mata-pelajaran-kelas/ljk-view.blade.php ENDPATH**/ ?>