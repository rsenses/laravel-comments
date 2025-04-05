<?php

declare(strict_types=1);

namespace Rsenses\Comments;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CommentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-comments')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->loadMigrationsFrom([
            __DIR__.'/../database/migrations',
        ]);
    }
}
