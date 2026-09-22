<?php

namespace App\Filament\Resources\StaffConversations;

use App\Actions\NotifyStaff;
use App\Filament\Resources\StaffConversations\Pages\ManageStaffConversations;
use App\Models\StaffConversation;
use App\Models\StaffMessage;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StaffConversationResource extends Resource
{
    protected static ?string $model = StaffConversation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Staff messages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assignedGuard.full_name')->label('Guard')->searchable(),
                TextColumn::make('subject')->searchable(),
                TextColumn::make('last_message_at')->dateTime()->sortable(),
                TextColumn::make('messages_count')->counts('messages')->label('Messages'),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->recordActions([
                Action::make('reply')
                    ->form([
                        Textarea::make('body')->required()->rows(4),
                    ])
                    ->action(function (StaffConversation $record, array $data, NotifyStaff $notifyStaff): void {
                        /** @var User $user */
                        $user = Auth::user();

                        StaffMessage::query()->create([
                            'staff_conversation_id' => $record->id,
                            'sender_type' => User::class,
                            'sender_id' => $user->id,
                            'body' => $data['body'],
                        ]);

                        $record->update(['last_message_at' => now()]);

                        $notifyStaff(
                            $record->assignedGuard,
                            'message',
                            'New message from operations',
                            mb_strimwidth($data['body'], 0, 120, '…'),
                            ['conversation_id' => $record->id],
                        );

                        Notification::make()->title('Reply sent')->success()->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStaffConversations::route('/'),
        ];
    }
}
