<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFor;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFrom;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentInvoices::registerFor([
            InvoiceFor::make(Patient::class)
                ->label('Patient')
                ->column('name')
        ]);
        FilamentInvoices::registerFrom([
            InvoiceFrom::make(Clinic::class)
                ->label('Clinic')
                ->column('name'),
            InvoiceFrom::make(Doctor::class)
                ->label('Doctor')
                ->column('name'),
        ]);
    }
}
