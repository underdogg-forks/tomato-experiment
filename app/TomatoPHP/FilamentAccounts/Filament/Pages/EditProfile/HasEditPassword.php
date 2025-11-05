<?php

namespace TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

trait HasEditPassword
{
    public ?array $passwordData = [];

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->password()
                    ->label(__('filament-accounts::messages.profile.password.current')),
                TextInput::make('password')
                    ->password()
                    ->same('password_confirmation')
                    ->label(__('filament-accounts::messages.profile.password.new')),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label(__('filament-accounts::messages.profile.password.confirmation')),
            ])
            ->statePath('passwordData');
    }

    public function updatePassword(): void
    {
        $this->getUser()->forceFill([
            'password' => Hash::make($this->passwordData['password'] ?? null),
        ])->save();

        Notification::make()
            ->title(__('filament-accounts::messages.profile.password.updated'))
            ->success()
            ->send();
    }

    public function getUpdatePasswordFormActions(): array
    {
        return [];
    }
}
