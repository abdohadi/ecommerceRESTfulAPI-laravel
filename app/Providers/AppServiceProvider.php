<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        User::created(function($user) {
            retry(5, function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        User::updated(function($user) {
            if ($user->isDirty('email')) {
                retry(5, function() use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });

        // Trigger this event when a product gets updated
        Product::updated(function($product) {
            // Make the product unavailable if the quantity is 0
            if ($product->quantity == 0 && $product::UNAVAILABLE_PRODUCT) {
                $product->status = 'unavailable';
                $product->save();
            }
        });
    }
}
