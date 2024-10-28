<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\InvoiceResource\Widgets\InvoiceStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentTypes\Models\Type;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;


    public function mount(): void
    {
        parent::mount();

        // FilamentInvoices::loadTypes();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InvoiceStatsWidget::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\Action::make('setting')
            //     ->hiddenLabel()
            //     ->tooltip(trans('messages.invoices.actions.invoices_status'))
            //     ->icon('heroicon-o-cog')
            //     ->color('info')
            //     ->action(function (){
            //         return redirect()->to(InvoiceStatus::getUrl());
            //     })
            //     ->label(trans('messages.invoices.actions.invoices_status')),
        ];
    }
}
