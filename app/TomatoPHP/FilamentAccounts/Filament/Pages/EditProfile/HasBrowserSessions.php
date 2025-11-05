<?php

namespace TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

trait HasBrowserSessions
{
    public ?array $browserSessionsData = [];

    public function browserSessionsForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('current_password')
                ->password()
                ->label(__('filament-accounts::messages.profile.browser_sessions.password')),
        ])->statePath('browserSessionsData');
    }

    public function terminateOtherSessions(): void
    {
        $this->browserSessionsData = [];
    }

    public function getBrowserSessionsFormActions(): array
    {
        return [];
    }
}
