<?php

declare(strict_types=1);

namespace App\Filament\Resources\AIDecisionLogResource\Pages;

use App\Filament\Resources\AIDecisionLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAIDecisionLogs extends ListRecords
{
    protected static string $resource = AIDecisionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
