<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AIDisclaimerResource\Pages;
use App\Models\AIDisclaimer;
use App\Services\ResponsibleAI\AIDisclaimerService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AIDisclaimerResource extends Resource
{
    protected static ?string $model = AIDisclaimer::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static \UnitEnum|string|null $navigationGroup = 'Responsible AI';

    protected static ?string $navigationLabel = 'AI Disclaimers';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Disclaimer Configuration')
                ->columns(2)
                ->schema([
                    TextInput::make('key')
                        ->label('Unique Key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('ai_screening_advisory')
                        ->helperText('Lowercase snake_case — used in code to reference this disclaimer.'),

                    TextInput::make('title')
                        ->label('Display Title')
                        ->required()
                        ->maxLength(200),

                    Select::make('context')
                        ->label('Display Context')
                        ->options([
                            AIDisclaimer::CTX_EMPLOYER_SCREENING => 'Employer Screening',
                            AIDisclaimer::CTX_CANDIDATE_RESULT   => 'Candidate Result',
                            AIDisclaimer::CTX_ADMIN              => 'Admin Panel',
                            AIDisclaimer::CTX_GLOBAL             => 'Global (everywhere)',
                        ])
                        ->required(),

                    Select::make('severity')
                        ->label('Severity')
                        ->options([
                            AIDisclaimer::SEV_INFO     => 'Info',
                            AIDisclaimer::SEV_WARNING  => 'Warning',
                            AIDisclaimer::SEV_CRITICAL => 'Critical',
                        ])
                        ->default(AIDisclaimer::SEV_INFO)
                        ->required(),

                    TextInput::make('display_order')
                        ->label('Display Order')
                        ->numeric()
                        ->default(10),
                ]),

            Section::make('Disclaimer Body')
                ->schema([
                    Textarea::make('body')
                        ->label('Message Text')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

            Section::make('Visibility & Acknowledgment')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Toggle::make('requires_acknowledgment')
                        ->label('Requires User Acknowledgment')
                        ->helperText('User must click "I Understand" before proceeding.'),
                    Toggle::make('show_to_employer')
                        ->label('Show to Employers / Recruiters')
                        ->default(true),
                    Toggle::make('show_to_candidate')
                        ->label('Show to Candidates'),
                    Toggle::make('show_to_admin')
                        ->label('Show in Admin Panel')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->description(fn ($record) => \Illuminate\Support\Str::limit($record->body, 60)),

                TextColumn::make('context')
                    ->label('Context')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        AIDisclaimer::CTX_EMPLOYER_SCREENING => 'Employer',
                        AIDisclaimer::CTX_CANDIDATE_RESULT   => 'Candidate',
                        AIDisclaimer::CTX_ADMIN              => 'Admin',
                        AIDisclaimer::CTX_GLOBAL             => 'Global',
                        default => $state,
                    }),

                TextColumn::make('severity')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'warning'  => 'warning',
                        'critical' => 'danger',
                        default    => 'info',
                    }),

                IconColumn::make('requires_acknowledgment')
                    ->label('Ack Required')
                    ->boolean(),

                IconColumn::make('show_to_employer')
                    ->label('Employer')
                    ->boolean(),

                IconColumn::make('show_to_candidate')
                    ->label('Candidate')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
                TernaryFilter::make('requires_acknowledgment')->label('Requires Ack'),
                SelectFilter::make('context')
                    ->options([
                        AIDisclaimer::CTX_EMPLOYER_SCREENING => 'Employer Screening',
                        AIDisclaimer::CTX_CANDIDATE_RESULT   => 'Candidate Result',
                        AIDisclaimer::CTX_ADMIN              => 'Admin',
                        AIDisclaimer::CTX_GLOBAL             => 'Global',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'info'     => 'Info',
                        'warning'  => 'Warning',
                        'critical' => 'Critical',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('seed_defaults')
                    ->label('Seed Default Disclaimers')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('This will insert the 5 mandatory responsible AI disclaimers (existing ones will not be overwritten).')
                    ->action(function (): void {
                        AIDisclaimerService::seedDefaults();
                        Notification::make()->title('Default disclaimers seeded')->success()->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAIDisclaimers::route('/'),
            'create' => Pages\CreateAIDisclaimer::route('/create'),
            'edit'   => Pages\EditAIDisclaimer::route('/{record}/edit'),
        ];
    }
}
