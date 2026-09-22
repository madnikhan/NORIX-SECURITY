<?php

namespace App\Filament\Resources\Guards;

use App\Actions\InviteStaffGuard;
use App\Filament\Resources\Guards\Pages\ManageGuards;
use App\Models\Guard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GuardResource extends Resource
{
    protected static ?string $model = Guard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('sia_licence_number')
                    ->required(),
                DatePicker::make('sia_expiry')
                    ->required(),
                TextInput::make('default_hourly_rate')
                    ->numeric()
                    ->prefix('£'),
                Textarea::make('availability_notes')
                    ->columnSpanFull(),
                Select::make('application_id')
                    ->relationship('application', 'id'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('must_set_password')
                    ->label('Must set password'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('sia_licence_number')
                    ->searchable(),
                TextColumn::make('sia_expiry')
                    ->date()
                    ->sortable(),
                TextColumn::make('default_hourly_rate')
                    ->money('GBP')
                    ->sortable(),
                TextColumn::make('application.id')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('invite')
                    ->label('Send invite')
                    ->action(function (Guard $record, InviteStaffGuard $inviteStaffGuard): void {
                        $inviteStaffGuard($record);
                        Notification::make()->title('Staff invite emailed')->success()->send();
                    }),
                Action::make('forcePasswordReset')
                    ->label('Force password reset')
                    ->requiresConfirmation()
                    ->action(function (Guard $record, InviteStaffGuard $inviteStaffGuard): void {
                        $record->forceFill([
                            'password' => null,
                            'must_set_password' => true,
                        ])->save();
                        $inviteStaffGuard($record);
                        Notification::make()->title('Password cleared and invite sent')->success()->send();
                    }),
                EditAction::make(),
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
            'index' => ManageGuards::route('/'),
        ];
    }
}
