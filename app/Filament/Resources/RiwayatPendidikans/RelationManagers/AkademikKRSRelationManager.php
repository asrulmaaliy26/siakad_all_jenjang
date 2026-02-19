<?php

namespace App\Filament\Resources\RiwayatPendidikans\RelationManagers;

use App\Filament\Resources\AkademikKrs\AkademikKrsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AkademikKRSRelationManager extends RelationManager
{
    protected static string $relationship = 'akademikKrs';


    protected static ?string $relatedResource = AkademikKrsResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
