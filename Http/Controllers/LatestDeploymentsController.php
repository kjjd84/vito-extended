<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Site;
use App\Vito\Plugins\Kjjd84\VitoExtended\Tables\LatestDeploymentsTable;
use Inertia\Inertia;
use Inertia\Response;

class LatestDeploymentsController extends Controller
{
    public function index(): Response
    {
        $project = user()->currentProject;

        $this->authorize('view', $project);

        $siteIds = Site::query()
            ->whereHas('server', fn ($query) => $query->where('project_id', $project->id))
            ->pluck('id');

        $latestDeploymentIds = Deployment::query()
            ->selectRaw('MAX(id) as id')
            ->whereIn('site_id', $siteIds)
            ->groupBy('site_id')
            ->pluck('id');

        $deployments = Deployment::query()->whereIn('deployments.id', $latestDeploymentIds);

        return Inertia::render('plugins/kjjd84-vito-extended/latest-deployments/index', [
            'deployments' => LatestDeploymentsTable::make($deployments)->simplePaginate(),
        ]);
    }
}
