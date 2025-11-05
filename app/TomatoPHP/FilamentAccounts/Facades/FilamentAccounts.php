<?php

namespace TomatoPHP\FilamentAccounts\Facades;

use Illuminate\Support\Facades\Facade;
use TomatoPHP\FilamentAccounts\FilamentAccountsManager;

/**
 * @method static array features()
 * @method static array configuration()
 * @method static void  update(array $configuration)
 */
class FilamentAccounts extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilamentAccountsManager::class;
    }
}
