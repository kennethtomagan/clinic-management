<?php

namespace App\Filament\Resources\PatienResource\RelationManagers;

use App\Models\Health;
use App\Models\User;
use App\Filament\Resources\MedicalResource\Pages;
use App\Support\AvatarOptions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
// use Filament\Tables\Actions\ExportBulkAction;
// use Pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MedicalRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalRecords';

    public static ?string $label = 'Medical';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make([
                
                Forms\Components\Select::make('doctor_id')
                ->label('Doctor')
                ->allowHtml()
                ->columnSpan(6)
                ->required()
                ->options(function (Get $get) {
                    $doctors = User::where('type', User::DOCTOR_TYPE)->get();
                    return $doctors->mapWithKeys(function ($doctor) {
                        return [$doctor->getKey() => AvatarOptions::getOptionString($doctor)];
                    })->toArray();
                })
                ->native(false),

            Forms\Components\Select::make('clinic_id')
                ->relationship('clinic', 'name')
                ->preload()
                ->required()
                ->searchable()
                ->columnSpan(6),

            Forms\Components\DatePicker::make('checkup_date')
                ->label('Check-up date')
                ->native(false)
                ->displayFormat('M d, Y')
                ->closeOnDateSelection()
                ->default(Carbon::now())
                ->required()
                ->columnSpan(6),

        ])
        ->columns(12)
        ->columnSpanFull(),

        Forms\Components\Section::make('Health records')
        ->schema([
            Forms\Components\TextInput::make('vision_right_eye')
                ->label('Vision righ eye')
                ->required()
                ->placeholder('20/20')
                ->columnSpan(6),

            Forms\Components\TextInput::make('vision_left_eye')
                ->label('Vision left eye')
                ->required()
                ->placeholder('20/20')
                ->columnSpan(6),

            Forms\Components\Textarea::make('diagnosis')
                ->label('Diagnosis')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Textarea::make('recommendations')
                ->columnSpanFull(),

            Forms\Components\Textarea::make('notes')
                ->columnSpanFull(),
            ])
    ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicals::route('/'),
            'create' => Pages\CreateMedical::route('/create'),
            'edit' => Pages\EditMedical::route('/{record}/edit'),
            'view' => Pages\ViewMedical::route('/{record}/show'),
        ];
    }
}
