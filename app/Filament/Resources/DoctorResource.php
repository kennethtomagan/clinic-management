<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->aside()
                    ->description('Fill out the personal details of the doctor, including name, gender, and profile photo.')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('Photo')
                            ->avatar()
                            ->imageEditor()
                            ->directory('doctors')
                            ->rules('mimes:jpeg,png|max:1024'),

                        Forms\Components\TextInput::make('first_name')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                                'Other' => 'Other',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->maxDate('today')
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->placeholder('MD, DR, DPBO etc..')
                            ->maxLength(255),

                    ]),

                Forms\Components\Section::make('Clinics')
                    ->description('Select one or more clinics where the doctor is available for consultation.')
                    ->aside()
                    ->schema([
                        Forms\Components\Select::make('clinic_id')
                            ->relationship('clinics', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),


                Forms\Components\Section::make('Professional Experience')
                    ->description('Provide the doctor\'s educational background, specialization, and years of experience.')
                    ->aside()
                    ->schema([

                        Forms\Components\TextInput::make('education')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('specialization')
                            ->placeholder('Ophthalmology')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TagsInput::make('subspecialty')
                            ->placeholder('Eye Diseases, Vision Correction, Microsurgery of the Eye')
                            ->helperText('Click enter to add new subspecialty.')
                            ->separator(','),

                        Forms\Components\TextInput::make('years_of_experience')
                            ->numeric(),

                        Forms\Components\Textarea::make('profile_description')
                            ->label('Profile Description'),
                        
                    ]),
                    
                Forms\Components\Section::make('Contact Information')
                    ->aside()
                    ->description('Enter the doctor\'s contact details, including email and phone number.')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone_number')
                            ->maxLength(255),

                        Forms\Components\CheckboxList::make('consultation_availability')
                            ->label('Consultation Availability')
                            ->options([
                                'in_person' => 'In-Person',
                                'online' => 'online',
                            ])
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('specialization')
                    ->label('Specialization')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('Yrs of Experience')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('clinics.name')
                    ->sortable()
                    ->searchable()
                    ->badge(),
            ])
            ->filters([
                //
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
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
