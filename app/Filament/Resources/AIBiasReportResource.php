<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AIBiasReportResource\Pages;
use App\Models\AIBiasReport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AIBiasReportResource extends Resource
{
    protected static ?string $model = AIBiasReport::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static \UnitEnum|string|null $navigationGroup = 'Responsible AI';

    protected static ?string $navigationLabel = 'Bias Detection Reports';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = AIBiasReport::requiresReview()->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('report_type')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', (string) $state))),

                TextColumn::make('scope')
                    ->label('Scope')
                    ->badge()
                    ->color('gray')
                    ->description(fn ($record) => $record->scope_id ? "ID: {$record->scope_id}" : 'All'),

                TextColumn::make('bias_level')
                    ->label('Bias Level')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'none'     => 'success',
                        'low'      => 'info',
                        'moderate' => 'warning',
                        'high'     => 'danger',
                        'critical' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('bias_severity')
                    ->label('Severity')
                    ->formatStateUsing(fn ($state) => $state !== null ? round((float) $state * 100).'%' : '—')
                    ->sortable(),

                TextColumn::make('total_decisions_analysed')
                    ->label('Decisions Analysed')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->period_start->format('M d') . ' → ' . $record->period_end->format('M d, Y')
                    ),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'reviewed'  => 'info',
                        'actioned'  => 'success',
                        'dismissed' => 'gray',
                        default     => 'gray',
                    }),

                IconColumn::make('requires_review')
                    ->label('Needs Review')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger'),

                IconColumn::make('reviewed')
                    ->label('Reviewed')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Generated')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('bias_level')
                    ->label('Bias Level')
                    ->options([
                        'none'     => 'None',
                        'low'      => 'Low',
                        'moderate' => 'Moderate',
                        'high'     => 'High',
                        'critical' => 'Critical',
                    ]),

                TernaryFilter::make('requires_review')
                    ->label('Requires Review')
                    ->trueLabel('Needs review')
                    ->falseLabel('Already reviewed'),

                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'reviewed'  => 'Reviewed',
                        'actioned'  => 'Actioned',
                        'dismissed' => 'Dismissed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (AIBiasReport $record) => ! $record->reviewed)
                    ->form([
                        Textarea::make('review_notes')
                            ->label('Review Notes')
                            ->placeholder('Summarise findings and any actions taken…')
                            ->required()
                            ->minLength(20),
                        Select::make('status')
                            ->label('Update Status')
                            ->options([
                                'reviewed'  => 'Reviewed — No action needed',
                                'actioned'  => 'Actioned — Changes made',
                                'dismissed' => 'Dismissed — False positive',
                            ])
                            ->required(),
                    ])
                    ->action(function (AIBiasReport $record, array $data): void {
                        $record->update([
                            'reviewed'     => true,
                            'reviewed_by'  => auth()->id(),
                            'reviewed_at'  => now(),
                            'review_notes' => $data['review_notes'],
                            'status'       => $data['status'],
                        ]);
                        Notification::make()->title('Report marked as reviewed')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('report_type')
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                    TextEntry::make('bias_level')->badge(),
                    TextEntry::make('bias_severity')
                        ->formatStateUsing(fn ($state) => $state !== null ? round((float)$state * 100).'%' : '—'),
                    TextEntry::make('total_decisions_analysed')->numeric(),
                    TextEntry::make('period_start')
                        ->formatStateUsing(fn ($state, $record) =>
                            $record->period_start->format('M d, Y').' → '.$record->period_end->format('M d, Y')
                        )
                        ->label('Analysis Period'),
                    TextEntry::make('status')->badge(),
                    IconEntry::make('requires_review')->boolean()->label('Requires Review'),
                    IconEntry::make('reviewed')->boolean(),
                ]),

            Section::make('Recommendations')
                ->schema([
                    TextEntry::make('recommendations')
                        ->label('')
                        ->formatStateUsing(fn ($state) => is_array($state)
                            ? implode("\n\n• ", array_merge(['• ' . ($state[0] ?? '')], array_slice($state, 1)))
                            : 'No recommendations.')
                        ->columnSpanFull(),
                ]),

            Section::make('Protected Attributes Affected')
                ->schema([
                    TextEntry::make('protected_attributes_affected')
                        ->label('')
                        ->formatStateUsing(fn ($state) => is_array($state) && ! empty($state)
                            ? implode(', ', $state)
                            : 'None identified.')
                        ->columnSpanFull(),
                ]),

            Section::make('Reviewer Notes')
                ->hidden(fn (AIBiasReport $record) => ! $record->reviewed)
                ->schema([
                    TextEntry::make('reviewer.name')->label('Reviewed By'),
                    TextEntry::make('reviewed_at')->dateTime(),
                    TextEntry::make('review_notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAIBiasReports::route('/'),
            'view'  => Pages\ViewAIBiasReport::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
