<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;


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
        $rfidField = UserResource::getRfidField();

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
                Forms\Components\Section::make('RFID')
                    ->description('If the patient has an RFID card, please scan it on the RFID reader to retrieve the RFID #')
                    ->schema($rfidField)
                    ->visible(fn (callable $get) => $get('type') === 'patient')
                    ->columns(2)
                    ->columnSpan(6),
                Forms\Components\Section::make('Password')
                    ->schema($passwordFields)
                    ->visible(fn ($livewire) => !($livewire instanceof ViewRecord)),
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
        ->columns([

            Tables\Columns\ImageColumn::make('avatar_url')
                ->label('Avatar')
                ->getStateUsing(fn ($record) => $record->avatar_url ?? asset('images/avatar_placeholder.png'))
                ->circular(),
            Tables\Columns\TextColumn::make('name')
                ->label('Full Name')
                ->searchable(['first_name', 'last_name'])
                ->sortable(['first_name', 'last_name']),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('type')
                ->label('User Type')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('rfid_points_sum')
                ->label('RFID Points')
                ->sortable()
                ->getStateUsing(fn ($record) => $record->rfid_points_sum),
        ])
        ->filters([
            // Filter users where type is 'patient'
            \Filament\Tables\Filters\SelectFilter::make('type')
                ->default('patient')
                ->query(fn ($query) => $query->where('type', 'patient')),
        ])
        // ->actions([
        //     Action::make('downloadPDF')
        //         ->label('Download ID Card')
        //         // ->icon('heroicon-s-download')
        //         ->color('success')
        //         ->action(function ($record) {
        //             $pdf = PDF::loadView('patient.user-card', ['user' => $record]);
        //             return response()->streamDownload(
        //                 fn () => print($pdf->output()),
        //                 'user-id-card.pdf'
        //             );
        //         })
        //         ->requiresConfirmation()
        //         ->tooltip('Download the user ID card as a PDF'),
        // ])
        ;
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
