<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;

class ListUsers extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'patient' => Tab::make()->query(fn ($query) => $query->where('type', User::PATIENT_TYPE)),
            'doctor' => Tab::make()->query(fn ($query) => $query->where('type', User::DOCTOR_TYPE)),
            'receptionist' => Tab::make()->query(fn ($query) => $query->where('type', User::RECEPTIONIST_TYPE)),
            'admin' => Tab::make()->query(fn ($query) => $query->where('type', User::ADMIN_TYPE)),
        ];
    }
}
