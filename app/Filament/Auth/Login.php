<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Actions\Action;
use Filament\Resources\Concerns\HasTabs;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{
    use  HasTabs;


    public function form(Form $form): Form
    {
        if ($this->activeTab == 'rfid') {
            return $form
                ->schema([
                    Forms\Components\TextInput::make('rfid_number')
                    ->label(' ')
                    ->password()
                    ->autofocus()
                    ->helperText('Please scan RFID on the RFID reader to retrieve the RFID #'),
                ]);

        }

        return $form
            ->schema([
                $this->getEmailFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    /**
     * @var view-string
     */
    protected static string $view = 'pages.auth.login';

    public function mount(): void
    {
        $this->activeTab = 'login';

        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public static function resetPage(){}


    protected function getFormActions(): array
    {
        return [
            Action::make('login')
                ->label('Login')
                ->submit('loginRfid')
                ->extraAttributes([
                    'class' => 'w-full' // Tailwind CSS for full-width button
                ]),
        ];
    }


    public function loginRfid()
    {
        // Validate the RFID input
        // $validatedData = $this->form->validate([
        //     'data.rfid_number' => ['required', 'string', 'exists:users,rfid_number'],
        // ]);
        if (empty($this->data['rfid_number'])) {
            $this->throwRfidFailureValidationException();
        }

        $user = User::where('rfid_number', $this->data['rfid_number'])->first();

        if ($this->activeTab == 'rfid' && $user) {
            // Authenticate the user manually without password
            \Auth::login($user);
    
            // Regenerate session for security purposes
            session()->regenerate();
    
            // Redirect to Filament's authenticated dashboard with success message
            return redirect()->intended('/admin')
                ->with('status', 'Login successful!');
        } else {
            $this->throwRfidFailureValidationException();
        }
    }


    protected function throwRfidFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.rfid_number' => 'The provided RFID number is invalid.',
        ]);
    }

}