<?php

namespace TomatoPHP\FilamentAccounts\Filament\Resources;

use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label(trans('Name')),
            Forms\Components\TextInput::make('email')->label(trans('Email')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('email'),
        ]);
    }

    /**
     * @return array<string, PageRegistration|class-string<Page>>
     */
    public static function getPages(): array
    {
        return [];
    }
}
