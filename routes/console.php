<?php

use App\Jobs\SendPing;
use Illuminate\Support\Facades\Schedule;

Schedule::command('repos:preload')->hourly();
Schedule::command('repos:crawlable')->weekly();
//Schedule::job(new SendPing(config('services.ohdear.ping_url')))->everyMinute();
