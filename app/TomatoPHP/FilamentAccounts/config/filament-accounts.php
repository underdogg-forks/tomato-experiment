<?php

use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Actions\AccountsTableActions;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Filters\AccountsFilters;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Forms\AccountsForm;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Pages\AccountPagesList;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Releations\AccountReleations;
use TomatoPHP\FilamentAccounts\Filament\Resources\AccountResource\Tables\AccountsTable;
use TomatoPHP\FilamentAccounts\Filament\Resources\TeamResource;
use TomatoPHP\FilamentAccounts\Models\Team;
use TomatoPHP\FilamentAccounts\Models\TeamInvitation;
use TomatoPHP\FilamentAccounts\Models\Membership;

return [
    'features' => [
        'accounts' => true,
        'meta' => true,
        'locations' => true,
        'contacts' => true,
        'requests' => true,
        'notifications' => true,
        'loginBy' => true,
        'avatar' => true,
        'types' => false,
        'teams' => false,
        'apis' => true,
        'send_otp' => true,
        'impersonate' => [
            'active' => true,
            'redirect' => '/app',
        ],
    ],
    'resource' => AccountResource::class,
    'login_by' => 'email',
    'required_otp' => true,
    'model' => \App\Models\Account::class,
    'guard' => 'accounts',
    'relations' => AccountReleations::class,
    'accounts' => [
        'form' => AccountsForm::class,
        'table' => AccountsTable::class,
        'actions' => AccountsTableActions::class,
        'filters' => AccountsFilters::class,
        'pages' => AccountPagesList::class,
    ],
    'teams' => [
        'allowed' => false,
        'model' => Team::class,
        'invitation' => TeamInvitation::class,
        'membership' => Membership::class,
        'resource' => TeamResource::class,
    ],
];
