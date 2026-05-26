<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationSessionResource\Pages;
use App\Models\EvaluationSession;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ViewAction;

class EvaluationSessionResource extends Resource
{
    protected static ?string $model = EvaluationSession::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static \UnitEnum|string|null $navigationGroup = 'Orin™ Evaluation';

    protected static ?string $navigationLabel = 'Evaluation Sessions';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'in_progress')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Session ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('application.job.title')
                    ->label('Job')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('application.guest_name')
                    ->label('Candidate')
                    ->getStateUsing(fn($record) =>
                        $record->application?->is_guest_applicant
                            ? $record->application?->guest_name
                            : $record->user?->name
                    )
                    ->searchable(['guest_name']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'expired'     => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('current_question_index')
                    ->label('Q Progress')
                    ->getStateUsing(fn($r) => ($r->current_question_index ?? 0) . ' / ' . ($r->total_questions ?? 15)),

                TextColumn::make('weighted_score')
                    ->label('Score')
                    ->suffix('%')
                    ->sortable()
                    ->numeric(decimalPlaces: 1),

                TextColumn::make('tab_switch_count')
                    ->label('Tab Switches')
                    ->sortable()
                    ->color(fn($state) => $state > 3 ? 'danger' : ($state > 0 ? 'warning' : 'success')),

                TextColumn::make('flagged_for_review')
                    ->label('Flagged')
                    ->badge()
                    ->getStateUsing(fn($r) => $r->flagged_for_review ? 'Yes' : 'No')
                    ->color(fn($state) => $state === 'Yes' ? 'danger' : 'success'),

                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'expired'     => 'Expired',
                    ]),
                SelectFilter::make('flagged_for_review')
                    ->label('Flagged')
                    ->options([1 => 'Yes', 0 => 'No']),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationSessions::route('/'),
        ];
    }
}
