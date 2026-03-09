<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\KrsChat;
use App\Models\KrsChatSeen;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use UnitEnum;
use BackedEnum;

class DiskusiPembimbing extends Page
{
    use HasPageShield;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Diskusi Pembimbing';
    protected static ?string $title = 'Diskusi Pembimbing';
    protected static string | \UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 24;

    protected string $view = 'filament.pages.diskusi-pembimbing';

    public $dosenId = null;

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();
        $dosenId = $user?->getDosenId();

        if ($dosenId) {
            $this->dosenId = $dosenId;
        } elseif ($user?->isMurid()) {
            // Ambil dosen wali dari riwayat pendidikan terbaru
            $this->dosenId = $user->siswaData?->riwayatPendidikan()
                ->whereNotNull('id_wali_dosen')
                ->orderBy('id', 'desc')
                ->first()?->id_wali_dosen;
        } elseif ($user?->hasRole('super_admin') || $user?->hasRole('admin') || $user?->hasRole('kaprodi')) {
            $this->dosenId = 'admin_select';
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $user && ($user->isMurid() || $user->isPengajar() || $user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('kaprodi'));
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) return null;

        $dosenId = $user->getDosenId();
        if (!$dosenId && $user->isMurid()) {
            $dosenId = $user->siswaData?->riwayatPendidikan()
                ->whereNotNull('id_wali_dosen')
                ->orderBy('id', 'desc')
                ->first()?->id_wali_dosen;
        }

        if (!$dosenId) return null;

        $lastSeen = KrsChatSeen::where('user_id', $user->id)
            ->where('id_dosen', $dosenId)
            ->value('last_seen_at') ?? '1970-01-01 00:00:00';

        $count = KrsChat::where('id_dosen', $dosenId)
            ->where('user_id', '!=', $user->id)
            ->where('created_at', '>', $lastSeen)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
