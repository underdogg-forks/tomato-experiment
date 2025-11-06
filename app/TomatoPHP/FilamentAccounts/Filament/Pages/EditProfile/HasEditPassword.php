<?php

namespace TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

trait HasEditPassword
{
    public array $passwordData = [];

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->password()
                    ->required()
                    ->label(trans('filament-accounts::messages.profile.password.current')),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('password_confirmation')
                    ->label(trans('filament-accounts::messages.profile.password.new')),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->label(trans('filament-accounts::messages.profile.password.confirmation')),
            ])
            ->statePath('passwordData');
    }

    public function updatePassword(): void
    {
        $this->getUser()->forceFill([
            'password' => Hash::make($this->passwordData['password'] ?? null),
        ])->save();

        Notification::make()
            ->title(trans('filament-accounts::messages.profile.password.updated'))
            ->success()
            ->send();
    }

    public function getUpdatePasswordFormActions(): array
    {
        return [];
    }
}
