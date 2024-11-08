<?php

namespace App\Filament\Resources\MedicalResource\Pages;

use App\Filament\Resources\MedicalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMedical extends ViewRecord
{
    protected static string $resource = MedicalResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->icon('heroicon-o-pencil'),
            // Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }
}
