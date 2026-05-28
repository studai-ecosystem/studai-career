<?php
declare(strict_types=1);
namespace App\Filament\Resources\AIBiasReportResource\Pages;
use App\Filament\Resources\AIBiasReportResource;
use Filament\Resources\Pages\ListRecords;
class ListAIBiasReports extends ListRecords
{
    protected static string $resource = AIBiasReportResource::class;
    protected function getHeaderActions(): array { return []; }
}
