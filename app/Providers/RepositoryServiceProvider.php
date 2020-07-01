<?php

namespace BeInMedia\Providers;

use BeInMedia\Repositories\AppointmentRepository;
use BeInMedia\Repositories\BaseRepository;
use BeInMedia\Repositories\Eloquent\EloquentAppointmentRepository;
use BeInMedia\Repositories\Eloquent\EloquentBaseRepository;
use BeInMedia\Repositories\Eloquent\EloquentExpertRepository;
use BeInMedia\Repositories\ExpertRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind( BaseRepository::class,EloquentBaseRepository::class);
        $this->app->bind( ExpertRepository::class,EloquentExpertRepository::class);
        $this->app->bind( AppointmentRepository::class,EloquentAppointmentRepository::class);

    }

}
