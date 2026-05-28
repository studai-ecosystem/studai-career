<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    /**
     * Strip any form fields that don't map to fillable model columns
     * (safety net for legacy/mismatched form field names).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove fields that don't exist in the model's fillable / DB schema
        unset($data['salary_negotiable'], $data['openings'], $data['deadline'], $data['extracted_skills']);
        return $data;
    }
}
