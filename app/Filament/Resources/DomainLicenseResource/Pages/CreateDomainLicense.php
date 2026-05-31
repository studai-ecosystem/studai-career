<?php

declare(strict_types=1);

namespace App\Filament\Resources\DomainLicenseResource\Pages;

use App\Filament\Resources\DomainLicenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainLicense extends CreateRecord
{
    protected static string $resource = DomainLicenseResource::class;
}
