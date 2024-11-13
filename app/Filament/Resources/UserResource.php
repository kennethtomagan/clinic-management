<?php

namespace App\Filament\Resources;

use App\Enums\DoctorStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'All Users';

    public static function form(Form $form): Form
    {
        $passwordFields = static::getPasswordFields();
        $fields = static::getFields();
        $doctorFields = static::getDoctorFields();
        $rfidField = static::getRfidField();

        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->schema($fields),
                Forms\Components\Section::make('Doctor Details')
                    ->relationship('doctorDetails')
                    ->schema($doctorFields)
                    ->visible(fn (callable $get) => $get('type') === 'doctor'),

                Forms\Components\Section::make('RFID')
                    ->description('If the patient has an RFID card, please scan it on the RFID reader to retrieve the RFID #')
                    ->schema($rfidField)
                    ->visible(fn (callable $get) => $get('type') === 'patient')
                    ->columns(2),
                Forms\Components\Section::make('Password')
                    ->schema($passwordFields)
                    ->visible(fn ($livewire) => !($livewire instanceof ViewRecord)),

            ]);
    }

    public static function getFields()
    {
        return [
            Forms\Components\FileUpload::make('avatar_url')
                ->label('Photo')
                ->avatar()
                ->imageEditor()
                ->directory('patient')
                ->rules('mimes:jpeg,png|max:1024'),

            Forms\Components\TextInput::make('first_name')
                ->maxLength(255)
                ->required(),

            Forms\Components\TextInput::make('last_name')
                ->maxLength(255)
                ->required(),

            Forms\Components\Select::make('gender')
                ->options([
                    'Male' => 'Male',
                    'Female' => 'Female',
                    'Other' => 'Other',
                ]),

            Forms\Components\DatePicker::make('date_of_birth')
                ->maxDate('today'),

            Forms\Components\TextInput::make('email')
                    ->label('Email address')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('phone')
                    ->maxLength(255),

            Forms\Components\Textarea::make('address'),

            Forms\Components\Select::make('type')
                ->required()
                ->options([
                    USER::PATIENT_TYPE => 'Patient',
                    USER::RECEPTIONIST_TYPE => 'Receptionist',
                    USER::DOCTOR_TYPE => 'Doctor',
                    USER::ADMIN_TYPE => 'Admin',
                ])
                ->reactive()
                ->label('User Type'),
        ];

    }

    public static function getPasswordFields()
    {
        return [
            // Password Fields
            Forms\Components\TextInput::make('password')
                ->password() // Make it a password field
                ->label('New Password')
                ->minLength(8) // Optional: Add password length validation
                ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)) // Only update if password is provided
                ->maxLength(255)
                ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                ->hint(
                    fn ($livewire) => $livewire instanceof CreateRecord 
                    ? ''
                    : 'Leave empty if you do not want to change the password'
                )
                ->visible(fn ($livewire) => !($livewire instanceof ViewRecord)),

            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->label('Confirm Password')
                ->same('password') // Ensure it matches the password field
                ->nullable() // Allow it to be empty as well
                ->maxLength(255)
                ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                ->visible(fn ($livewire) => !($livewire instanceof ViewRecord) || !($livewire instanceof ListRecords)),
        ];
    }

    public static function getRfidField()
    {
        return [
            Forms\Components\TextInput::make('rfid_number')
                ->label('RFID #')
                ->extraAttributes([
                    'onkeydown' => "if(event.key === 'Enter'){ event.preventDefault(); }"
                ])
                ->maxLength(255),

            Forms\Components\Placeholder::make('rfid_points_sum')
                ->label('RFID Points')
        ];
    }

    public static function getDoctorFields()
    {
        return [
            Forms\Components\TextInput::make('education')
                ->label('Education')
                ->required()
                ->nullable(),
            
            Forms\Components\TextInput::make('specialization')
                ->placeholder('Ophthalmology')
                ->required()
                ->maxLength(255),
                
            Forms\Components\TagsInput::make('subspecialty')
                ->placeholder('Eye Diseases, Vision Correction, Microsurgery of the Eye')
                ->helperText('Click enter to add new subspecialty.')
                ->separator(','),

            Forms\Components\TextInput::make('years_of_experience')
                ->label('Years of Experience')
                ->numeric()
                ->required(),
            
            Forms\Components\Select::make('clinic_id')
                ->relationship('clinic', 'name')
                ->label('Clinic')
                ->required(),

            // Forms\Components\Select::make('status')
            //     ->options([
            //         User::STATUS_ACTIVE => 'Active',
            //         User::STATUS_LEAVE => 'Leave',
            //         User::STATUS_RESIGNED => 'Resigned',
            //     ]),


            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->options(DoctorStatus::class)
                ->required(),
            // Forms\Components\CheckboxList::make('consultation_availability')
            //     ->label('Consultation Availability')
            //     ->options([
            //         'in_person' => 'In-Person',
            //         'online' => 'online',
            //     ]),

            Forms\Components\Textarea::make('profile_description')
                ->label('Profile Description'),
        ];
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

                Tables\Columns\TextColumn::make('type')
                    ->label('User Type')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            // ->query(User::query()->where('id', '!=', 1))
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
