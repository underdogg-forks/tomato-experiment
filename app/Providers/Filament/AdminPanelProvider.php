<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AppDashboard;
use App\Filament\Resources\TenantResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentCms\FilamentCMSPlugin;
use TomatoPHP\FilamentDocs\FilamentDocsPlugin;
use TomatoPHP\FilamentFcm\FilamentFcmPlugin;
use TomatoPHP\FilamentIssues\FilamentIssuesPlugin;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use TomatoPHP\FilamentLogger\FilamentLoggerPlugin;
use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;
use TomatoPHP\FilamentMenus\FilamentMenusPlugin;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentPWA\FilamentPWAPlugin;
use TomatoPHP\FilamentSeo\FilamentSeoPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentTenancy\FilamentTenancyPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class AdminPanelProvider extends PanelProvider
{
    /**
     * Configurable menu items for the admin panel.
     * Set to false to hide specific menu items.
     */
    protected array $menuConfig = [
        'dashboard'    => true,
        'tenants'      => true,
        'users'        => true,
        'accounts'     => true,
        'cms'          => true,
        'media'        => true,
        'settings'     => true,
        'types'        => true,
        'menus'        => true,
        'translations' => true,
        'alerts'       => true,
        'notes'        => true,
        'issues'       => true,
        'docs'         => true,
        'logger'       => true,
        'seo'          => true,
        'shield'       => true,
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->colors([
                'danger'  => Color::Red,
                'gray'    => Color::Slate,
                'info'    => Color::Blue,
                'primary' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('favicon.ico'))
            ->brandName('TomatoPHP')
            ->brandLogo(asset('tomato.png'))
            ->brandLogoHeight('80px')
            ->font(
                'Inter',
                provider: GoogleFontProvider::class,
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AppDashboard::class,
            ])
            ->globalSearchFieldKeyBindingSuffix()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            ])
            ->plugin(
                FilamentTenancyPlugin::make()->allowImpersonate()->panel('app')
            )
            ->plugins([
                FilamentTypesPlugin::make(),
                FilamentMenusPlugin::make(),
                FilamentLanguageSwitcherPlugin::make(),
                FilamentUsersPlugin::make(),
                FilamentShieldPlugin::make(),
                FilamentFcmPlugin::make(),
                FilamentPWAPlugin::make(),
            ])
            ->plugin(
                FilamentSettingsHubPlugin::make()
                    ->allowShield(),
            )
            ->plugin(
                FilamentMediaManagerPlugin::make()
                    ->allowUserAccess()
                    ->allowSubFolders(),
            )
            ->plugin(
                FilamentTranslationsPlugin::make()
                    ->allowCreate(),
            )
            ->plugin(
                FilamentAlertsPlugin::make()
                    ->models([
                        \App\Models\User::class    => 'Admins',
                        \App\Models\Account::class => 'Accounts',
                    ])
                    ->useDiscord()
                    ->useDatabase()
                    ->useSettingsHub(),
            )
            ->plugin(
                FilamentCMSPlugin::make()
                    ->defaultLocales(['ar', 'en'])
                    ->useThemeManager()
                    ->usePageBuilder()
                    ->useFormBuilder(),
            )
            ->plugin(
                FilamentNotesPlugin::make()
                    ->useStatus()
                    ->useGroups()
                    ->useUserAccess()
                    ->useCheckList()
                    ->useShareLink()
                    ->useNotification(),
            )
            ->plugin(
                FilamentAccountsPlugin::make()
            )
            ->plugin(
                FilamentLoggerPlugin::make()
            )
            ->plugin(
                FilamentSeoPlugin::make()
            )
            ->plugin(
                FilamentIssuesPlugin::make()
            )
            ->plugin(
                FilamentDocsPlugin::make()
            )
            ->navigation(function (NavigationBuilder $builder) {
                return $this->buildNavigation($builder);
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function buildNavigation(NavigationBuilder $builder): NavigationBuilder
    {
        $items  = [];
        $groups = [];

        // Dashboard
        if ($this->menuConfig['dashboard'] ?? true) {
            $items[] = NavigationItem::make('Dashboard')
                ->icon('heroicon-o-home')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.app-dashboard'))
                ->url(fn (): string => AppDashboard::getUrl());
        }

        // Tenant Management Group
        $tenantItems = [];
        if ($this->menuConfig['tenants'] ?? true) {
            $tenantItems[] = NavigationItem::make('Tenants')
                ->icon('heroicon-o-building-office')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.tenants.*'))
                ->url(fn (): string => TenantResource::getUrl());
        }

        if ( ! empty($tenantItems)) {
            $groups[] = NavigationGroup::make('Tenant Management')
                ->items($tenantItems);
        }

        // User Management Group
        $userItems = [];

        if ($this->menuConfig['users'] ?? true) {
            // Users resource from FilamentUsersPlugin
            $userItems[] = NavigationItem::make('Users')
                ->icon('heroicon-o-users')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.users.*'))
                ->url('/admin/users')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentUsers\Resources\UserResource::class));
        }

        if ($this->menuConfig['accounts'] ?? true) {
            // Accounts resource from FilamentAccountsPlugin
            $userItems[] = NavigationItem::make('Accounts')
                ->icon('heroicon-o-user-group')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.accounts.*'))
                ->url('/admin/accounts')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentAccounts\Resources\AccountResource::class));
        }

        if ($this->menuConfig['shield'] ?? true) {
            $userItems[] = NavigationItem::make('Roles')
                ->icon('heroicon-o-shield-check')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.shield.roles.*'))
                ->url('/admin/shield/roles')
                ->visible(fn (): bool => class_exists(\BezhanSalleh\FilamentShield\Resources\RoleResource::class));
        }

        if ( ! empty($userItems)) {
            $groups[] = NavigationGroup::make('User Management')
                ->items($userItems);
        }

        // Content Management Group
        $contentItems = [];

        if ($this->menuConfig['cms'] ?? true) {
            $contentItems[] = NavigationItem::make('Posts')
                ->icon('heroicon-o-document-text')
                ->url('/admin/posts')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentCms\Resources\PostResource::class));
        }

        if ($this->menuConfig['media'] ?? true) {
            $contentItems[] = NavigationItem::make('Media')
                ->icon('heroicon-o-photo')
                ->url('/admin/media')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin::class));
        }

        if ($this->menuConfig['menus'] ?? true) {
            $contentItems[] = NavigationItem::make('Menus')
                ->icon('heroicon-o-bars-3')
                ->url('/admin/menus')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentMenus\Resources\MenuResource::class));
        }

        if ( ! empty($contentItems)) {
            $groups[] = NavigationGroup::make('Content')
                ->items($contentItems);
        }

        // System Group
        $systemItems = [];

        if ($this->menuConfig['types'] ?? true) {
            $systemItems[] = NavigationItem::make('Types')
                ->icon('heroicon-o-tag')
                ->url('/admin/types')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentTypes\Resources\TypeResource::class));
        }

        if ($this->menuConfig['translations'] ?? true) {
            $systemItems[] = NavigationItem::make('Translations')
                ->icon('heroicon-o-language')
                ->url('/admin/translations')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentTranslations\Resources\TranslationResource::class));
        }

        if ($this->menuConfig['alerts'] ?? true) {
            $systemItems[] = NavigationItem::make('Alerts')
                ->icon('heroicon-o-bell')
                ->url('/admin/alerts')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentAlerts\FilamentAlertsPlugin::class));
        }

        if ($this->menuConfig['notes'] ?? true) {
            $systemItems[] = NavigationItem::make('Notes')
                ->icon('heroicon-o-document')
                ->url('/admin/notes')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentNotes\Resources\NoteResource::class));
        }

        if ($this->menuConfig['issues'] ?? true) {
            $systemItems[] = NavigationItem::make('Issues')
                ->icon('heroicon-o-exclamation-triangle')
                ->url('/admin/issues')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentIssues\Resources\IssueResource::class));
        }

        if ($this->menuConfig['docs'] ?? true) {
            $systemItems[] = NavigationItem::make('Docs')
                ->icon('heroicon-o-book-open')
                ->url('/admin/docs')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentDocs\Resources\DocResource::class));
        }

        if ($this->menuConfig['logger'] ?? true) {
            $systemItems[] = NavigationItem::make('Logger')
                ->icon('heroicon-o-clipboard-document-list')
                ->url('/admin/logs')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentLogger\Resources\ActivityResource::class));
        }

        if ($this->menuConfig['seo'] ?? true) {
            $systemItems[] = NavigationItem::make('SEO')
                ->icon('heroicon-o-magnifying-glass')
                ->url('/admin/seo')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentSeo\Resources\SeoResource::class));
        }

        if ( ! empty($systemItems)) {
            $groups[] = NavigationGroup::make('System')
                ->items($systemItems);
        }

        // Settings Group
        $settingsItems = [];

        if ($this->menuConfig['settings'] ?? true) {
            $settingsItems[] = NavigationItem::make('Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->url('/admin/settings')
                ->visible(fn (): bool => class_exists(\TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin::class));
        }

        if ( ! empty($settingsItems)) {
            $groups[] = NavigationGroup::make('Settings')
                ->items($settingsItems);
        }

        // Build final navigation
        return $builder->items($items)->groups($groups);
    }
}
