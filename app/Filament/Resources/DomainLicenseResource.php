<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DomainLicenseResource\Pages;
use App\Models\DomainLicense;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DomainLicenseResource extends Resource
{
    protected static ?string $model = DomainLicense::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';

    protected static \UnitEnum|string|null $navigationGroup = 'Business Operations';

    protected static ?string $navigationLabel = 'Domain Licenses';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Domain')
                    ->columns(2)
                    ->schema([
                        TextInput::make('domain')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Email domain, e.g. studai.one. Users registering with this domain are auto-licensed.')
                            ->dehydrateStateUsing(fn (string $state): string => DomainLicense::normalizeDomain($state)),

                        TextInput::make('organization_name')
                            ->label('Organization Name')
                            ->maxLength(255),
                    ]),

                Section::make('License')
                    ->columns(2)
                    ->schema([
                        Select::make('subscription_plan_id')
                            ->label('Plan')
                            ->relationship('subscriptionPlan', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Plan automatically assigned to licensed users.'),

                        TextInput::make('total_seats')
                            ->label('Total Seats')
                            ->numeric()
                            ->minValue(0)
                            ->default(5)
                            ->required()
                            ->helperText('Number of licenses. Use 0 for unlimited.'),

                        TextInput::make('seats_used')
                            ->label('Seats Used')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabledOn('create'),

                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->native(false)
                            ->helperText('Leave empty for no expiry.'),

                        Toggle::make('auto_assign')
                            ->label('Auto-assign on registration')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                TextColumn::make('organization_name')
                    ->label('Organization')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('subscriptionPlan.name')
                    ->label('Plan')
                    ->badge(),

                TextColumn::make('seats_used')
                    ->label('Seats')
                    ->formatStateUsing(fn (DomainLicense $record): string => $record->total_seats === 0
                        ? $record->seats_used . ' / ∞'
                        : $record->seats_used . ' / ' . $record->total_seats)
                    ->badge()
                    ->color(fn (DomainLicense $record): string => $record->hasAvailableSeats() ? 'success' : 'danger'),

                IconColumn::make('auto_assign')
                    ->label('Auto')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('expires_at')
                    ->dateTime('M d, Y')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('domain')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('auto_assign')
                    ->label('Auto-assign'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomainLicenses::route('/'),
            'create' => Pages\CreateDomainLicense::route('/create'),
            'view' => Pages\ViewDomainLicense::route('/{record}'),
            'edit' => Pages\EditDomainLicense::route('/{record}/edit'),
        ];
    }
}
