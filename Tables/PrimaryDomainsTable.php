<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Tables;

use App\Models\HostedDomain;
use App\Tables\HostedDomainTable;
use App\Vito\Plugins\Kjjd84\VitoExtended\Support\PrimaryDomainSslFix;
use Forjed\InertiaTable\Column;
use Forjed\InertiaTable\Columns\ActionsColumn;

class PrimaryDomainsTable extends HostedDomainTable
{
    protected string $defaultSort = 'domain';

    protected function query(): void
    {
        $this->perPage = 50;
        $this->query->with('site.server', 'ssl')->orderBy('domain');
    }

    protected function columns(): array
    {
        $columns = array_values(array_filter(
            parent::columns(),
            fn ($column) => ! $column instanceof ActionsColumn,
        ));

        $columns[] = Column::make('fix_ssl', 'Fix');
        $columns[] = Column::data('needs_ssl_fix', fn (HostedDomain $hostedDomain) => PrimaryDomainSslFix::needsFix($hostedDomain));
        $columns[] = Column::data('site_webserver', fn (HostedDomain $hostedDomain) => $hostedDomain->site->webserver()->id());
        $columns[] = Column::data('site_webserver_creates_site_ssls', fn (HostedDomain $hostedDomain) => $hostedDomain->site->webserver()->createsSiteSSLs());

        return $columns;
    }
}
