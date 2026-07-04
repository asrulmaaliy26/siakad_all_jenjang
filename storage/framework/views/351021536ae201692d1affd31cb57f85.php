
                    <div class="hidden md:flex flex-col items-end justify-center px-3 text-right">
                        <span class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                            <?php echo e(auth()->user()->name); ?>

                        </span>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 tracking-wider">
                            <?php echo e(auth()->user()->getRoleNames()->implode(", ")); ?>

                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has("impersonator_id")): ?>
                            <a href="<?php echo e(route("stop-impersonating")); ?>" class="text-xs text-red-600 hover:text-red-800 underline mt-1">
                                Kembali ke Superadmin
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php /**PATH C:\Users\losts\Desktop\web STAI AL Mannan\siakad\storage\framework\views/713c119c28178bf8df1692d91a2deb56.blade.php ENDPATH**/ ?>