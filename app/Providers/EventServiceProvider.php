<?php

namespace App\Providers;

use App\Events\LeadStatusChanged;
use App\Events\NewLeadCreated;
use App\Listeners\SendNewNotification;
use App\Listeners\SendStatusChangeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
//    protected static bool $shouldDiscoverEvents = false;

    protected $listen = [
        NewLeadCreated::class => [SendNewNotification::class],
        LeadStatusChanged::class => [SendStatusChangeNotification::class],
    ];
}
