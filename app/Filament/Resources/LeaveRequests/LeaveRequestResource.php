<?php

namespace App\Filament\Resources\LeaveRequests;

use App\Actions\NotifyStaff;
use App\Filament\Resources\LeaveRequests\Pages\ManageLeaveRequests;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Leave requests';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guard_id')
                    ->relationship('assignedGuard', 'full_name')
                    ->searchable()
                    ->required(),
                DatePicker::make('starts_on')->required(),
                DatePicker::make('ends_on')->required(),
                Textarea::make('reason')->columnSpanFull(),
                Select::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])->required()->default('pending'),
                Textarea::make('review_notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assignedGuard.full_name')->label('Guard')->searchable(),
                TextColumn::make('starts_on')->date()->sortable(),
                TextColumn::make('ends_on')->date()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->visible(fn (LeaveRequest $record): bool => $record->status === 'pending')
                    ->action(function (LeaveRequest $record, NotifyStaff $notifyStaff): void {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        $notifyStaff(
                            $record->assignedGuard,
                            'leave_decision',
                            'Leave approved',
                            'Your leave request was approved.',
                            ['leave_request_id' => $record->id],
                        );
                        Notification::make()->title('Leave approved')->success()->send();
                    }),
                Action::make('reject')
                    ->color('danger')
                    ->visible(fn (LeaveRequest $record): bool => $record->status === 'pending')
                    ->action(function (LeaveRequest $record, NotifyStaff $notifyStaff): void {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        $notifyStaff(
                            $record->assignedGuard,
                            'leave_decision',
                            'Leave rejected',
                            'Your leave request was rejected. Contact operations if needed.',
                            ['leave_request_id' => $record->id],
                        );
                        Notification::make()->title('Leave rejected')->warning()->send();
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
            'index' => ManageLeaveRequests::route('/'),
        ];
    }
}
