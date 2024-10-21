<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Slot;
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
use Illuminate\Support\Str;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('appointment_id')
                        ->label('Appointment ID')
                        ->default(fn() => 'APT-' . Str::random(8))
                        ->required(),
                    Forms\Components\Select::make('patient_id')
                        ->label('Patient')
                        ->allowHtml()
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
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
                    Forms\Components\Select::make('clinic_id')
                        ->relationship('clinic', 'name')
                        ->preload()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('date', null);
                            $set('doctor', null);
                        }),
                    Forms\Components\DatePicker::make('date')
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->closeOnDateSelection()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('doctor_id', null)),
                    Forms\Components\Select::make('doctor_id')
                        ->label('Doctor')
                        ->allowHtml()
                        ->options(function (Get $get) {
                            $doctors = User::where('type', User::DOCTOR_TYPE)
                                ->whereHas('schedules', function (Builder $query) use ($get) {
                                    $dayOfTheWeek = Carbon::parse($get('date'))->dayOfWeek;
                                    $query
                                        ->where('clinic_id', $get('clinic_id'))
                                        ->where('day_of_week', $dayOfTheWeek);

                                })
                                ->get();
                            return $doctors->mapWithKeys(function ($doctor) {
                                return [$doctor->getKey() => AvatarOptions::getOptionString($doctor)];
                            })->toArray();
                        })
                        ->native(false)
                        ->hidden(fn (Get $get) => blank($get('date')))
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('slot_id', null))
                        ->helperText(function ($component) {
                            if (! $component->getOptions()) {
                                return new HtmlString(
                                    '<span class="text-sm text-danger-600 dark:text-danger-400">No Doctors available. Please select a different clinic or date</span>'
                                );
                            }

                            return '';
                        }),
                    Forms\Components\Select::make('slot_id')
                        ->native(false)
                        ->label('Slot')
                        ->required()
                        ->options(function (Get $get) {
                            $doctor = User::find($get('doctor_id'));
                            $dayOfTheWeek = Carbon::parse($get('date'))->dayOfWeek;
                            $clinicId = $get('clinic_id');
                            
                            return $clinicId ? Slot::availableFor($doctor, $dayOfTheWeek, $clinicId)->get()->pluck('formatted_time', 'id') : [];
                        })
                        ->hidden(fn (Get $get) => blank($get('doctor_id')))
                        ->getOptionLabelFromRecordUsing(fn (Slot $record) => $record->formatted_time)
                        ->helperText(function ($component) {
                            if (! $component->getOptions()) {
                                return new HtmlString(
                                    '<span class="text-sm text-danger-600 dark:text-danger-400">No time slots available. Please select a different clinic, date or doctor</span>'
                                );
                            }

                            return '';
                        })
                        ->rule(function (Get $get) {
                            return [
                                'required',
                                function (string $attribute, $value, $fail) use ($get) {
                                    $date = $get('date');
                                    $slotId = $value;
                                    
                                    // Check if there's an existing appointment with the same date and slot_id
                                    $exists = Appointment::where('date', $date)
                                        ->where('slot_id', $slotId)
                                        ->exists();

                                    if ($exists) {
                                        $fail('The selected slot is already booked for the chosen date.');
                                    }
                                },
                            ];
                        }),
                    Forms\Components\TextInput::make('description')
                        ->label('Reason for booking')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->native(false)
                        ->options(AppointmentStatus::class)
                        ->visibleOn(Pages\EditAppointment::class),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('patient.avatar_url')
                    ->label(' ')
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
                Tables\Columns\TextColumn::make('date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot.formatted_time')
                    ->label('Time')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Confirm')
                    ->action(function (Appointment $record) {
                        $record->status = AppointmentStatus::Confirmed;
                        $record->save();
                    })
                    ->visible(fn (Appointment $record) => $record->status == AppointmentStatus::Pending)
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Tables\Actions\Action::make('Cancel')
                    ->action(function (Appointment $record) {
                        $record->status = AppointmentStatus::Canceled;
                        $record->save();
                    })
                    ->visible(fn (Appointment $record) => $record->status != AppointmentStatus::Canceled)
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
