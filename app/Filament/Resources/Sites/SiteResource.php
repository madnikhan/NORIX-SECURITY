<?php

namespace App\Filament\Resources\Sites;

use App\Actions\GeocodeAddress;
use App\Filament\Resources\Sites\Pages\ManageSites;
use App\Models\Site;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Throwable;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->required()
                    ->helperText('Geocoded automatically for staff clock-in geofencing.'),
                TextInput::make('latitude')
                    ->numeric()
                    ->step(0.0000001),
                TextInput::make('longitude')
                    ->numeric()
                    ->step(0.0000001),
                TextInput::make('geofence_radius_meters')
                    ->numeric()
                    ->default(150)
                    ->required()
                    ->suffix('m'),
                Textarea::make('requirements')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('latitude')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('longitude')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('geofence_radius_meters')
                    ->label('Fence (m)')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
                Action::make('regeocode')
                    ->label('Re-geocode')
                    ->action(function (Site $record, GeocodeAddress $geocodeAddress): void {
                        try {
                            $coords = $geocodeAddress($record->address);
                        } catch (Throwable $e) {
                            Notification::make()->title('Geocode failed')->body($e->getMessage())->danger()->send();

                            return;
                        }

                        if ($coords === null) {
                            Notification::make()->title('No results for this address')->warning()->send();

                            return;
                        }

                        $record->update([
                            'latitude' => $coords['lat'],
                            'longitude' => $coords['lng'],
                            'geocoded_at' => now(),
                        ]);

                        Notification::make()->title('Site coordinates updated')->success()->send();
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
            'index' => ManageSites::route('/'),
        ];
    }
}
