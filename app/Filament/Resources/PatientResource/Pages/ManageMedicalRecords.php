<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\MedicalResource;
use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ManageMedicalRecords extends ManageRelatedRecords
{
    protected static string $resource = PatientResource::class;

    protected static string $relationship = 'medicalRecords';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = 'Medical Record';
    
    public static ?string $title = 'Medical Records';

    public static function getNavigationLabel(): string
    {
        return 'Medical Records';
    }

    public function form(Form $form): Form
    {
        return $form->schema(MedicalResource::getFormFields($this->record->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('patient.avatar_url')
                    ->label('Avatar')
                    ->getStateUsing(fn ($record) => $record->patient->avatar_url ?? asset('images/avatar_placeholder.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clinic.name')
                    ->label('Clinic')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkup_date')
                    ->label('Checkup Date')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Add any table filters here
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Medical Record'), // Changed the label for the create action
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ])
            ->emptyStateHeading('No health records') // Changed the empty table message
            ->emptyStateDescription('Add new medical records using the button above.');
    }
}
