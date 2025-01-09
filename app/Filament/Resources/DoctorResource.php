<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\Doctor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorResource extends UserResource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Doctors';

    public static ?string $label = 'Doctor';

    public static function form(Form $form): Form
    {
        // Get the common fields from UserResource
        $fields = UserResource::getFields();
        $doctorFields = UserResource::getDoctorFields();
        $rfidField = UserResource::getRfidField();

        // Override the 'type' field
        foreach ($fields as &$field) {
            if ($field instanceof Forms\Components\Select && $field->getName() === 'type') {
                $field = $field->default(User::DOCTOR_TYPE)
                    ->disabled()
                    ->dehydrated();
            }
        }

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema($fields),
                Forms\Components\Section::make('Doctor Details')
                    ->visible(fn (callable $get) => $get('type') === USER::DOCTOR_TYPE)
                    ->relationship('doctorDetails')
                    ->schema($doctorFields),

                Forms\Components\Section::make('RFID')
                    ->description('If the patient has an RFID card, please scan it on the RFID reader to retrieve the RFID #')
                    ->schema($rfidField)
                    ->columns(2),
                Forms\Components\Section::make('Password')
                    ->schema(UserResource::getPasswordFields())
                    ->visible(fn ($livewire) => !($livewire instanceof ViewRecord)),
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
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

                Tables\Columns\TextColumn::make('doctorDetails.specialization')
                    ->label('Specialization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctorDetails.years_of_experience')
                    ->label('Years of Experience')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Filter users where type is 'doctor'
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->default('doctor')
                    ->query(fn ($query) => $query->where('type', 'doctor')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()->label('Permanently delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
            
        // return parent::table($table)->filters([
        //     // Filter users where type is 'doctor'
        //     \Filament\Tables\Filters\SelectFilter::make('type')
        //         ->default('doctor')
        //         ->query(fn ($query) => $query->where('type', 'doctor')),
        // ]);
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'view' => Pages\ViewDoctor::route('/{record}'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
