<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers;

use App\Actions\HostedDomain\ActivateHostedDomain;
use App\Actions\SSL\CheckSslExpiry;
use App\Actions\SSL\RenewSiteSsl;
use App\Enums\HostedDomainStatus;
use App\Enums\SslMethod;
use App\Enums\SslStatus;
use App\Http\Controllers\Controller;
use App\Jobs\HostedDomain\SetupHostedDomainSslJob;
use App\Models\HostedDomain;
use App\Vito\Plugins\Kjjd84\VitoExtended\Support\PrimaryDomainSslFix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class FixHostedDomainSslController extends Controller
{
    public function __invoke(HostedDomain $hostedDomain): RedirectResponse
    {
        $hostedDomain->load('site.server', 'ssl');

        $site = $hostedDomain->site;
        $server = $site->server;

        $this->authorize('update', [$hostedDomain, $site, $server]);

        if ($hostedDomain->site->server->project_id !== user()->currentProject->id) {
            abort(404);
        }

        if (! PrimaryDomainSslFix::needsFix($hostedDomain)) {
            return back()->with('error', 'This domain cannot be fixed right now.');
        }

        if ($hostedDomain->ssl_method !== SslMethod::LETSENCRYPT) {
            return back()->with('error', 'Only Let\'s Encrypt domains can be fixed from here.');
        }

        if (! $site->webserver()->createsSiteSSLs()) {
            return $this->fixNginxManagedSsl($hostedDomain);
        }

        return $this->fixSiteManagedSsl($hostedDomain);
    }

    private function fixNginxManagedSsl(HostedDomain $hostedDomain): RedirectResponse
    {
        if ($hostedDomain->ssl !== null && $hostedDomain->ssl->certificate_path !== null) {
            try {
                app(CheckSslExpiry::class)->check($hostedDomain->ssl, notify: false);
            } catch (ValidationException $e) {
                return back()->with('error', collect($e->errors())->flatten()->first());
            }

            return back()->with('success', 'SSL expiry date refreshed.');
        }

        app(ActivateHostedDomain::class)->activate($hostedDomain);

        return back()->with('info', 'Domain SSL configuration updated.');
    }

    private function fixSiteManagedSsl(HostedDomain $hostedDomain): RedirectResponse
    {
        $site = $hostedDomain->site;
        $ssl = $hostedDomain->ssl;

        if ($ssl !== null && $ssl->certificate_path !== null && $ssl->expires_at === null) {
            try {
                app(CheckSslExpiry::class)->check($ssl, notify: false);
            } catch (ValidationException $e) {
                return back()->with('error', collect($e->errors())->flatten()->first());
            }

            return back()->with('success', 'SSL expiry date refreshed.');
        }

        if ($ssl !== null && $ssl->certificate_path !== null && $ssl->expires_at !== null) {
            return back()->with('info', 'SSL certificate already looks good.');
        }

        if ($hostedDomain->ssl_id && $ssl !== null) {
            try {
                app(RenewSiteSsl::class)->renew($site);

                return back()->with('info', 'Renewing site SSL certificate.');
            } catch (ValidationException) {
                // Fall through to a direct re-provision for this domain.
            }
        }

        if ($ssl !== null) {
            $ssl->status = SslStatus::CREATING;
            $ssl->save();
        }

        $hostedDomain->error = null;
        $hostedDomain->status = HostedDomainStatus::UPDATING;
        $hostedDomain->save();

        dispatch(new SetupHostedDomainSslJob($hostedDomain))->onQueue('ssh');

        return back()->with('info', 'Setting up SSL certificate.');
    }
}
