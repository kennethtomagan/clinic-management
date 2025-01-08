<x-filament-panels::page.simple>

    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'login'"
            wire:click="$set('activeTab', 'login')"
        >
            <x-heroicon-o-key class="w-5 h-5 inline-block mr-2"/> 
            Login
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$activeTab === 'rfid'"
            wire:click="$set('activeTab', 'rfid')"
        >
            <x-heroicon-o-credit-card class="w-5 h-5 inline-block mr-2"/>
            RFID
        </x-filament::tabs.item>
    </x-filament::tabs>


    @if($activeTab === 'login')
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    @elseif($activeTab === 'rfid')
        <x-filament-panels::form id="form" wire:submit="loginRfid"> 
            {{ $this->form }}

            <x-filament-panels::form.actions 
                :actions="$this->getFormActions()"
            /> 
        </x-filament-panels::form>
    @endif
</x-filament-panels::page.simple>
