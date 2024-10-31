<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\PatientRfidPoint;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentTypes\Models\Type;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;


    protected static string $view = 'pages.view-invoice';


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            Actions\Action::make('print')
                ->label(trans('messages.invoices.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (){
                    $this->js('window.print()');
                }),

            Actions\Action::make('pay')
                ->hidden(fn($record) => ($record->total === $record->paid) || $record->status === 'paid' || $record->status === 'estimate')
                ->requiresConfirmation()
                // ->iconButton()
                ->color('success')
                ->fillForm(fn($record) => [
                    'total' => $record->total,
                    'paid' => $record->paid,
                    'amount' => $record->total - $record->paid,
                ])
                ->form([
                    Forms\Components\TextInput::make('total')
                        ->label(trans('messages.invoices.actions.total'))
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('paid')
                        ->label(trans('messages.invoices.actions.paid'))
                        ->numeric()
                        ->disabled(),
                    Forms\Components\TextInput::make('amount')
                        ->label(trans('messages.invoices.actions.amount'))
                        ->required()
                        ->numeric(),

                    Forms\Components\Toggle::make('use_rfid')
                        ->label('Retrieve points?')
                        ->reactive(),

                    Forms\Components\TextInput::make('points_rfid_number')
                        ->label('RFID number')
                        ->extraAttributes([
                            'onkeydown' => "if(event.key === 'Enter'){ event.preventDefault(); }"
                        ])
                        ->visible(fn ($get) => $get('use_rfid')),
                ])
                ->action(function (array $data, Invoice $record) {

                    $record->rfidPoints()->create([
                        'user_id' => $record->for_id,
                        'rfid_number' => $data['points_rfid_number'], 
                        'points' =>  $data['amount'] / 100,
                        'status' => PatientRfidPoint::STATUS_ACTIVE
                    ]);

                    $record->update([
                        'paid' => $record->paid + $data['amount']
                    ]);

                    $record->invoiceMetas()->create([
                        'key' => 'payments',
                        'value' => $data['amount']
                    ]);

                    $record->invoiceLogs()->create([
                        'log' => 'Paid ' . number_format($data['amount'], 2) . ' ' . $record->currency->iso . ' By: ' . auth()->user()->name,
                        'type' => 'payment',
                    ]);

                    if ($record->total === $record->paid) {
                        $record->update([
                            'status' => 'paid'
                        ]);
                    }

                    Notification::make()
                        ->title(trans('messages.invoices.actions.pay.notification.title'))
                        ->body(trans('messages.invoices.actions.pay.notification.body'))
                        ->success()
                        ->send();
                })
                ->icon('heroicon-s-credit-card')
                ->label("Pay")
                ->modalHeading(trans('messages.invoices.actions.pay.label'))
                ->tooltip(trans('messages.invoices.actions.pay.label')),
        ];
    }
}
