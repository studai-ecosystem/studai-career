<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Actions;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Illuminate\Support\Str;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static \UnitEnum|string|null $navigationGroup = 'Business Operations';

    protected static ?string $navigationLabel = 'Pricing Plans';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plan Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set): void {
                                if ($state !== null) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique identifier used in URLs and code.'),

                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix(fn (callable $get) => $get('currency') ?? 'INR'),

                        Select::make('currency')
                            ->options([
                                'INR' => 'INR (₹)',
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                            ])
                            ->default('INR')
                            ->required(),

                        Select::make('billing_period')
                            ->options([
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'yearly' => 'Yearly',
                                'lifetime' => 'Lifetime',
                            ])
                            ->default('monthly')
                            ->required(),
                    ]),

                Section::make('Limits & Credits')
                    ->columns(2)
                    ->schema([
                        TextInput::make('ai_credits')
                            ->label('AI Credits')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Monthly AI credits granted to subscribers.'),

                        TextInput::make('applications_limit')
                            ->label('Applications Limit')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Leave empty for unlimited.'),

                        TextInput::make('job_alerts_limit')
                            ->label('Job Alerts Limit')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Leave empty for unlimited.'),

                        TextInput::make('api_calls_limit')
                            ->label('API Calls Limit')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Leave empty for unlimited.'),
                    ]),

                Section::make('Features & Capabilities')
                    ->columns(2)
                    ->schema([
                        TagsInput::make('features')
                            ->label('Feature Highlights')
                            ->placeholder('Add a feature')
                            ->columnSpanFull()
                            ->helperText('Shown on the pricing page.'),

                        Toggle::make('priority_support')
                            ->label('Priority Support'),

                        Toggle::make('api_access')
                            ->label('API Access'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Featured (Most Popular)'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Payment Gateway IDs')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('razorpay_plan_id')
                            ->label('Razorpay Plan ID')
                            ->maxLength(255),

                        TextInput::make('payu_plan_id')
                            ->label('PayU Plan ID')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money(fn (SubscriptionPlan $record) => $record->currency)
                    ->sortable(),

                TextColumn::make('billing_period')
                    ->badge()
                    ->sortable(),

                TextColumn::make('ai_credits')
                    ->label('AI Credits')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('user_subscriptions_count')
                    ->label('Subscribers')
                    ->counts('userSubscriptions')
                    ->badge(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (SubscriptionPlan $record): void {
                        $new = $record->replicate();
                        $new->name = $record->name . ' (Copy)';
                        $new->slug = $record->slug . '-copy-' . Str::random(4);
                        $new->is_featured = false;
                        $new->save();
                    }),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'view' => Pages\ViewSubscriptionPlan::route('/{record}'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
