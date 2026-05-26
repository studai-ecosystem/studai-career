<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BulkEmailLogResource\Pages;
use App\Models\BulkEmailLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BulkEmailLogResource extends Resource
{
    protected static ?string $model = BulkEmailLog::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static \UnitEnum|string|null $navigationGroup = 'Orin™ Evaluation';

    protected static ?string $navigationLabel = 'Bulk Email Logs';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),

                TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('email_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'evaluation_invite'    => 'info',
                        'shortlist_result'     => 'success',
                        'rejection'            => 'danger',
                        'application_received' => 'gray',
                        default                => 'gray',
                    }),

                TextColumn::make('total_recipients')->label('Total')->sortable(),
                TextColumn::make('sent_count')->label('Sent')->sortable()->color('success'),
                TextColumn::make('failed_count')->label('Failed')->sortable()->color(fn($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'completed' => 'success',
                        'in_progress' => 'info',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('started_at')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('completed_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('email_type')
                    ->options([
                        'evaluation_invite'    => 'Evaluation Invite',
                        'shortlist_result'     => 'Shortlist Result',
                        'rejection'            => 'Rejection',
                        'application_received' => 'Application Received',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'completed'   => 'Completed',
                        'in_progress' => 'In Progress',
                        'failed'      => 'Failed',
                    ]),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkEmailLogs::route('/'),
        ];
    }
}
