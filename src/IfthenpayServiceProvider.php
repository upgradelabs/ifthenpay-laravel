<?php

namespace Upgradelabs\Ifthenpay;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class IfthenpayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ifthenpay-laravel')
            ->hasConfigFile()
            ->hasMigrations([
                'create_mbway_payment_requests_table',
                'create_mbway_payment_statuses_table',
                'create_mbway_payment_refunds_table',
            ]);
    }
}
