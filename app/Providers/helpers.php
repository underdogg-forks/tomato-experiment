<?php

use TomatoPHP\FilamentCms\Models\Post;

if ( ! function_exists('appTitle')) {
    function appTitle(?string $title = null): string
    {
        return ($title ? $title . ' | ' : null) . (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0] ?? setting('site_name') : str(setting('site_name'))->explode('|')[1] ?? setting('site_name'));
    }
}

if ( ! function_exists('appDescription')) {
    function appDescription(?string $description = null): string
    {
        return ($description ? $description . ' | ' : null) . (app()->getLocale() === 'en' ? str(setting('site_description'))->explode('|')[0] ?? setting('site_description') : str(setting('site_description'))->explode('|')[1] ?? setting('site_description'));
    }
}

if ( ! function_exists('appKeywords')) {
    function appKeywords(?string $keywords = null): string
    {
        return ($keywords ? $keywords . ' | ' : null) . (app()->getLocale() === 'en' ? str(setting('site_keywords'))->explode('|')[0] ?? setting('site_keywords') : str(setting('site_keywords'))->explode('|')[1] ?? setting('site_keywords'));
    }
}

if ( ! function_exists('load_page')) {
    function load_page(string $slug)
    {
        return Post::where('slug', $slug)->firstOrFail();
    }
}

if ( ! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        // Example: fetch from config or database
        return config('settings.' . $key, $default);
    }
}
