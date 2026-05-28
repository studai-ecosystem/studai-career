<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\HumanOverrideResource\Pages;
use App\Models\HumanOverride;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HumanOverrideResource extends Resource
{
    protected static ?string $model = HumanOverride::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static \UnitEnum|string|null $navigationGroup = 'Responsible AI';

    protected static ?string $navigationLabel = 'Human Overrides';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) HumanOverride::whereDate('created_at', today())->count() ?: null;
        } catch (\Throwable) {
            return null;
        }
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

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->description(fn ($record) => "ID: {$record->subject_id}"),

                TextColumn::make('overrider.name')
                    ->label('Overridden By')
                    ->description(fn ($record) => $record->overrider_role),

                TextColumn::make('original_decision')
                    ->label('AI Said')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'shortlist' => 'success',
                        'reject'    => 'danger',
                        'review'    => 'warning',
                        default     => 'gray',
                    }),

                TextColumn::make('override_decision')
                    ->label('Human Said')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'shortlist' => 'success',
                        'reject'    => 'danger',
                        'review'    => 'warning',
                        default     => 'gray',
                    }),

                TextColumn::make('override_category')
                    ->label('Category')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => HumanOverride::categories()[$state] ?? ucfirst((string) $state)),

                IconColumn::make('is_bias_correction')
                    ->label('Bias Fix')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('original_score')
                    ->label('Score Δ')
                    ->formatStateUsing(function ($state, HumanOverride $record) {
                        if ($state === null && $record->override_score === null) {
                            return '—';
                        }
                        $orig = (float) ($state ?? 0);
                        $new  = (float) ($record->override_score ?? 0);
                        $delta = $new - $orig;
                        return sprintf('%s%.1f', $delta >= 0 ? '+' : '', $delta);
                    })
                    ->color(fn ($state, HumanOverride $record) => (($record->override_score ?? 0) - ($state ?? 0)) >= 0 ? 'success' : 'danger'),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->reason),

                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_bias_correction')
                    ->label('Bias Corrections Only')
                    ->placeholder('All overrides')
                    ->trueLabel('Bias corrections only')
                    ->falseLabel('Non-bias overrides'),

                SelectFilter::make('override_category')
                    ->label('Category')
                    ->options(HumanOverride::categories()),

                SelectFilter::make('override_decision')
                    ->label('Resulting Decision')
                    ->options([
                        'shortlist' => 'Shortlist',
                        'reject'    => 'Reject',
                        'review'    => 'Review',
                        'hold'      => 'Hold',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Override Details')
                ->columns(2)
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('overrider.name')->label('Overridden By'),
                    \Filament\Infolists\Components\TextEntry::make('overrider_role')->badge()->color('info'),
                    \Filament\Infolists\Components\TextEntry::make('original_decision')->badge(),
                    \Filament\Infolists\Components\TextEntry::make('override_decision')->badge(),
                    \Filament\Infolists\Components\TextEntry::make('original_score')
                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1).'%' : '—'),
                    \Filament\Infolists\Components\TextEntry::make('override_score')
                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1).'%' : '—'),
                    \Filament\Infolists\Components\IconEntry::make('is_bias_correction')->boolean(),
                    \Filament\Infolists\Components\TextEntry::make('override_category')
                        ->formatStateUsing(fn ($state) => HumanOverride::categories()[$state] ?? ucfirst($state)),
                ]),
            \Filament\Schemas\Components\Section::make('Reason & Justification')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('reason')->columnSpanFull(),
                    \Filament\Infolists\Components\TextEntry::make('justification')
                        ->placeholder('No additional justification provided.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHumanOverrides::route('/'),
            'view'  => Pages\ViewHumanOverride::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Overrides are permanent audit records
    }
}
