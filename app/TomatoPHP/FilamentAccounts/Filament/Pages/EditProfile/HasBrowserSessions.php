<?php

namespace TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

trait HasBrowserSessions
{
    public array $browserSessionsData = [];

    public function browserSessionsForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('current_password')
                ->password()
                ->label(trans('filament-accounts::messages.profile.browser_sessions.password')),
        ])->statePath('browserSessionsData');
    }

    public function terminateOtherSessions(): void
    {
        $password = $this->browserSessionsData['current_password'] ?? null;
        $user     = $this->getUser();
        if ( ! $user || ! $password || ! Hash::check($password, $user->password)) {
            Notification::make()
                ->title(trans('filament-accounts::messages.profile.browser_sessions.invalid_password'))
                ->danger()
                ->send();

            return;
        }
        // Invalidate other sessions
        auth('accounts')->logoutOtherDevices($password);
        $this->browserSessionsData = [];
        Notification::make()
            ->title(trans('filament-accounts::messages.profile.browser_sessions.terminated'))
            ->success()
            ->send();
    }

    public function getBrowserSessionsFormActions(): array
    {
        return [
            Action::make('terminateOtherSessions')
                ->label(trans('filament-accounts::messages.profile.browser_sessions.terminate'))
                ->submit('terminateOtherSessions')
                ->requiresConfirmation(),
        ];
    }
}
