<div wire:poll.5s="$refresh" class="relative">
    {{-- Main Container --}}
    <div class="flex flex-col h-[650px] bg-white dark:bg-[#0f172a] rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">

        {{-- Header: Glassmorphism effect --}}
        <div class="sticky top-0 z-10 px-6 py-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-green-500 to-green-600 flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-900 dark:text-white tracking-tight">Ruang Diskusi Pembimbing</h3>
                    <div class="flex items-center gap-1.5">
                        <span class="flex w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <p class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-widest">Sistem Akademik Terpadu</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isAdminMode && $dosenId)
                <button wire:click="$set('dosenId', null)" class="text-[11px] uppercase tracking-wider px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg transition-all font-bold border border-gray-200 dark:border-slate-700">
                    Ganti Dosen
                </button>
                @endif
                <button wire:click="$refresh" wire:loading.attr="disabled" class="p-2.5 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-all group relative">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-400 group-hover:text-primary-500 transition-colors" wire:loading.class="animate-spin text-primary-500" />
                </button>
            </div>
        </div>

        @if($isAdminMode && empty($dosenId))
        {{-- Premium Admin Selection --}}
        <div class="flex-1 p-12 flex flex-col items-center justify-center bg-gray-50/50 dark:bg-transparent relative overflow-hidden">
            {{-- Decorative Background Elements --}}
            <div class="absolute top-0 left-0 w-full h-full opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500 rounded-full blur-[100px]"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500 rounded-full blur-[100px]"></div>
            </div>

            <div class="w-20 h-20 rounded-3xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center mb-8 rotate-3 shadow-inner">
                <x-heroicon-o-user-circle class="w-10 h-10 text-primary-600 dark:text-primary-400" />
            </div>

            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 text-center">Pilih Ruang Kendali</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-10 text-center max-w-sm leading-relaxed">
                Sebagai Administrator, Anda dapat memantau dan membantu proses bimbingan akademik dengan memilih salah satu Dosen Wali di bawah ini.
            </p>

            <div class="w-full max-w-sm group">
                <div class="relative">
                    <select wire:model.live="dosenId" class="w-full pl-5 pr-12 py-3.5 bg-white dark:bg-slate-800 border-gray-200 dark:border-slate-700 dark:text-white rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer font-medium">
                        <option value="">-- Pilih Dosen Wali --</option>
                        @foreach(\App\Models\DosenData::orderBy('nama', 'asc')->get() as $dosen)
                        <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <x-heroicon-m-chevron-down class="w-5 h-5" />
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Messages Area with Modern Scrollbar --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-[#f8fafc] dark:bg-transparent" style="scroll-behavior: smooth;">
            @forelse($messages ?? [] as $msg)
            @php
            /** @var \App\Models\User|null $msgUser */
            $msgUser = $msg->user;
            $isSuperAdmin = $msgUser ? $msgUser->hasRole('super_admin') : false;
            $isDosenReal = $msgUser ? $msgUser->hasRole('pengajar') : false;
            $isDosenView = $isDosenReal || $isSuperAdmin;

            $alignment = $isDosenView ? 'justify-start' : 'justify-end';
            $bubbleStyle = $isDosenView
            ? 'bg-white dark:bg-slate-800 text-gray-800 dark:text-white rounded-2xl rounded-tl-none border border-gray-100 dark:border-slate-700 shadow-sm'
            : 'bg-primary-600 text-white rounded-2xl rounded-tr-none shadow-lg shadow-primary-600/20';

            $senderName = $msgUser ? $msgUser->name : 'Unknown';
            if (!$isDosenView && $msgUser?->siswaData) {
            $senderName = $msgUser->siswaData->nama;
            } elseif ($isSuperAdmin) {
            $senderName = $msgUser->name . ' (Admin)';
            }
            @endphp

            <div class="flex {{ $alignment }} items-end gap-2 group animate-in fade-in slide-in-from-bottom-2 duration-300">
                @if($isDosenView)
                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 mb-1 border border-primary-200 dark:border-primary-800">
                    <span class="text-[10px] font-black text-primary-700 dark:text-primary-400">{{ substr($senderName, 0, 1) }}</span>
                </div>
                @endif

                <div class="max-w-[75%] flex flex-col {{ $isDosenView ? 'items-start' : 'items-end' }}">
                    {{-- Sender & Time Label --}}
                    <div class="flex items-center gap-3 mb-1.5 px-1">
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $isDosenView ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                            {{ $isDosenView ? ($isSuperAdmin ? $senderName : 'PEMBIMBING') : $senderName }}
                        </span>
                        <span class="text-[9px] font-bold text-gray-300 dark:text-slate-600">{{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}</span>
                    </div>

                    {{-- Chat Bubble --}}
                    <div class="px-5 py-3 {{ $bubbleStyle }} transition-transform hover:scale-[1.01]">
                        <p class="text-[13px] leading-relaxed font-medium whitespace-pre-wrap">{{ $msg->message }}</p>
                    </div>
                </div>

                @if(!$isDosenView)
                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 mb-1 border border-gray-300 dark:border-slate-600">
                    <span class="text-[10px] font-black text-gray-600 dark:text-slate-400">{{ substr($senderName, 0, 1) }}</span>
                </div>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-center p-12 translate-y-[-10%]">
                <div class="w-24 h-24 rounded-full bg-white dark:bg-slate-800 shadow-xl flex items-center justify-center mb-6 border border-gray-50 dark:border-slate-700">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 text-gray-200 dark:text-slate-600" />
                </div>
                <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2 italic">Hening Sekejap...</h4>
                <p class="text-sm text-gray-500 dark:text-slate-500 max-w-[240px] leading-relaxed">Belum ada percakapan terjalin. Jadilah yang pertama memberikan sapaan hangat!</p>
            </div>
            @endforelse
            <div id="anchor" class="h-1"></div>
        </div>

        {{-- Input Area: Floating style --}}
        <div class="px-6 py-5 bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                <div class="flex-1 relative group">
                    <textarea
                        wire:model.defer="message"
                        placeholder="Ketik pesan Anda di sini..."
                        rows="1"
                        class="w-full bg-gray-50 dark:bg-slate-800/50 border-gray-200 dark:border-slate-700 rounded-2xl px-5 py-3 text-sm focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white resize-none max-h-32"
                        {{ empty($dosenId) ? 'disabled' : '' }}
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-primary-600 hover:bg-primary-500 text-white w-12 h-12 rounded-2xl shadow-xl shadow-primary-500/30 flex items-center justify-center transition-all hover:scale-105 active:scale-95 disabled:opacity-50 flex-shrink-0"
                    {{ empty($dosenId) ? 'disabled' : '' }}>
                    <x-heroicon-s-paper-airplane class="w-6 h-6 rotate-45 -translate-y-0.5 -translate-x-0.5" wire:loading.remove />
                    <div wire:loading class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </form>
            <p class="mt-2 text-[10px] text-gray-400 dark:text-slate-600 text-center font-bold tracking-widest uppercase italic">Tekan Enter untuk Mengirim</p>
        </div>
        @endif

        {{-- Custom Styles --}}
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(203, 213, 225, 0.4);
                border-radius: 20px;
                border: 2px solid transparent;
                background-clip: content-box;
            }

            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(51, 65, 85, 0.4);
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background-color: #94a3b8;
                background-clip: padding-box;
                border: 0;
            }
        </style>

        {{-- Scroll Logic --}}
        <script>
            document.addEventListener('livewire:initialized', () => {
                const chatContainer = document.getElementById('chat-messages');
                const scrollToBottom = () => {
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                };

                // Initial scroll
                setTimeout(scrollToBottom, 100);

                // Scroll on new message
                Livewire.on('message-sent', () => {
                    setTimeout(scrollToBottom, 50);
                });

                // Periodic scroll check (optional but helps with polling)
                let lastHeight = chatContainer?.scrollHeight;
                setInterval(() => {
                    if (chatContainer && chatContainer.scrollHeight !== lastHeight) {
                        const isAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 100;
                        if (isAtBottom) scrollToBottom();
                        lastHeight = chatContainer.scrollHeight;
                    }
                }, 1000);
            });
        </script>
    </div>
</div>