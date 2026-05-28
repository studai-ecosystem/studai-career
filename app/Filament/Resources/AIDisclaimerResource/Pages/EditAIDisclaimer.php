<?php
declare(strict_types=1);
namespace App\Filament\Resources\AIDisclaimerResource\Pages;
use App\Filament\Resources\AIDisclaimerResource;
use Filament\Resources\Pages\EditRecord;
class EditAIDisclaimer extends EditRecord
{
    protected static string $resource = AIDisclaimerResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()];
    }
}
