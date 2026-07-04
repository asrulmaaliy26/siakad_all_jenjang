
                    <div class="hidden md:flex flex-col items-end justify-center px-3 text-right">
                        <span class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 tracking-wider">
                            {{ auth()->user()->getRoleNames()->implode(", ") }}
                        </span>
                        @if(session()->has("impersonator_id"))
                            <a href="{{ route("stop-impersonating") }}" class="text-xs text-red-600 hover:text-red-800 underline mt-1">
                                Kembali ke Superadmin
                            </a>
                        @endif
                    </div>
                