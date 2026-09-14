<?php

namespace App\Filament\Resources\Guards\Pages;

use App\Filament\Resources\Guards\GuardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGuards extends ManageRecords
{
    protected static string $resource = GuardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
