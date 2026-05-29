<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class LatestApplications extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        try {
            return Schema::hasTable('applications');
        } catch (\Throwable) {
            return false;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Application::query()->with(['user', 'job.company'])->latest()->limit(10)
            )
            ->heading('Latest Job Applications')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record): string => $record->user?->email ?? ''),

                TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->description(fn ($record): string => $record->job?->company?->name ?? ''),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'shortlisted' => 'primary',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('d M Y, H:i')
                    ->description(fn ($record): string => $record->created_at->diffForHumans())
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
