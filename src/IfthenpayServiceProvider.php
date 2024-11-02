<?php

namespace Upgradelabs\Ifthenpay;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class IfthenpayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ifthenpay-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ifthenpay_laravel_table');
    }
}
