<?php

declare(strict_types=1);

namespace App\Filament\Resources\BulkEmailLogResource\Pages;

use App\Filament\Resources\BulkEmailLogResource;
use Filament\Resources\Pages\ListRecords;

class ListBulkEmailLogs extends ListRecords
{
    protected static string $resource = BulkEmailLogResource::class;
}
