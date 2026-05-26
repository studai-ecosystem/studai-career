<?php

declare(strict_types=1);

namespace App\Filament\Resources\EvaluationSessionResource\Pages;

use App\Filament\Resources\EvaluationSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationSessions extends ListRecords
{
    protected static string $resource = EvaluationSessionResource::class;
}
