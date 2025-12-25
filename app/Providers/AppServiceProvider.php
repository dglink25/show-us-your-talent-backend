<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void{
        
        if (env('APP_ENV') !== 'local') {
            // Désactiver l'encapsulation des données dans "data"
            JsonResource::withoutWrapping();
        
            // Configuration du format des réponses
            $this->app->bind('Illuminate\Routing\ResponseFactory', function ($app) {
                return new \Illuminate\Routing\ResponseFactory(
                    $app['Illuminate\Contracts\View\Factory'],
                    $app['Illuminate\Routing\Redirector']
                );
            });
            URL::forceScheme('https');
        }
    }

}