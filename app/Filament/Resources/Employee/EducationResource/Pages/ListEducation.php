<?php

namespace App\Filament\Resources\Employee\EducationResource\Pages;

use App\Filament\Resources\Employee\EducationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEducation extends ListRecords
{
    protected static string $resource = EducationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
