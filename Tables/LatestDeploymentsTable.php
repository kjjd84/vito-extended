<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Tables;

use App\Enums\DeploymentStatus;
use App\Http\Resources\ServerLogResource;
use App\Models\Deployment;
use App\Tables\DeploymentTable;
use Forjed\InertiaTable\Column;
use Forjed\InertiaTable\Columns\DateTimeColumn;
use Forjed\InertiaTable\Columns\EnumColumn;
use Forjed\InertiaTable\Columns\TextColumn;

class LatestDeploymentsTable extends DeploymentTable
{
    protected string $defaultSort = 'sites.domain';

    protected function query(): void
    {
        parent::query();
        $this->perPage = 50;
        $this->query
            ->join('sites', 'deployments.site_id', '=', 'sites.id')
            ->select('deployments.*')
            ->orderBy('sites.domain');
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('site_domain', 'Domain')->sortable()->accessor('sites.domain'),
            Column::make('commit', 'Commit'),
            DateTimeColumn::make('created_at', 'Deployed At')->sortable()->toLocal(),
            EnumColumn::make('status', 'Status')->sortable(),
            Column::make('release', 'Release'),
            Column::make('deploy', 'Deploy'),
            Column::data('site_id'),
            Column::data('server_id', fn (Deployment $deployment) => $deployment->site->server_id),
            Column::data('site_domain', fn (Deployment $deployment) => $deployment->site->domain),
            Column::data('active'),
            Column::data('can_redeploy', fn (Deployment $deployment) => $deployment->status === DeploymentStatus::FAILED),
            Column::data('commit_data'),
            Column::data('log', fn (Deployment $deployment) => $deployment->log ? ServerLogResource::make($deployment->log) : null),
        ];
    }
}
