<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended;

use App\Actions\Ziggy\GetZiggyRoutes;
use App\Plugins\AbstractPlugin;
use App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers\FixHostedDomainSslController;
use App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers\LatestDeploymentsController;
use App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers\PrimaryDomainsController;
use App\Vito\Plugins\Kjjd84\VitoExtended\Support\PublishesInertiaPages;
use App\Vito\Plugins\Kjjd84\VitoExtended\Support\PublishesSidebarNav;
use Illuminate\Support\Facades\Route;

class Plugin extends AbstractPlugin
{
    protected string $name = 'Vito Extended';

    protected string $description = 'View latest deployments and primary domains across every server and site.';

    public function boot(): void
    {
        app(PublishesInertiaPages::class)->publishIfMissing();
        app(PublishesSidebarNav::class)->publishIfMissing();
        $this->registerRoutes();
    }

    public function enable(): void
    {
        $this->publish();
    }

    public function install(): void
    {
        $this->publish();
    }

    public function uninstall(): void
    {
        app(PublishesInertiaPages::class)->unpublish();
        app(PublishesSidebarNav::class)->unpublish();
        GetZiggyRoutes::forgetCache();
    }

    private function publish(): void
    {
        app(PublishesInertiaPages::class)->publish();
        app(PublishesSidebarNav::class)->publish();
        GetZiggyRoutes::forgetCache();
    }

    private function registerRoutes(): void
    {
        Route::middleware(['web', 'auth', 'has-project'])
            ->prefix('vito-extended')
            ->group(function (): void {
                Route::get('/latest-deployments', [LatestDeploymentsController::class, 'index'])
                    ->name('kjjd84-vito-extended.latest-deployments');
                Route::get('/primary-domains', [PrimaryDomainsController::class, 'index'])
                    ->name('kjjd84-vito-extended.primary-domains');
                Route::post('/primary-domains/{hostedDomain}/fix-ssl', FixHostedDomainSslController::class)
                    ->name('kjjd84-vito-extended.primary-domains.fix-ssl');
            });
    }
}
