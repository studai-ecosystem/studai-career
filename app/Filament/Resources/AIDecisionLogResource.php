<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AIDecisionLogResource\Pages;
use App\Models\AIDecisionLog;
use App\Models\HumanOverride;
use App\Services\ResponsibleAI\HumanOverrideService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AIDecisionLogResource extends Resource
{
    protected static ?string $model = AIDecisionLog::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static \UnitEnum|string|null $navigationGroup = 'Responsible AI';

    protected static ?string $navigationLabel = 'AI Decision Audit Log';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'decision_type';

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = AIDecisionLog::where('bias_flagged', true)
                ->where('was_overridden', false)
                ->whereDate('created_at', today())
                ->count();
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('decision_type')
                    ->label('Decision Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'shortlist' => 'success',
                        'reject'    => 'danger',
                        'score'     => 'info',
                        'flag'      => 'warning',
                        default     => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->description(fn ($record) => "ID: {$record->subject_id}")
                    ->sortable(),

                TextColumn::make('ai_score')
                    ->label('AI Score')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1).'%' : '—')
                    ->color(fn ($state) => match (true) {
                        $state >= 85  => 'success',
                        $state >= 70  => 'info',
                        $state >= 55  => 'warning',
                        $state !== null => 'danger',
                        default       => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('ai_recommendation')
                    ->label('AI Recommendation')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'shortlist' => 'success',
                        'reject'    => 'danger',
                        'review'    => 'warning',
                        'hold'      => 'gray',
                        default     => 'gray',
                    }),

                TextColumn::make('final_decision')
                    ->label('Final Decision')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'shortlist' => 'success',
                        'reject'    => 'danger',
                        'review'    => 'warning',
                        default     => 'gray',
                    }),

                IconColumn::make('was_overridden')
                    ->label('Overridden')
                    ->boolean()
                    ->trueIcon('heroicon-o-hand-raised')
                    ->falseIcon('heroicon-o-cpu-chip')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('bias_flagged')
                    ->label('Bias Flag')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('confidence')
                    ->label('Confidence')
                    ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100).'%' : '—')
                    ->sortable(),

                TextColumn::make('model_used')
                    ->label('Model')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('bias_flagged')
                    ->label('Bias Flagged')
                    ->placeholder('All')
                    ->trueLabel('Flagged Only')
                    ->falseLabel('Clean Only'),

                TernaryFilter::make('was_overridden')
                    ->label('Override Status')
                    ->placeholder('All')
                    ->trueLabel('Overridden')
                    ->falseLabel('Not Overridden'),

                SelectFilter::make('decision_type')
                    ->label('Decision Type')
                    ->options([
                        'shortlist' => 'Shortlist',
                        'reject'    => 'Reject',
                        'score'     => 'Score',
                        'recommend' => 'Recommend',
                        'flag'      => 'Flag',
                        'ats_score' => 'ATS Score',
                    ]),

                SelectFilter::make('ai_recommendation')
                    ->label('AI Recommendation')
                    ->options([
                        'shortlist' => 'Shortlist',
                        'reject'    => 'Reject',
                        'review'    => 'Review',
                        'hold'      => 'Hold',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('override')
                    ->label('Override')
                    ->icon('heroicon-o-hand-raised')
                    ->color('warning')
                    ->visible(fn (AIDecisionLog $record) => ! $record->was_overridden)
                    ->form([
                        Select::make('override_decision')
                            ->label('Your Decision')
                            ->options([
                                'shortlist' => 'Shortlist',
                                'reject'    => 'Reject',
                                'review'    => 'Send for Review',
                                'hold'      => 'Hold',
                            ])
                            ->required(),
                        Select::make('override_category')
                            ->label('Override Category')
                            ->options(HumanOverride::categories())
                            ->default(HumanOverride::CAT_GENERAL)
                            ->required(),
                        Toggle::make('is_bias_correction')
                            ->label('This is a Bias Correction')
                            ->helperText('Check if you are overriding because of a suspected bias in the AI output.'),
                        Textarea::make('reason')
                            ->label('Reason for Override')
                            ->required()
                            ->minLength(20)
                            ->placeholder('Explain why you are overriding this AI decision…'),
                        Textarea::make('justification')
                            ->label('Additional Justification')
                            ->placeholder('Optional — any further context or evidence…'),
                    ])
                    ->action(function (AIDecisionLog $record, array $data): void {
                        app(HumanOverrideService::class)->override(
                            $record,
                            $data['override_decision'],
                            $data['reason'],
                            [
                                'category'           => $data['override_category'],
                                'is_bias_correction' => $data['is_bias_correction'] ?? false,
                                'justification'      => $data['justification'] ?? null,
                            ]
                        );
                        Notification::make()
                            ->title('Override recorded')
                            ->body("AI decision overridden → {$data['override_decision']}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->poll('30s');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('AI Decision Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('decision_type')->badge(),
                    TextEntry::make('ai_score')
                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1).'%' : '—'),
                    TextEntry::make('ai_recommendation')->badge(),
                    TextEntry::make('confidence')
                        ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100).'%' : '—'),
                    TextEntry::make('model_used')->badge()->color('gray'),
                    TextEntry::make('final_decision')->badge(),
                    IconEntry::make('was_overridden')->boolean()->label('Human Override Applied'),
                    IconEntry::make('bias_flagged')->boolean()->label('Bias Flagged'),
                    TextEntry::make('created_at')->dateTime(),
                ]),

            Section::make('Natural Language Explanation')
                ->schema([
                    TextEntry::make('natural_language_explanation')
                        ->label('')
                        ->columnSpanFull()
                        ->placeholder('No explanation generated.'),
                ]),

            Section::make('Score Factors (XAI Breakdown)')
                ->schema([
                    TextEntry::make('score_factors')
                        ->label('')
                        ->formatStateUsing(function ($state) {
                            if (empty($state)) {
                                return 'No factor breakdown available.';
                            }
                            $lines = [];
                            foreach ($state as $f) {
                                $bar   = str_repeat('█', (int) (($f['value'] ?? 0) / 10));
                                $lines[] = sprintf(
                                    '%-30s %s %s%% (weight: %s%%)',
                                    $f['factor'] ?? '?',
                                    $bar,
                                    round($f['value'] ?? 0, 1),
                                    round(($f['weight'] ?? 0) * 100, 0)
                                );
                            }
                            return implode("\n", $lines);
                        })
                        ->columnSpanFull(),
                ]),

            Section::make('Bias Indicators')
                ->hidden(fn (AIDecisionLog $record) => ! $record->bias_flagged)
                ->schema([
                    TextEntry::make('bias_indicators')
                        ->label('')
                        ->formatStateUsing(function ($state) {
                            if (empty($state)) {
                                return 'No indicators recorded.';
                            }
                            return collect($state)->map(fn ($i) =>
                                "[{$i['severity']}] {$i['type']}: {$i['detail']}"
                            )->join("\n");
                        })
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAIDecisionLogs::route('/'),
            'view'  => Pages\ViewAIDecisionLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Read-only resource — created by the system
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
