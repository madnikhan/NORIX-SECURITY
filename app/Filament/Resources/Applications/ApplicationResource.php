<?php

namespace App\Filament\Resources\Applications;

use App\Filament\Resources\Applications\Pages\ManageApplications;
use App\Models\Application;
use App\Models\Guard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
            ])->required(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->searchable(),
                TextColumn::make('candidate.name')->searchable(),
                TextColumn::make('jobPosting.title')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('documents')
                    ->icon(Heroicon::OutlinedDocument)
                    ->modalHeading('Application documents')
                    ->modalContent(function (Application $record) {
                        $lines = $record->documents->map(function ($doc) {
                            return e($doc->type).' — '.e($doc->file_name).' ('.$doc->status.')';
                        })->implode('<br>');

                        return new \Illuminate\Support\HtmlString($lines ?: 'No documents.');
                    })
                    ->modalSubmitAction(false),
                Action::make('hire')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Application $record) => $record->status !== 'hired')
                    ->action(function (Application $record) {
                        $history = $record->status_history ?? [];
                        $history[] = [
                            'status' => 'hired',
                            'at' => now()->toIso8601String(),
                            'by' => auth()->id(),
                        ];
                        $record->update([
                            'status' => 'hired',
                            'status_history' => $history,
                        ]);

                        Guard::query()->updateOrCreate(
                            ['email' => $record->candidate->email],
                            [
                                'full_name' => $record->candidate->name,
                                'phone' => $record->candidate->phone,
                                'sia_licence_number' => $record->sia_licence_number ?: 'PENDING',
                                'sia_expiry' => $record->sia_expiry?->toDateString() ?: now()->addYear()->toDateString(),
                                'application_id' => $record->id,
                                'is_active' => true,
                            ]
                        );

                        Notification::make()->title('Candidate hired and added to guard roster')->success()->send();
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
