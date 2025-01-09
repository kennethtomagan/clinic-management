<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalResource\Pages;
use App\Filament\Resources\MedicalResource\RelationManagers;
use App\Models\Health;
use App\Models\User;
use App\Support\AvatarOptions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class MedicalResource extends Resource
{
    protected static ?string $model = Health::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getNavigationLabel(): string
    {
        return 'Medical record';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Medical records';
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getLabel(): ?string
    {
        return 'Medical record';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormFields());
    }

    public static function getFormFields($patientId = null)
    {
        return [
            Forms\Components\Section::make([
                Forms\Components\Select::make('patient_id')
                    ->label('Patient')
                    ->allowHtml()
                    ->searchable()
                    ->required()
                    ->columnSpan(6)
                    ->default($patientId)
                    ->getSearchResultsUsing(function (string $search) {
                        $patient = User::where('type', User::PATIENT_TYPE)
                            ->where(function ($query) use ($search) {
                                $query->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get();
                    
                        return $patient->mapWithKeys(function ($patient) {
                                return [$patient->getKey() => AvatarOptions::getOptionString($patient)];
                        })->toArray();
                    })
                    ->options(function (): array {
                        $patients = User::where('type', User::PATIENT_TYPE)->get();

                        return $patients->mapWithKeys(function ($patient) {
                            return [$patient->getKey() => AvatarOptions::getOptionString($patient)];
                        })->toArray();
                    }),
                
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
            ->columns(12)
            ->columnSpanFull(),

        ];
    }

    public static function table(Table $table): Table
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
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
            'index' => Pages\ListMedicals::route('/'),
            'create' => Pages\CreateMedical::route('/create'),
            'edit' => Pages\EditMedical::route('/{record}/edit'),
            'view' => Pages\ViewMedical::route('/{record}/show'),
        ];
    }
}
