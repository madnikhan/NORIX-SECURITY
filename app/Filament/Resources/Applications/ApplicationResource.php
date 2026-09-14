<?php

namespace App\Filament\Resources\Applications;

use App\Actions\HireApplication;
use App\Filament\Resources\Applications\Pages\ManageApplications;
use App\Models\Application;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'Recruitment';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('candidate_id')->relationship('candidate', 'name')->required(),
            Select::make('job_posting_id')->relationship('jobPosting', 'title')->required(),
            TextInput::make('reference')->required(),
            TextInput::make('sia_licence_number'),
            DatePicker::make('sia_expiry'),
            TextInput::make('right_to_work_share_code'),
            Select::make('status')->options([
                'submitted' => 'Submitted',
                'under_review' => 'Under review',
                'docs_verified' => 'Documents verified',
                'rejected' => 'Rejected',
                'hired' => 'Hired',
            ])
                ->disableOptionWhen(fn (string $value): bool => $value === 'hired')
                ->helperText('Use Hire / Sync to roster to mark a candidate as hired.')
                ->required(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['candidate', 'rosterGuard']))
            ->columns([
                TextColumn::make('reference')->searchable(),
                TextColumn::make('candidate.name')->searchable(),
                TextColumn::make('jobPosting.title')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->after(function (Application $record) {
                        if ($record->fresh()?->status === 'hired') {
                            app(HireApplication::class)($record->fresh(), auth()->id());
                        }
                    }),
                Action::make('documents')
                    ->icon(Heroicon::OutlinedDocument)
                    ->modalHeading('Application documents')
                    ->modalContent(function (Application $record) {
                        $lines = $record->documents->map(function ($doc) {
                            return e($doc->type).' — '.e($doc->file_name).' ('.$doc->status.')';
                        })->implode('<br>');

                        return new HtmlString($lines ?: 'No documents.');
                    })
                    ->modalSubmitAction(false),
                Action::make('hire')
                    ->label(fn (Application $record) => $record->status === 'hired' ? 'Sync to roster' : 'Hire')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Application $record) => $record->status !== 'hired' || ! $record->hasRosterGuard())
                    ->action(function (Application $record) {
                        $wasHired = $record->status === 'hired';

                        app(HireApplication::class)($record, auth()->id());

                        Notification::make()
                            ->title($wasHired
                                ? 'Candidate synced to guard roster'
                                : 'Candidate hired and added to guard roster')
                            ->success()
                            ->send();
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
            'index' => ManageApplications::route('/'),
        ];
    }
}
