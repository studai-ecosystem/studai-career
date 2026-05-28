<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Strip any form fields that don't map to fillable model columns.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['salary_negotiable'], $data['openings'], $data['deadline'], $data['extracted_skills']);
        return $data;
    }
}
