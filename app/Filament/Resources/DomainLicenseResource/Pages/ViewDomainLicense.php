<?php

declare(strict_types=1);

namespace App\Filament\Resources\DomainLicenseResource\Pages;

use App\Filament\Resources\DomainLicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDomainLicense extends ViewRecord
{
    protected static string $resource = DomainLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
