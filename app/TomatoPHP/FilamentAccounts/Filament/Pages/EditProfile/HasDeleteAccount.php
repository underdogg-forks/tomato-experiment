<?php

namespace TomatoPHP\FilamentAccounts\Filament\Pages\EditProfile;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

trait HasDeleteAccount
{
    public ?array $deleteAccountData = [];

    public function deleteAccountForm(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('reason')
                    ->label(trans('filament-accounts::messages.profile.delete_account.reason'))
                    ->rows(3),
            ])
            ->statePath('deleteAccountData');
    }

    public function deleteAccount(): void
    {
        Notification::make()
            ->title(trans('filament-accounts::messages.profile.delete_account.disabled'))
            ->warning()
            ->send();
    }

    public function getDeleteAccountFormActions(): array
    {
        return [];
    }
}
