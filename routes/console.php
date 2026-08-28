<?php

use App\Console\Commands\ReconcileCardPayments;
use App\Console\Commands\ReconcileNexoPosCards;
use App\Console\Commands\SendBookingReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(SendBookingReminders::class)
    ->everyMinute()
    ->withoutOverlapping();

// Safety net for lost/late PlutoPay webhooks — reconcile stuck card sales.
Schedule::command(ReconcileCardPayments::class)
    ->everyMinute()
    ->withoutOverlapping();

// Safety net for the tablet POS: settle card sales whose webhook never landed
// (host outage, rotated signing secret, provider-disabled endpoint).
Schedule::command(ReconcileNexoPosCards::class)
    ->everyMinute()
    ->withoutOverlapping();
