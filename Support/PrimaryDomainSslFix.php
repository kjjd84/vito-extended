<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Support;

use App\Enums\HostedDomainStatus;
use App\Enums\SslMethod;
use App\Models\HostedDomain;

class PrimaryDomainSslFix
{
    public static function needsFix(HostedDomain $hostedDomain): bool
    {
        if (! self::canAttemptFix($hostedDomain)) {
            return false;
        }

        return self::certificateIsNull($hostedDomain) || self::expiresIsNull($hostedDomain);
    }

    public static function canAttemptFix(HostedDomain $hostedDomain): bool
    {
        return in_array($hostedDomain->status, [
            HostedDomainStatus::ACTIVE,
            HostedDomainStatus::PENDING,
            HostedDomainStatus::INACTIVE,
        ], true);
    }

    public static function certificateIsNull(HostedDomain $hostedDomain): bool
    {
        if ($hostedDomain->ssl_method === SslMethod::LETSENCRYPT && ! $hostedDomain->site->webserver()->createsSiteSSLs()) {
            return false;
        }

        if ($hostedDomain->ssl_method === SslMethod::LETSENCRYPT && $hostedDomain->site->webserver()->createsSiteSSLs()) {
            return ! $hostedDomain->ssl_id || $hostedDomain->ssl === null;
        }

        return $hostedDomain->ssl === null;
    }

    public static function expiresIsNull(HostedDomain $hostedDomain): bool
    {
        return $hostedDomain->ssl?->expires_at === null;
    }
}
