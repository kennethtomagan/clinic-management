<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use TomatoPHP\FilamentEcommerce\Filament\Pages;
use TomatoPHP\FilamentEcommerce\Settings\OrderingSettings;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentSettingsHub\Settings\SitesSettings;
use TomatoPHP\FilamentTypes\Components\TypeColumn;
use TomatoPHP\FilamentTypes\Models\Type;

class InvoiceStatus extends Page implements HasTable
{
    use InteractsWithTable;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected ?string $status = null;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = "settings.status";

    public array $data = [];

    public function mount(): void
    {
        // FilamentInvoices::loadTypes();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->action(fn()=> redirect()->to(InvoiceResource::getUrl('index')))
                ->color('danger')
                ->label(trans('filament-settings-hub::messages.back')),
        ];
    }

    public function getTitle(): string
    {
        return trans('messages.settings.status.title');
    }

    public function table(Table $table): Table
    {
        $localsTitle = [];
        foreach (config('filament-menus.locals') as $key=>$local){
            $localsTitle[] = TextInput::make($key)
                ->label($local[app()->getLocale()])
                ->required();
        }

        return $table->query(Type::query()->where('for', 'invoices'))
            ->paginated(false)
            ->columns([
                TypeColumn::make('key')
                    ->label(trans('messages.settings.status.columns.status'))
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('edit')
                    ->label(trans('messages.settings.status.action.edit'))
                    ->tooltip(trans('messages.settings.status.action.edit'))
                    ->form([
                        KeyValue::make('name')
                            ->schema($localsTitle)
                            ->keyLabel(trans('messages.settings.status.columns.language'))
                            ->editableKeys(false)
                            ->addable(false)
                            ->deletable(false)
                            ->label(trans('messages.settings.status.columns.value')),
                        IconPicker::make('icon')->label(trans('messages.settings.status.columns.icon')),
                        ColorPicker::make('color')->label(trans('messages.settings.status.columns.color')),
                    ])
                    ->fillForm(fn(Type $record) => $record->toArray())
                    ->icon('heroicon-s-pencil-square')
                    ->iconButton()
                    ->action(function (array $data, Type $type){
                        $type->update($data);
                        Notification::make()
                            ->title(trans('messages.settings.status.action.notification'))
                            ->success()
                            ->send();
                    })
            ]);
    }
}
