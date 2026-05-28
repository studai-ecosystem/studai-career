<?php
declare(strict_types=1);
namespace App\Filament\Resources\HumanOverrideResource\Pages;
use App\Filament\Resources\HumanOverrideResource;
use Filament\Resources\Pages\ListRecords;
class ListHumanOverrides extends ListRecords
{
    protected static string $resource = HumanOverrideResource::class;
    protected function getHeaderActions(): array { return []; }
}
