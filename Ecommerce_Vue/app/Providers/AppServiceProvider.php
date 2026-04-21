<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Tighten\Ziggy\Ziggy as TightenZiggy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Inertia::share([
            'ziggy' => function () {
                return array_merge((new TightenZiggy())->toArray(), [
                    'location' => request()->url(),
                ]);
            },
        ]);
    }
}