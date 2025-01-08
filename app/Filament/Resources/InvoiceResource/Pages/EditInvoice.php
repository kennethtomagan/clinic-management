<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\PatientRfidPoint;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            Actions\ViewAction::make()->icon('heroicon-o-eye'),
        ];
    }

    public function afterSave()
    {
        $data = [];
        // $data['discount'] = collect($this->getRecord()->invoicesItems)->sum(function ($item) {
        //     return $item->discount * $item->qty;
        // });
        $data['vat'] = collect($this->getRecord()->invoicesItems)->sum(function ($item) {
            return $item->vat * $item->qty;
        });
        // $data['total'] = collect($this->getRecord()->invoicesItems)->sum('total');

        if (!empty($this->data['use_rfid_discount']) && $this->data['use_rfid_discount'] == true && !empty($this->data['rfid_number'])) {
            $rfidPoints = PatientRfidPoint::where('rfid_number', $this->data['rfid_number'])->delete();
        }

        $this->getRecord()->update($data);

        $this->getRecord()->invoiceLogs()->create([
            'log' => "Invoice Updated By: " . auth()->user()->name,
            'type' => 'updated'
        ]);
    }
}
