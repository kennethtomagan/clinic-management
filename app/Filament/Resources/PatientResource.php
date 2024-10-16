<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends UserResource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Patients';

    public static ?string $label = 'Patient';

    public static function form(Form $form): Form
    {
        // Get the common fields from UserResource
        $fields = UserResource::getFields();
        $passwordFields = UserResource::getPasswordFields();

        // Override the 'type' field
        foreach ($fields as &$field) {
            if ($field instanceof Forms\Components\Select && $field->getName() === 'type') {
                $field = $field->default(User::PATIENT_TYPE)
                    ->disabled()
                    ->dehydrated();
            }
        }

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema($fields),
                Forms\Components\Section::make('Password')
                    ->schema($passwordFields)
                    ->visible(fn ($livewire) => !($livewire instanceof ViewRecord)),
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)->filters([
            // Filter users where type is 'patient'
            \Filament\Tables\Filters\SelectFilter::make('type')
                ->default('patient')
                ->query(fn ($query) => $query->where('type', 'patient')),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
