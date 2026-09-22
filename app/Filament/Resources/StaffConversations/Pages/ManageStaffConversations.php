<?php

namespace App\Filament\Resources\StaffConversations\Pages;

use App\Filament\Resources\StaffConversations\StaffConversationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStaffConversations extends ManageRecords
{
    protected static string $resource = StaffConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
