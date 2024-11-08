<?php

namespace App\Filament\Resources\MedicalResource\Pages;

use App\Filament\Resources\MedicalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedical extends EditRecord
{
    protected static string $resource = MedicalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
