<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Tenant;
use App\Observers\TenantObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\SyncedResourceChangedInForeignDatabase;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Stancl\Tenancy\Events\TenancyEnded;
use TomatoPHP\FilamentCms\Events\PostCreated;
use TomatoPHP\FilamentCms\Events\PostDeleted;
use TomatoPHP\FilamentCms\Events\PostUpdated;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFor;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFrom;
use TomatoPHP\FilamentSeo\Jobs\GoogleIndexURLJob;
use TomatoPHP\FilamentSeo\Jobs\GoogleRemoveIndexURLJob;
use TomatoPHP\FilamentTypes\Models\Type;
use Ymigval\LaravelIndexnow\IndexNowService;

require_once __DIR__ . '/helpers.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Tenant::observe(TenantObserver::class);

        //URL::forceScheme('https');

        Event::listen(SyncedResourceChangedInForeignDatabase::class, function ($data) {
            config(['database.connections.dynamic.database' => $data->tenant->tenancy_db_name]);
            DB::connection('dynamic')
                ->table('users')
                ->where('email', $data->model->email)
                ->update([
                    'name'     => $data->model->name,
                    'email'    => $data->model->email,
                    'packages' => json_encode($data->model->packages),
                    'password' => $data->model->password,
                ]);
        });

        Event::listen(TenancyBootstrapped::class, function ($event) {
            $permissionRegistrar           = app(\Spatie\Permission\PermissionRegistrar::class);
            $permissionRegistrar->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->getTenantKey();
        });

        Event::listen(TenancyEnded::class, function ($event) {
            $permissionRegistrar           = app(\Spatie\Permission\PermissionRegistrar::class);
            $permissionRegistrar->cacheKey = 'spatie.permission.cache';
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_AFTER,
            fn (): string => Blade::render('@livewire(\'quick-menu\')')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn (): string => view('layouts.js')->render()
        );

        FilamentInvoices::registerFor([
            InvoiceFor::make(Account::class)
                ->label('For Account'),
        ]);

        FilamentInvoices::registerFrom([
            InvoiceFrom::make(Account::class)
                ->label('From Account'),
        ]);

        RateLimiter::for('twitter', function ($job) {
            return Limit::perHour(1);
        });

        //FilamentIssues::register(fn() => Post::query()->where('type', 'open-source')->pluck('meta_url')->map(fn($item) => str($item)->remove('https://github.com/')->remove('https://www.github.com/')->toString())->toArray());

        Event::listen(PostCreated::class, function ($event) {
            $post = Post::query()->find($event->data['id']);

            $urlAr = url('/ar' . ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);
            $urlEn = url('/en' . ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            $links = [
                str($urlAr),
                str($urlEn),
            ];

            dispatch(new GoogleIndexURLJob(
                url: $urlAr,
            ));

            dispatch(new GoogleIndexURLJob(
                url: $urlEn,
            ));

            $indexNow = new IndexNowService('indexnow');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('microsoft_bing');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('naver');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('seznam');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('yandex');
            $indexNow->submit($links);
        });

        Event::listen(PostUpdated::class, function ($event) {
            $post = Post::query()->find($event->data['id']);

            $urlAr = url('/ar' . ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);
            $urlEn = url('/en' . ($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            $links = [
                str($urlAr),
                str($urlEn),
            ];

            dispatch(new GoogleIndexURLJob(
                url: $urlAr,
            ));

            dispatch(new GoogleIndexURLJob(
                url: $urlEn,
            ));

            $indexNow = new IndexNowService('indexnow');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('microsoft_bing');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('naver');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('seznam');
            $indexNow->submit($links);

            $indexNow = new IndexNowService('yandex');
            $indexNow->submit($links);
        });

        Event::listen(PostDeleted::class, function ($event) {
            $post = Post::query()->find($event->data['id']);

            $url = url(($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            dispatch(new GoogleRemoveIndexURLJob(
                url: $url,
            ));
        });
    }
}
