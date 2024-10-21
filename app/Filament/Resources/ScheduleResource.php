<?php

namespace App\Filament\Resources;

use App\Enums\DaysOfTheWeek;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Slot;
use App\Models\User;
use App\Support\AvatarOptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('clinic_id')
                        ->relationship('clinic', 'name')
                        ->preload()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('doctor_id', null)),
                    Forms\Components\Select::make('doctor_id')
                        // ->native(false)

                        ->allowHtml()
                        ->searchable()
                        ->label('Doctor')
                        ->getSearchResultsUsing(function (string $search) {
                            $patient = User::where('type', User::DOCTOR_TYPE)
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
                            $patients = User::where('type', User::DOCTOR_TYPE)->get();

                            return $patients->mapWithKeys(function ($patient) {
                                return [$patient->getKey() => AvatarOptions::getOptionString($patient)];
                            })->toArray();
                        })
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('day_of_week')
                        ->options(DaysOfTheWeek::class)
                        ->native(false)
                        ->required(),
                    Forms\Components\Repeater::make('slots')
                        ->label('Available slot (1 per Appointment)')
                        ->helperText('Please define the start and end times for each slot.')
                        ->createItemButtonLabel('Add more slot')
                        ->relationship()
                        ->schema([
                            Forms\Components\TimePicker::make('start')
                                ->seconds(false)
                                ->required(),
                            Forms\Components\TimePicker::make('end')
                                ->seconds(false)
                                ->required()
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->defaultGroup(
            //     Tables\Grouping\Group::make('clinic.name')
            //         ->collapsible()
            //         ->titlePrefixedWithLabel(false)
            // )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('clinic.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slots')
                    ->badge()
                    ->formatStateUsing(fn (Slot $state) => $state->start->format('h:i A') . ' - ' . $state->end->format('h:i A')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Schedule $record) => $record->slots()->delete())
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
